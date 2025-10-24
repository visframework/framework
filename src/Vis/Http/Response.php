<?php

namespace Vis\Http;

class Response
{
    private array $headers = [];

    public function __construct(private string $body = '', private int $status = 200)
    {
    }

    public function send(): void
    {
        if (headers_sent()) {
            return;
        }

        array_walk(
            $this->headers,
            static fn (string $key, string $value) => header(sprintf('%s: %s', $key, $value))
        );

        http_response_code($this->status);

        echo $this->body;
    }
}