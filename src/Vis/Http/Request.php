<?php

namespace Vis\Http;

use Vis\Core\Collection;

class Request
{
    const string METHOD_GET = 'GET';

    const int STATUS_NOT_FOUND = 404;

    public readonly Collection $request;
    public readonly Collection $server;

    public function __construct(array $request, array $server)
    {
        $this->request = new Collection($request);
        $this->server  = new Collection($server);
    }

    public static function capture(): Request
    {
        return new Request($_REQUEST, $_SERVER);
    }
}