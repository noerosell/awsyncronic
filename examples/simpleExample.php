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

final class simpleExample
{

    private $awsSqsKey = 'AKIAIM72P5MPB43OIIXA';

    private $awsSecret = '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';

    private $awsSqsRegion = 'us-east-1';

    private $awsSqsVersion = '2012-11-05';

    private $awsSqsurl = "https://sqs.us-east-1.amazonaws.com/142601763968/dev-bouncer4proyection_domain_events";


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

(new simpleExample())->run();
