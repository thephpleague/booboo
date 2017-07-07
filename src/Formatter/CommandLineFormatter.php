<?php

namespace League\BooBoo\Formatter;

class CommandLineFormatter extends AbstractFormatter
{
    public function format($e)
    {
        if ($e instanceof \ErrorException) {
            return $this->handleErrors($e);
        }

        return $this->formatExceptions($e);
    }

    public function handleErrors(\ErrorException $e)
    {
        $errorString = "%s%s in %s on line %d\n";

        $severity = $this->determineSeverityTextValue($e->getSeverity());

        // Let's calculate the length of the box, and set the box border.
        $dashes = "\n+" . str_repeat('-', strlen($severity) + 2) . "+\n";
        $severity = $dashes . '| ' . strtoupper($severity) . " |" . $dashes;

        // Okay, now let's prep the message components.
        $error = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();

        $error = sprintf($errorString, $severity, $error, $file, $line);
        return $error;
    }

    protected function formatExceptions($e)
    {
        $errorString = "+---------------------+\n| UNHANDLED EXCEPTION |\n+---------------------+\n";
        $errorString .= "Fatal error: Uncaught exception '%s' %s with message '%s' in %s on line %d\n\n";
        $errorString .= "Stack Trace:\n%s\n";

        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();
        $code = null;
        if ($e->getCode()) {
            $code = '(' . $e->getCode() . ')';
        }

        $error = sprintf($errorString, $type, $code, $message, $file, $line, $trace);
        return $error;
    }
}
