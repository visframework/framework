<?php

namespace Vis\Routing;

class RouteCollection
{
    private array $routes = [];

    public function getRoutes(): array
    {
        return $this->routes;
    }
}