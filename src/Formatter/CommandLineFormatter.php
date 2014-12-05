<?php

namespace Savage\BooBoo\Formatter;

class CommandLineFormatter extends AbstractFormatter
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
        $errorString = "\n ------------------------\n  AN ERROR HAS OCCURRED \n ------------------------\n";
        $errorString .= " %s: %s in %s on line %d\n";

        $severity = $this->determineSeverityTextValue($e->getSeverity());

        $error = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();

        $error = sprintf($errorString, $severity, $error, $file, $line);
        return $error;
    }

    protected function formatExceptions(\Exception $e)
    {

        $errorString = "\n ---------------------------\n  AN EXCEPTION HAS OCCURRED \n ---------------------------\n";
        $errorString .= " Fatal error: Uncaught exception '%s' with message '%s' in %s on line %d\n\n";
        $errorString .= "Stack Trace:\n%s\n";

        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();

        $error = sprintf($errorString, $type, $message, $file, $line, $trace);
        return $error;
    }
}
