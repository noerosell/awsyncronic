<?php

namespace awsyncronic\examples;


use Amp\Loop;
use awsyncronic\examples\SqsCustomClient;

/**
 * have you executed `composer dumpautload` ? if not do it now
 */
$loader = require __DIR__ . '/../vendor/autoload.php';

final class usingCustomClientExample
{

    private $awsSqsKey = 'AKIAIM72P5MPB43OIIXA';

    private $awsSecret = '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';

    private $awsSqsRegion = 'us-east-1';

    private $awsSqsVersion = '2012-11-05';

    private $awsSqsurl = "https://sqs.us-east-1.amazonaws.com/142601763968/dev-bouncer4proyection_domain_event";


    /** @var  SqsCustomClient */
    private $sqsQueue;

    public function run()
    {
        Loop::setErrorHandler(new LoopErrorHandler());
        Loop::run(function(){
            try {
                Loop::repeat(0,function(){
                $this->sqsQueue = new SqsCustomClient($this->awsSqsRegion, $this->awsSqsVersion, $this->awsSqsKey, $this->awsSecret);
                $promise = $this->sqsQueue->getMessage($this->awsSqsurl);
                $message = yield $promise;
                print_r($message);
                });
            } catch (\Exception $e) {
                throw $e;
            }
        });
    }


}

(new main())->run();
