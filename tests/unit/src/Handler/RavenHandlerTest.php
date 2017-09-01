<?php

use League\BooBoo\Handler\SentryHandler;

class RavenHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testRavenLogHandler()
    {

        $ravenClient = Mockery::mock('Raven_Client');
        $ravenClient->shouldReceive('captureException')->once()->with(Mockery::type('Exception'));
        $ravenClient->makePartial();

        $handler = new SentryHandler($ravenClient);
        $handler->handle(new Exception);
    }

    public function testRavenLogHandlerWithErrorException()
    {

        $ravenClient = Mockery::mock('Raven_Client');
        $ravenClient->shouldReceive('captureException')->once()->with(Mockery::type('Exception'));
        $ravenClient->makePartial();

        $handler = new SentryHandler($ravenClient);
        $handler->handle(new ErrorException('test', 0, E_ERROR));
    }

    public function testErrorLevelNotListenedForIsIgnored()
    {
        $ravenClient = Mockery::mock('Raven_Client');
        $ravenClient->shouldNotHaveReceived('captureException');
        $ravenClient->makePartial();

        $errorLevel = E_ERROR | E_WARNING;

        $handler = new SentryHandler($ravenClient, $errorLevel);
        $handler->handle(new ErrorException('test', 0, E_NOTICE));

    }

    protected function tearDown()
    {
        Mockery::close();
    }
}
