<?php

namespace awsyncronic\lib;

use Amp\Artax\DefaultClient;
use Amp\Artax\Request;
use Amp\Loop;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

const HTTP_OK = 200;

final class AwsHttpHandler
{
    /**
     * @var \Amp\Artax\Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new DefaultClient();
    }

    /**
     * Adds a new task on amphp event Loop. This task will do the request to aws and resolve or reject the guzzle
     * promise which is waiting to be fullfilled.
     *
     * @param RequestInterface $request
     * @param array $options
     *
     * @return Promise\Promise
     */
    public function __invoke(RequestInterface $request, array $options = [])
    {
        $guzzlePromise = new Promise\Promise();

        $internalRequest = new Request($request->getUri(), $request->getMethod());
        $internalRequest = $internalRequest->withHeaders($request->getHeaders());
        $internalRequest = $internalRequest->withBody($request->getBody()->getContents());

        Loop::defer(function () use ($internalRequest, $options, $guzzlePromise) {
            try {
                $promise = $this->client->request($internalRequest, $options);
                /** @var \Amp\Artax\Response $response */
                $response = yield $promise;
                $body     = yield $response->getBody();

                $guzzleResponse = new Response($response->getStatus(), $response->getHeaders(), $body);
                if ($response->getStatus() !== HTTP_OK) {
                    $guzzlePromise->reject($response->getReason());
                } else {
                    $guzzlePromise->resolve($guzzleResponse);
                }
            } catch (\Exception $e) {
                $guzzlePromise->reject($e);
            }
        });

        return $guzzlePromise;
    }
}
