<?php

namespace Vis\Controller;

use LogicException;
use Vis\DependencyInjection\ContainerAwareInterface;
use Vis\DependencyInjection\ContainerAwareTrait;
use Vis\Http\Response;

abstract class AbstractController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function render(string $name, array $context = []): Response
    {
        if (!$this->container->has('twig')) {
            throw new LogicException('Service "twig" is not available. Try running "composer require twig/twig".');
        }

        return new Response($this->container->get('twig')->render($name, $context));
    }
}