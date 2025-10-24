<?php

namespace Vis\Core;

use ArrayAccess;

class Collection implements ArrayAccess
{
    public function __construct(private array $parameters = [])
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function has(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function get(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function set(string $name, mixed $value): void
    {
        $this->parameters[$name] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}