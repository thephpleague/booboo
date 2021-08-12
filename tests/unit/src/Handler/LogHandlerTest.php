<?php

use League\BooBoo\Handler\LogHandler;
use PHPUnit\Framework\TestCase;

class LogHandlerTest extends TestCase {

    /**
     * @var LogHandler
     */
    protected $handler;

    /**
     * @var Mockery\MockInterface
     */
    protected $logger;


    protected function setUp() : void
    {
        $this->logger = new \Tests\Fakes\Psr3Handler();
        $this->handler = new LogHandler($this->logger);
    }

    public function testExceptionsAreLoggedCritical() {
        $this->handler->handle(new \Exception);
        $this->assertEquals(1, $this->logger::$critical);
    }

    public function testErrorExceptionForErrors() {
        $message = 'test message';
        $exception = new \ErrorException($message);
        $this->handler->handle(new \ErrorException($message, 0, E_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_RECOVERABLE_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_CORE_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_COMPILE_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_USER_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_PARSE));

        $this->assertEquals(6, $this->logger::$error);
    }

    public function testErrorExceptionForWarnings() {
        $message = 'test message';
        $exception = new \ErrorException($message);
        $this->handler->handle(new \ErrorException($message, 0, E_WARNING));
        $this->handler->handle(new \ErrorException($message, 0, E_USER_WARNING));
        $this->handler->handle(new \ErrorException($message, 0, E_CORE_WARNING));
        $this->handler->handle(new \ErrorException($message, 0, E_COMPILE_WARNING));

        $this->assertEquals(4, $this->logger::$warning);
    }

    public function testErrorExceptionForNoticesAndInfo() {
        $message = 'test message';
        $exception = new \ErrorException($message);
        $this->handler->handle(new \ErrorException($message, 0, E_NOTICE));
        $this->handler->handle(new \ErrorException($message, 0, E_USER_NOTICE));
        $this->handler->handle(new \ErrorException($message, 0, E_STRICT));
        $this->handler->handle(new \ErrorException($message, 0, E_DEPRECATED));
        $this->handler->handle(new \ErrorException($message, 0, E_USER_DEPRECATED));

        $this->assertEquals(2, $this->logger::$notice);
        $this->assertEquals(3, $this->logger::$info);
    }

    protected function tearDown() : void
    {
        Mockery::close();
    }
}
