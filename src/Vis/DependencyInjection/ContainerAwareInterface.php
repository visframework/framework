<?php

namespace Vis\DependencyInjection;

interface ContainerAwareInterface
{
    public function setContainer(Container $container): void;
}