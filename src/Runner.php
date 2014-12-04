<?php

namespace Savage\ShitHappens;

use Savage\ShitHappens\Formatter;
use Savage\ShitHappens\Handler;

class Runner
{

    const ERROR_HANDLER = 'errorHandler';
    const EXCEPTION_HANDLER = 'exceptionHandler';

    protected $handlerStack = array();
    protected $formatterStack = array();
    protected $silenceErrors = false;

    protected $errorPage;
    protected $throwErrorsAsExceptions = false;

    public function __construct(array $formatters = [], array $handlers = [])
    {
        // Let's honor the INI settings.
        if (ini_get('display_errors') == false) {
            $this->silenceAllErrors(true);
        }

        foreach ($formatters as $formatter) {
            $this->pushFormatter($formatter);
        }

        foreach ($handlers as $handler) {
            $this->pushHandler($handler);
        }
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $fatalErrors = E_ERROR | E_USER_ERROR | E_COMPILE_ERROR | E_CORE_ERROR;

        // Only handle errors that match the error reporting level.
        if (!($errno & error_reporting())) { // bitwise operation
            if ($errno & $fatalErrors) {
                exit(1);
            }
            return true;
        }

        $e = new \ErrorException($errstr, 0, $errno, $errfile, $errline);

        if ($this->throwErrorsAsExceptions) {
            throw $e;
        } else {
            $this->exceptionHandler($e);
        }

        // Fatal errors should be fatal
        if ($errno & $fatalErrors) {
            exit(1);
        }
    }

    public function exceptionHandler(\Exception $e)
    {
        $this->runHandlers($e);

        if (!$this->silenceErrors) {
            $formattedResponse = $this->runFormatters($e);
            print $formattedResponse;
        }

        if ($this->silenceErrors &&
            isset($this->errorPage) &&
            !($e instanceof \ErrorException)
        ) {
            ob_start();
            $response = $this->errorPage->format($e);
            ob_end_clean();
            http_response_code(500);
            print $response;
            exit;
        }
    }

    public function register()
    {
        if (empty($this->formatterStack)) {
            throw new Exception\NoFormattersRegisteredException(
                'No formatters were registered before attempting to register the error handler'
            );
        }

        set_error_handler(array($this, self::ERROR_HANDLER));
        set_exception_handler(array($this, self::EXCEPTION_HANDLER));
    }

    public function pushHandler(Handler\HandlerInterface $handler)
    {
        $this->handlerStack[] = $handler;
        return $this;
    }

    public function popHandler()
    {
        return array_pop($this->handlerStack);
    }

    public function getHandlers()
    {
        return $this->handlerStack;
    }

    public function clearHandlers()
    {
        $this->handlerStack = array();
        return $this;
    }

    public function pushFormatter(Formatter\FormatterInterface $formatter)
    {
        $this->formatterStack[] = $formatter;
        return $this;
    }

    public function popFormatter()
    {
        return array_pop($this->formatterStack);
    }

    public function getFormatters()
    {
        return $this->formatterStack;
    }

    public function clearFormatters()
    {
        $this->formatterStack = array();
        return $this;
    }

    protected function runHandlers(\Exception $e)
    {

        foreach (array_reverse($this->handlerStack) as $handler) {
            $handler->handle($e);
        }

        return $e;
    }

    protected function runFormatters(\Exception $e)
    {
        $string = '';

        if ($e instanceof \ErrorException) {
            $severity = $e->getSeverity();
        } else {
            $severity = E_ERROR;
        }

        foreach (array_reverse($this->formatterStack) as $formatter) {
            if ($severity & $formatter->getErrorLimit()) {
                return $formatter->format($e);
            }
        }
    }

    public function silenceAllErrors($bool)
    {
        $this->silenceErrors = (bool)$bool;
    }

    public function deregister()
    {
        restore_error_handler();
        restore_exception_handler();
        return $this;
    }

    public function setErrorPageFormatter(Formatter\FormatterInterface $errorPage)
    {
        $this->errorPage = $errorPage;
    }

    public function treatErrorsAsExceptions($bool)
    {
        $this->throwErrorsAsExceptions = (bool)$bool;
    }


}
