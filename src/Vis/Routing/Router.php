<?php

namespace Vis\Routing;

use Closure;
use InvalidArgumentException;
use Vis\DependencyInjection\ContainerAwareInterface;
use Vis\DependencyInjection\ContainerAwareTrait;
use Vis\Http\HttpNotFoundException;
use Vis\Http\Request;
use Vis\Http\Response;

class Router implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var array<string, Route>
     */
    private array $routes = [];

    public function add(string $name, string $path, Closure|array|string $callable, array $methods = []): Route
    {
        if (is_string($callable)) {
            $callable = explode('@', $callable);
        }

        return $this->routes[$name] = new Route($name, $path, $callable, $methods);
    }

    /**
     * @throws HttpNotFoundException
     */
    public function matchRequest(Request $request): Response
    {
        if (preg_match($this->getPattern(), $request->server->get('REQUEST_URI'), $matches)) {
            /** @var string[] $matches */
            $matches = array_keys(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));

            return $this->dispatch($request, $this->routes[$matches[0]]);
        }

        throw new HttpNotFoundException('Page not found');
    }

    private function getPattern(): string
    {
        $groups = array_map(
            fn (Route $route) => sprintf('(?<%s>%s)', $route->getName(), preg_quote($route->getPath(), '/')),
            $this->routes
        );

        return '/^' . implode('|', $groups) . '$/';
    }

    private function dispatch(Request $request, Route $route): Response
    {
        $callable = $route->getCallable();

        if (!$callable instanceof Closure) {
            $callable[0] = new $callable[0];

            $this->container->set('controller', $callable[0]);
        }

        $response = call_user_func($callable, $request);

        if (!$response instanceof Response) {
            throw new InvalidArgumentException('Controller must return an instance of Response.');
        }

        return $response;
    }
}