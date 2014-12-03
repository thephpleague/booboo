<?php

namespace Aura\Error;

class ErrorHandler {

    const ERROR_HANDLER = 'errorHandler';
    const EXCEPTION_HANDLER = 'exceptionHandler';

    protected $handlerStack = array();
    protected $formatterStack = array();
    protected $silenceErrors = false;

    public function errorHandler($errno, $errstr, $errfile, $errline) {
        $e = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        $this->exceptionHandler($e);
    }

    public function exceptionHandler(\Exception $e) {
        $this->runHandlers($e);

        if(!$this->silenceErrors) {
            $formattedResponse = $this->runFormatters($e);
            print $formattedResponse;
        }
    }

    public function register() {
        set_error_handler(array($this, self::ERROR_HANDLER));
        set_exception_handler(array($this, self::EXCEPTION_HANDLER));
    }

    public function pushHandler(HandlerInterface $handler) {
        $this->handlerStack[] = $handler;
        return $this;
    }

    public function popHandler() {
        return array_pop($this->handlerStack);
    }

    public function getHandlers() {
        return $this->handlerStack;
    }

    public function clearHandlers() {
        $this->handlerStack = array();
        return $this;
    }

    public function pushFormatter(FormatterInterface $formatter) {
        $this->formatterStack[] = $formatter;
        return $this;
    }

    public function popFormatter() {
        return array_pop($this->formatterStack);
    }

    public function getFormatters() {
        return $this->formatterStack;
    }

    public function clearFormatters() {
        $this->formatterStack = array();
        return $this;
    }

    protected function runHandlers(\Exception $e) {

        foreach(array_reverse($this->handlerStack) as $handler) {
            $handler->handle($e);
        }

        return $e;
    }

    protected function runFormatters(\Exception $e) {
        $string = '';
        foreach(array_reverse($this->formatterStack) as $formatter) {
            $string .= $formatter->format($e);
        }

        return $string;
    }

    public function silenceAllErrors($bool) {
        $this->silenceErrors = (bool)$bool;
    }


}