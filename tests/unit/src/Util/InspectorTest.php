<?php

class InspectorText extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Savage\BooBoo\Util\Inspector
     */
    protected $inspector;

    /**
     * @var ErrorException
     */
    protected $exception;

    public function setUp()
    {
        $this->exception = new ErrorException('test message');
        $this->inspector = new \Savage\BooBoo\Util\Inspector($this->exception);
    }

    public function testGetters()
    {
        $inspector = $this->inspector;
        $this->assertInstanceOf('ErrorException', $inspector->getException());
        $this->assertEquals('ErrorException', $inspector->getExceptionName());
        $this->assertEquals('test message', $inspector->getExceptionMessage());
        $this->assertFalse($inspector->hasPreviousException());
    }

    public function testGetFrames()
    {
        $frames = $this->inspector->getFrames();
        $this->assertEquals(count($this->exception->getTrace()), count($frames));
    }
}