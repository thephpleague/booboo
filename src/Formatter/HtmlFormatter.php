<?php

namespace Savage\ShitHappens\Formatter;

class HtmlFormatter extends AbstractFormatter
{

    public function format(\Exception $e)
    {
        if ($e instanceof \ErrorException) {
            return $this->handleErrors($e);
        }

        return $this->formatExceptions($e);
    }

    public function handleErrors(\ErrorException $e)
    {
        $errorString = '';

        $errorString = "<br /><strong>%s</strong>: %s in <strong>%s</strong> on line <strong>%d</strong><br />";

        $severity = $this->determineSeverityTextValue($e->getSeverity());

        $error = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();

        $error = sprintf($errorString, $severity, $error, $file, $line);
        return $error;
    }

    protected function formatExceptions(\Exception $e)
    {
        $errorString = "<br /><strong>Fatal error:</strong> Uncaught exception '%s' with ";
        $errorString .= "message '%s' in %s on line %d<br />%s<br />";

        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();

        $error = sprintf($errorString, $type, $message, $file, $line, $trace);
        return $error;
    }
}
