<?php

use League\BooBoo\Handler\SentryHandler;
use PHPUnit\Framework\TestCase;

class RavenHandlerTest extends TestCase
{
    public function testRavenLogHandler()
    {

        $ravenClient = new \Tests\Fakes\SentryClient();
        $exception = new \Exception;
        $handler = new SentryHandler($ravenClient);
        $handler->handle($exception);

        $this->assertSame(array_shift($ravenClient->loggedExceptions), $exception);
    }

    public function testRavenLogHandlerWithErrorException()
    {
        $ravenClient = new \Tests\Fakes\SentryClient();
        $exception = new ErrorException('test', 0, E_ERROR);
        $handler = new SentryHandler($ravenClient);
        $handler->handle($exception);
        $this->assertSame(array_shift($ravenClient->loggedExceptions), $exception);
    }

    public function testErrorLevelNotListenedForIsIgnored()
    {
        $ravenClient = new \Tests\Fakes\SentryClient();
        $exception = new ErrorException('test', 0, E_NOTICE);
        $errorLevel = E_ERROR | E_WARNING;
        $handler = new SentryHandler($ravenClient, $errorLevel);
        $handler->handle($exception);
        $this->assertCount(0, $ravenClient->loggedExceptions);

    }
}
