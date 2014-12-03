<?php

namespace Aura\Error\Formatter;

class HtmlFormatter implements FormatterInterface {

    public function format(\Exception $e) {
        if($e instanceof \ErrorException) {
            return $this->handleErrors($e);
        }

        return $this->formatExceptions($e);
    }

    public function handleErrors(\ErrorException $e) {
        $errorString = '';

        $errorString = "%s: %s in %s on line %d<br />";

        switch($e->getSeverity()) {
            case E_ERROR:
                $severity = 'Error';
                break;

            case E_WARNING:
                $severity = 'Warning';
                break;

            case E_NOTICE:
                $severity = 'Notice';
                break;
        }

        $error = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();

        $error = sprintf($errorString, $severity, $error, $file, $line);
        return $error;
    }

    protected function formatExceptions(\Exception $e) {
        $errorString = "Fatal error: Uncaught exception '%s' with message '%s' in %s on line %d<br />%s<br />";

        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();

        $error = sprintf($errorString, $type, $message, $file, $line, $trace);
        return $error;
    }

}