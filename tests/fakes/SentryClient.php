<?php

namespace Tests\Fakes;

class SentryClient implements \Sentry\ClientInterface
{
    public $loggedExceptions = [];

    public function getOptions(): \Sentry\Options {}

    public function captureMessage(string $message, ?\Sentry\Severity $level = null, ?\Sentry\State\Scope $scope = null): ?\Sentry\EventId {}

    public function captureException(\Throwable $exception, ?\Sentry\State\Scope $scope = null): ?\Sentry\EventId
    {
        $this->loggedExceptions[] = $exception;
        return null;
    }

    public function captureLastError(?\Sentry\State\Scope $scope = null): ?\Sentry\EventId
    {
    }

    public function captureEvent(\Sentry\Event $event, ?\Sentry\EventHint $hint = null, ?\Sentry\State\Scope $scope = null): ?\Sentry\EventId
    {
    }

    public function getIntegration(string $className): ?\Sentry\Integration\IntegrationInterface
    {
    }

    public function flush(?int $timeout = null): \GuzzleHttp\Promise\PromiseInterface
    {
    }
}
