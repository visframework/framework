<?php

namespace Vis\Core;

use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Vis\DependencyInjection\Container;
use Vis\DependencyInjection\ContainerAwareInterface;
use Vis\Http\Request;
use Vis\Http\Response;
use Vis\Routing\Router;

abstract class AbstractKernel
{
    private Container $container;

    private bool $booted = false;

    public function __construct(private readonly string $publicDir, private readonly string $environment)
    {
        $this->boot();
    }

    private function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $projectDir  = realpath($this->publicDir . '/..');
        $cacheDir    = $projectDir . '/cache';
        $templateDir = $projectDir . '/templates';

        $container = new Container();
        $container->setParameters([
            'kernel.public_dir'  => $this->publicDir,
            'kernel.project_dir' => $projectDir,
            'kernel.cache_dir'   => $cacheDir,
        ]);

        $container->registerForAutoConfiguration(
            ContainerAwareInterface::class,
            static fn (ContainerAwareInterface $service) => $service->setContainer($container)
        );

        $loader = new FilesystemLoader($templateDir);
        $container->set('twig.loader.filesystem', $loader);
        $container->set('twig', new Environment($loader, ['cache' => $cacheDir]));

        $router = new Router();
        $container->set('router', $router);

        $this->configureRoutes($router);

        $this->container = $container;
        $this->booted    = true;
    }

    protected function configureRoutes(Router $router): void
    {
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function handleRequest(Request $request): void
    {
        $this->container->set('request', $request);

        try {
            $response = $this->container->get('router')->matchRequest($request);
        } catch (Throwable $exception) {
            $response = $this->handleThrowable($exception);
        }

        $response->send();
    }

    protected function handleThrowable(Throwable $exception): Response
    {
        return new Response($exception->getMessage(), $exception->getCode());
    }
}