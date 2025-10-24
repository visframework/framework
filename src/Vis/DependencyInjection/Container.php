<?php

namespace Vis\DependencyInjection;

use Closure;
use InvalidArgumentException;
use Vis\Core\Collection;

class Container
{
    private readonly Collection $parameters;

    private array $services            = [];
    private array $autoConfigCallbacks = [];

    public function __construct()
    {
        $this->parameters = new Collection();
    }

    public function setParameter(string $name, mixed $value): void
    {
        $this->parameters->set($name, $value);
    }

    public function set(string $name, object $value): void
    {
        foreach (class_implements($value) as $interface) {
            if (!isset($this->autoConfigCallbacks[$interface])) {
                continue;
            }

            foreach ($this->autoConfigCallbacks[$interface] as $callback) {
                call_user_func($callback, $value);
            }
        }

        $this->services[$name] = $value;
    }

    public function setParameters(array $parameters): void
    {
        array_map([$this, 'setParameter'], array_keys($parameters), $parameters);
    }

    public function getParameter(string $name): mixed
    {
        return $this->parameters->get($name);
    }

    public function get(string $name): mixed
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException(sprintf('The service "%s" does not exist.', $name));
        }

        if (($service = ($this->services[$name] ?? null)) instanceof Closure) {
            $this->set($name, $service = call_user_func($service, $this));
        }

        return $service;
    }

    public function has(string $name): bool
    {
        return isset($this->services[$name]);
    }

    public function registerForAutoConfiguration(string $interface, Closure $callback): void
    {
        $callbacks   = $this->autoConfigCallbacks[$interface] ?? [];
        $callbacks[] = $callback;

        $this->autoConfigCallbacks[$interface] = $callbacks;
    }
}