<?php

use League\BooBoo\Handler\SentryHandler;
use PHPUnit\Framework\TestCase;

class Fake_Raven_Client extends Raven_Client
{
    public $loggedExceptions = [];

    public function __construct() {}

    public function captureException($exception, $data = null, $logger = null, $vars = null)
    {
        array_push($this->loggedExceptions, $exception);
    }
}

class RavenHandlerTest extends TestCase
{
    public function testRavenLogHandler()
    {

        $ravenClient = new Fake_Raven_Client();
        $exception = new \Exception;
        $handler = new SentryHandler($ravenClient);
        $handler->handle($exception);

        $this->assertSame(array_shift($ravenClient->loggedExceptions), $exception);
    }

    public function testRavenLogHandlerWithErrorException()
    {
        $ravenClient = new Fake_Raven_Client();
        $exception = new ErrorException('test', 0, E_ERROR);
        $handler = new SentryHandler($ravenClient);
        $handler->handle($exception);
        $this->assertSame(array_shift($ravenClient->loggedExceptions), $exception);
    }

    public function testErrorLevelNotListenedForIsIgnored()
    {
        $ravenClient = new Fake_Raven_Client();
        $exception = new ErrorException('test', 0, E_NOTICE);
        $errorLevel = E_ERROR | E_WARNING;
        $handler = new SentryHandler($ravenClient, $errorLevel);
        $handler->handle($exception);
        $this->assertCount(0, $ravenClient->loggedExceptions);

    }
}
