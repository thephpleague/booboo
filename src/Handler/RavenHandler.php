<?php

namespace League\BooBoo\Handler;

use \Exception;
use League\BooBoo\Handler\HandlerInterface;

class RavenHandler implements HandlerInterface
{
    protected $client;

    public function __construct(\Raven_Client $client, $minimumLogLevel = E_ALL)
    {
        $this->client = $client;
        $this->minimumLogLevel = $minimumLogLevel;
    }

    public function handle(Exception $e)
    {
        if ($e instanceof \ErrorException) {
            $level = $e->getSeverity();
        } else {
            $level = E_ERROR;
        }

        if ($this->minimumLogLevel & $level) {
            $this->client->getIdent($this->client->captureException($e));
        }
    }
}
