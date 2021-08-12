<?php

namespace League\BooBoo\Handler;

use ErrorException;
use Sentry\ClientInterface;

class SentryHandler implements HandlerInterface
{
    /**
     * @var \Client
     */
    protected $client;

    public function __construct(ClientInterface $client, $minimumLogLevel = E_ALL)
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
