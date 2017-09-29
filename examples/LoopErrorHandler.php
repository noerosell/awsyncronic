<?php

namespace awsyncronic\examples;

final class LoopErrorHandler
{
    public function __invoke($exception)
    {
        print_r('Error: '.$exception);
    }
}