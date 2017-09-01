<?php

namespace League\BooBoo\Handler;

use ErrorException;
use Raven_Client;

class SentryHandler implements HandlerInterface
{
    /**
     * @var \Raven_Client
     */
    protected $client;

    public function __construct(Raven_Client $client, $minimumLogLevel = E_ALL)
    {
        $this->client = $client;
        $this->minimumLogLevel = $minimumLogLevel;
    }

    public function handle($e)
    {
        if ($e instanceof ErrorException) {
            $level = $e->getSeverity();
        } else {
            $level = E_ERROR;
        }

        if ($this->minimumLogLevel & $level) {
            $this->client->captureException($e);
        }
    }
}
