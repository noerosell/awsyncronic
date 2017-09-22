<?php

namespace awsyncronic\lib;

use Amp\Loop;
use Aws\CommandInterface;
use Aws\Middleware;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Promise\queue;

final class AwsLoopMiddleware
{
    /**
     * add a new task in the Amphp event Loop which starts the guzzle internal queue
     *
     * @return callable
     */
    public static function runGuzzleQueue()
    {
        return Middleware::tap(
            function (CommandInterface $cmd, RequestInterface $req = null) {
                Loop::defer(function () {
                    queue()->run();
                });
            });
    }
}