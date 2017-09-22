<?php

namespace asyncSQS\lib;

use Amp\Loop;
use Aws\Sqs\SqsClient;
use awsyncronic\lib\AwsHttpHandler;
use awsyncronic\lib\AwsLoopMiddleware;
use GuzzleHttp\HandlerStack;

/**
 * have you executed `composer dumpautload` ? if not do it now
 */
$loader = require __DIR__ . '/../vendor/autoload.php';

final class main
{

    private $awsSqsKey = '';

    private $awsSecret = '';

    private $awsSqsRegion = '';

    private $awsSqsVersion = '';

    private $awsSqsurl = "";

    /** @var  \Aws\Sqs\SqsClient */
    private $sqsQueue;

    public function run()
    {
        $this->startSqs();
        Loop::run(function () {
            /**
             * call to aws client as we are used to, no wait() needed !!, it will resolve at some point in the future.
             */
            $guzzlePromise = $this->sqsQueue->receiveMessageAsync(['QueueUrl' => $this->awsSqsurl]);
            $guzzlePromise->then(function ($result) {
                print_r($result);
            });
        });
    }

    private function startSqs()
    {
        /**
         * Create your preferred aws client setting on it the AwsHttpHandler
         */
        $this->sqsQueue = new SqsClient(
            [
                'http_handler' => HandlerStack::create(new AwsHttpHandler()),
                'region'       => $this->awsSqsRegion,
                'version'      => $this->awsSqsVersion,
                'credentials'  => [
                    'key'    => $this->awsSqsKey,
                    'secret' => $this->awsSecret,
                ],
            ]
        );

        /**
         * add AwsLoopMiddleware
         */
        $this->sqsQueue->getHandlerList()->appendBuild(AwsLoopMiddleware::runGuzzleQueue());
    }
}

(new main())->run();
