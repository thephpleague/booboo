<?php

use League\BooBoo\Handler\RavenHandler;

class RavenHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testRavenLogHandler()
    {

        $ravenClient = Mockery::mock('Raven_Client');
        $ravenClient->shouldReceive('captureException')->once()->with(Mockery::type('Exception'));

        $handler = new RavenHandler($ravenClient);
        $handler->handle(new Exception);
    }

    public function testRavenLogHandlerWithErrorException()
    {

        $ravenClient = Mockery::mock('Raven_Client');
        $ravenClient->shouldReceive('captureException')->once()->with(Mockery::type('Exception'));

        $handler = new RavenHandler($ravenClient);
        $handler->handle(new ErrorException('test', 0, E_ERROR));
    }

    public function testErrorLevelNotListenedForIsIgnored()
    {
        $ravenClient = Mockery::mock('Raven_Client');
        $ravenClient->shouldNotHaveReceived('captureException');

        $errorLevel = E_ERROR | E_WARNING;

        $handler = new RavenHandler($ravenClient, $errorLevel);
        $handler->handle(new ErrorException('test', 0, E_NOTICE));

    }
}
