<?php

namespace League\BooBoo\Formatter;

class CommandLineFormatter extends AbstractFormatter
{

    protected $showExceptionsStack;

    /**
     * @param bool $showExceptionsStack  If set to true will display the complete stack of exception and their traces,
     *                                   instead of showing only the highest one.
     */
    public function __construct($showExceptionsStack = false) {
        $this->showExceptionsStack = $showExceptionsStack;
    }

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

    /**
     * @param \Throwable $e
     * @return string
     */
    protected function formatSingleException($e)
    {
        $errorString = "%s%s: %s\n";
        $errorString .= "%s(%d)\n\n";
        $errorString .= "TRACE:\n%s\n";

        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();
        $code = null;
        if ($e->getCode()) {
            $code = '[' . $e->getCode() . '] ';
        }

        $error = sprintf($errorString, $code, $type, $message, $file, $line, $trace);
        return $error;
    }

    /**
     * @param \Throwable $e
     * @return string
     */
    protected function formatExceptions($e)
    {
        $header = "+---------------------------+\n|    UNHANDLED EXCEPTION    |\n+---------------------------+\n";
        $error = $header.$this->formatSingleException($e);

        if ($this->showExceptionsStack) {
            $i = 1;
            while ($e = $e->getPrevious()) {
                $ct = str_pad($i++, 3, '0', STR_PAD_LEFT);
                $error .= "\n+---------------+\n| PREVIOUS #$ct |\n+---------------+\n";
                $error .= $this->formatSingleException($e);
            }
        }

        return $error;
    }
}
