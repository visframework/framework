<?php

namespace Vis\DependencyInjection;

trait ContainerAwareTrait
{
    protected ?Container $container = null;

    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }
}