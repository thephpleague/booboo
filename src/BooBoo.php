<?php

namespace League\BooBoo;

use ErrorException;
use League\BooBoo\Exception\NoFormattersRegisteredException;
use League\BooBoo\Formatter\FormatterInterface;
use League\BooBoo\Handler\HandlerInterface;

class BooBoo
{
    /**
     * A constant for the error handling function.
     */
    const ERROR_HANDLER = 'errorHandler';

    /**
     * A constant for the exception handler.
     */
    const EXCEPTION_HANDLER = 'exceptionHandler';

    /**
     * A constant for the shutdown handler.
     */
    const SHUTDOWN_HANDLER = 'shutdownHandler';

    /**
     * @var array Handler stack array
     */
    protected $handlerStack = [];

    /**
     * @var array Formatter stack array
     */
    protected $formatterStack = [];

    /**
     * @var bool Whether or not we should silence all errors.
     */
    protected $silenceErrors = false;

    /**
     * @var An error page formatter, for creating pretty error pages in production
     */
    protected $errorPage;

    /**
     * @var bool If set to true, will throw all errors as exceptions (making them blocking)
     */
    protected $throwErrorsAsExceptions = false;

    /**
     * This isn't set as a default, because we can't. Set in the constructor.
     *
     * @var int
     */
    protected $fatalErrors;

    /**
     * @param array $formatters
     * @param array $handlers
     */
    public function __construct(array $formatters, array $handlers = [])
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

        $this->fatalErrors = E_ERROR | E_USER_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_PARSE;
    }

    /**
     * An error handling function for PHP. Follows the protocols laid out
     * in the documentation for defining an error handler. Variable names
     * are straight from the PHP documentation.
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     *
     * @return bool
     * @throws \ErrorException
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        // Only handle errors that match the error reporting level.
        if (!($errno & error_reporting())) { // bitwise operation
            if ($errno & $this->fatalErrors) {
                $this->terminate();
            }
            return true;
        }

        $e = new ErrorException($errstr, 0, $errno, $errfile, $errline);

        if ($this->throwErrorsAsExceptions) {
            throw $e;
        } else {
            $this->exceptionHandler($e);
        }

        // Fatal errors should be fatal
        if ($errno & $this->fatalErrors) {
            $this->terminate();
        }
    }

    protected function terminate()
    {
        exit(1);
    }

    /**
     * An exception handler, per the documentation in PHP. This function is
     * also used for the handling of errors, even when they are non-blocking.
     *
     * @param \Exception $e
     */
    public function exceptionHandler($e)
    {
        http_response_code(500);

        $this->runHandlers($e);

        if (!$this->silenceErrors) {
            $formattedResponse = $this->runFormatters($e);
            print $formattedResponse;
        }

        if ($this->silenceErrors &&
            isset($this->errorPage) &&
            !($e instanceof ErrorException)
        ) {
            ob_start();
            $response = $this->errorPage->format($e);
            ob_end_clean();
            print $response;
            return;
        }
    }

    /**
     * A function for running the error handler on a fatal error.
     */
    public function shutdownHandler()
    {
        // We can't throw exceptions in the shutdown handler.
        $this->treatErrorsAsExceptions(false);

        $error = error_get_last();
        if ($error && $error['type'] & $this->fatalErrors) {
            $this->errorHandler(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

    /**
     * Registers the error handlers, and is required to be called before the
     * error handling code is effective.
     *
     * @throws \League\BooBoo\Exception\NoFormattersRegisteredException
     */
    public function register()
    {
        if (empty($this->formatterStack)) {
            throw new NoFormattersRegisteredException(
                'No formatters were registered before attempting to register the error handler'
            );
        }

        // We want the formatters we register to handle the errors.
        ini_set('display_errors', false);

        set_error_handler([$this, self::ERROR_HANDLER]);
        set_exception_handler([$this, self::EXCEPTION_HANDLER]);
        register_shutdown_function([$this, self::SHUTDOWN_HANDLER]);
    }

    /**
     * Add a new handler to the stack.
     *
     * @param \League\BooBoo\Handler\HandlerInterface $handler
     * @return $this
     */
    public function pushHandler(HandlerInterface $handler)
    {
        $this->handlerStack[] = $handler;
        return $this;
    }

    /**
     * Remove an error handler from the bottom of the stack.
     *
     * @return \League\BooBoo\Handler\HandlerInterface|null
     */
    public function popHandler()
    {
        return array_pop($this->handlerStack);
    }

    /**
     * Get a list of available handlers.
     *
     * @return array
     */
    public function getHandlers()
    {
        return $this->handlerStack;
    }

    /**
     * CLear all the handlers.
     *
     * @return $this
     */
    public function clearHandlers()
    {
        $this->handlerStack = [];
        return $this;
    }

    /**
     * Adds a new formatter to the formatter stack.
     *
     * @param \League\BooBoo\Formatter\FormatterInterface $formatter
     * @return $this
     */
    public function pushFormatter(FormatterInterface $formatter)
    {
        $this->formatterStack[] = $formatter;
        return $this;
    }

    /**
     * Pops a formatter off the bottom of the formatter stack.
     *
     * @return \League\BooBoo\Formatter\FormatterInterface|null
     */
    public function popFormatter()
    {
        return array_pop($this->formatterStack);
    }

    /**
     * Gets all formatters currently registered.
     *
     * @return array
     */
    public function getFormatters()
    {
        return $this->formatterStack;
    }

    /**
     * Clears all formatters currently registered.
     *
     * @return $this
     */
    public function clearFormatters()
    {
        $this->formatterStack = [];
        return $this;
    }

    /**
     * Runs all the handlers registered, and returns the exception provided.
     *
     * @param \Exception $e
     * @return \Exception
     */
    protected function runHandlers($e)
    {
        /** @var \League\BooBoo\Handler\HandlerInterface $handler */
        foreach (array_reverse($this->handlerStack) as $handler) {
            $handler->handle($e);
        }

        return $e;
    }

    /**
     * @param \Exception $e
     *
     * @return mixed
     */
    protected function runFormatters($e)
    {
        if ($e instanceof ErrorException) {
            $severity = $e->getSeverity();
        } else {
            $severity = E_ERROR;
        }

        /** @var \League\BooBoo\Formatter\FormatterInterface $formatter */
        foreach (array_reverse($this->formatterStack) as $formatter) {
            if ($severity & $formatter->getErrorLimit()) {
                return $formatter->format($e);
            }
        }
    }

    /**
     * Silences all errors.
     *
     * @param bool $bool
     */
    public function silenceAllErrors($bool)
    {
        $this->silenceErrors = (bool)$bool;
    }

    /**
     * Deregisters the error handling functions, returning them to their previous state.
     *
     * @return $this
     */
    public function deregister()
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * Registers an error page for handling uncaught exceptions in production.
     *
     * @param \League\BooBoo\Formatter\FormatterInterface $errorPage
     */
    public function setErrorPageFormatter(FormatterInterface $errorPage)
    {
        $this->errorPage = $errorPage;
    }

    /**
     * Allows the user to explicitly require errors to be thrown as exceptions. This
     * makes all errors blocking, even if they are minor (e.g. E_NOTICE, E_WARNING).
     *
     * @param bool $bool
     */
    public function treatErrorsAsExceptions($bool)
    {
        $this->throwErrorsAsExceptions = (bool)$bool;
    }
}
