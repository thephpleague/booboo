<?php

use Savage\BooBoo\Handler\LogHandler;

class LogHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * @var LogHandler
     */
    protected $handler;

    /**
     * @var Mockery\MockInterface
     */
    protected $logger;


    protected function setUp() {
        $this->logger = Mockery::mock('Psr\Log\LoggerInterface');
        $this->handler = new LogHandler($this->logger);
    }

    public function testExceptionsAreLoggedCritical() {
        $this->logger->shouldReceive('critical')->once();

        $this->handler->handle(new \Exception);

        try {
            $this->logger->mockery_verify();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testErrorExceptionForErrors() {
        $message = 'test message';
        $exception = new \ErrorException($message);
        $this->logger->shouldReceive('error')->times(6);

        $this->handler->handle(new \ErrorException($message, 0, E_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_RECOVERABLE_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_CORE_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_COMPILE_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_USER_ERROR));
        $this->handler->handle(new \ErrorException($message, 0, E_PARSE));

        try {
            $this->logger->mockery_verify();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testErrorExceptionForWarnings() {
        $message = 'test message';
        $exception = new \ErrorException($message);
        $this->logger->shouldReceive('warning')->times(4);

        $this->handler->handle(new \ErrorException($message, 0, E_WARNING));
        $this->handler->handle(new \ErrorException($message, 0, E_USER_WARNING));
        $this->handler->handle(new \ErrorException($message, 0, E_CORE_WARNING));
        $this->handler->handle(new \ErrorException($message, 0, E_COMPILE_WARNING));


        try {
            $this->logger->mockery_verify();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testErrorExceptionForNoticesAndInfo() {
        $message = 'test message';
        $exception = new \ErrorException($message);
        $this->logger->shouldReceive('notice')->times(2);
        $this->logger->shouldReceive('info')->times(3);

        $this->handler->handle(new \ErrorException($message, 0, E_NOTICE));
        $this->handler->handle(new \ErrorException($message, 0, E_USER_NOTICE));
        $this->handler->handle(new \ErrorException($message, 0, E_STRICT));
        $this->handler->handle(new \ErrorException($message, 0, E_DEPRECATED));
        $this->handler->handle(new \ErrorException($message, 0, E_USER_DEPRECATED));



        try {
            $this->logger->mockery_verify();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}