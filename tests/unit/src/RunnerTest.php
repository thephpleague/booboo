<?php

use Savage\ShitHappens\Runner;
use Savage\ShitHappens\Formatter;
use Savage\ShitHappens\Handler;

class RunnerTest extends PHPUnit_Framework_TestCase {

    protected $runner;
    protected $formatter;
    protected $handler;

    protected function setUp() {
        $this->runner = new Runner;

        $this->formatter = $this->getMockForAbstractClass(Formatter\AbstractFormatter::class);
        $this->handler = $this->getMockForAbstractClass(Handler\HandlerInterface::class);
        $this->runner->pushFormatter($this->formatter);
    }

    /**
     * @expectedException Savage\ShitHappens\Exception\NoFormattersRegisteredException
     */
    public function testNoFormatterRaisesException() {
        $runner = new Runner;
        $runner->register();
    }

    #public function test


}