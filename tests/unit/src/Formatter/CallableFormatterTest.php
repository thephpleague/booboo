<?php

class TestCallableForFormatter
{
    public function __invoke()
    {
        return 'abcd';
    }
}

class CallableFormatterTest extends PHPUnit_Framework_TestCase
{
    public function testCallableIsCalled()
    {
        $callable = new \League\BooBoo\Formatter\CallableFormatter(new TestCallableForFormatter);
        $this->assertEquals('abcd', $callable->format(new Exception));
    }
}