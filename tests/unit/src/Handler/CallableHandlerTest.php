<?php

class TestCallableForHandler
{
    public function __invoke()
    {
        return 'abcd';
    }
}

class CallableHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testCallableIsCalled()
    {
        $callable = new \League\BooBoo\Handler\CallableHandler(new TestCallableForHandler);
        $this->assertEquals('abcd', $callable->handle(new Exception));
    }
}