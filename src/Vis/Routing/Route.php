<?php

namespace Vis\Routing;

use Closure;
use Vis\Http\Request;

class Route
{
    private string $path;
    private array  $methods = [Request::METHOD_GET];

    public function __construct(
        private string $name,
        string $path,
        private Closure|array $callable,
        array|string $methods = []
    ) {
        $this->setPath($path);
        $this->setMethods($methods);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = '/' . trim($path, '/');

        return $this;
    }

    public function getCallable(): Closure|array
    {
        return $this->callable;
    }

    public function setCallable(Closure|array $callable): self
    {
        $this->callable = $callable;

        return $this;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function setMethods(array|string $methods): self
    {
        $this->methods = array_map('strtoupper', (array)$methods);

        return $this;
    }
}