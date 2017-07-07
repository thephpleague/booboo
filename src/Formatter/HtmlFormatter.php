<?php

namespace League\BooBoo\Formatter;

use League\BooBoo\Util;

class HtmlFormatter extends AbstractFormatter
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
        $errorString = '';

        $errorString = "<br /><strong>%s</strong>: %s in <strong>%s</strong> on line <strong>%d</strong><br />";

        $severity = $this->determineSeverityTextValue($e->getSeverity());

        $error = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();

        $error = sprintf($errorString, $severity, $error, $file, $line);
        return $error;
    }

    protected function formatExceptions($e)
    {
        $inspector = new Util\Inspector($e);

        $errorString = "<br /><strong>Fatal error:</strong> Uncaught exception '%s'";

        if ($e->getCode()) {
            $errorString .= " (" . $e->getCode() . ") ";
        }

        $errorString .= " with message '%s' in %s on line %d<br />%s<br />";

        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $traceString = '#%d: %s %s<br />';
        $trace = '';

        foreach ($inspector->getFrames() as $k => $frame) {
            list($function, $fileline) = $this->processFrame($frame);
            $trace .= sprintf($traceString, $k, $function, $fileline);
        }

        $error = sprintf($errorString, $type, $message, $file, $line, $trace);

        if ($e->getPrevious()) {
            $error = $this->formatExceptions($e->getPrevious()) . $error;
        }

        return $error;
    }

    protected function processFrame(Util\Frame $frame)
    {
        $function = $frame->getClass() ?: '';
        $function .= $frame->getClass() && $frame->getFunction() ? ":" : "";
        $function .= $frame->getFunction() ?: '';

        $fileline = ($frame->getFile() ?: '<#unknown>');
        $fileline .= ':';
        $fileline .= (int)$frame->getLine();
        return [$function, $fileline];
    }
}
