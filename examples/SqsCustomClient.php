<?php

namespace awsyncronic\examples;

use Amp\Deferred;

use Aws\Sqs\SqsClient;
use awsyncronic\lib\AwsHttpHandler;
use awsyncronic\lib\AwsLoopMiddleware;
use GuzzleHttp\HandlerStack;
use function Amp\call;

final class SqsCustomClient
{
    /** @var  SqsClient */
    private $sqsQueue;

    /**
     * SqsCustomClient constructor.
     *
     * @param $awsSqsRegion
     * @param $awsSqsVersion
     * @param null $awsSqsKey
     * @param null $awsSecret
     */
    public function __construct($awsSqsRegion, $awsSqsVersion, $awsSqsKey = null, $awsSecret = null)
    {
        $options = [
            'http_handler' => HandlerStack::create(new AwsHttpHandler()),
            'region'       => $awsSqsRegion,
            'version'      => $awsSqsVersion,
            'retries'      => 0
        ];

        if ($awsSqsKey !== null && $awsSecret !== null) {
            $credentials = [
                'credentials' => [
                    'key'    => $awsSqsKey,
                    'secret' => $awsSecret,
                ],
            ];
            $options     = array_merge($options, $credentials);
        }

        $this->sqsQueue = new SqsClient($options);
        $this->sqsQueue->getHandlerList()->appendBuild(AwsLoopMiddleware::runGuzzleQueue());
    }

    /**
     * @param $awsSqsUrl
     *
     * @return \Amp\Promise
     */
    public function getMessage($awsSqsUrl)
    {
        return call(function () use ($awsSqsUrl) {
            $deferred = new Deferred();
            $promise  = $deferred->promise();

            $this->sqsQueue->receiveMessageAsync(['QueueUrl' => $awsSqsUrl, 'WaitTimeSeconds' => 5])
                ->then(
                    function ($result) use ($deferred) {
                        $deferred->resolve($result);
                    },
                    function ($reason) use ($deferred) {
                        $exception = new \Exception($reason);
                        $deferred->fail($exception);
                        throw $exception;
                    });

            $result = yield $promise;

            return $result;
        });
    }
}