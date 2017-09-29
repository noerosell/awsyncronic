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

    private $awsSqsKey = '';

    private $awsSecret = '';

    private $awsSqsRegion = '';

    private $awsSqsVersion = '';

    private $awsSqsurl = "";


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
