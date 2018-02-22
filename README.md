# awsyncronic
Awsyncronic is a set of utils, an http handler and a Middleware step for guzzle, which makes possible a no-blocking i/o 
comunication. Built on top of amphp/amp project which is who makes the magic. amphp/artax is the http client what 
asyncronic use to make request. Awsyncronic coordinates the amp Loop with the Guzzle internal queue.

With Guzzle you can make async requests, but while the requests are completing, the process is waiting until all these 
request are finished. This is great, but what if while the async requests are completing, the process is doing other
things, like preparing the next set of requests to send ? This is the intention of this little project.

### aws-sdk-php and awsyncronic
You can use awsyncronic to comunicate with any kind of http infrastructure, so why not try to comunicate with Aws 
infrastructure through his oficial sdk whith this awesome characteristics, async and i/o no-blocking.

### Requirements

PHP >= 7.0

Amphp async non-blocking io framework >= 2.0



#### Installation
`composer require noerosell/awsyncronic`

#### Quick start 
See examples directory.
