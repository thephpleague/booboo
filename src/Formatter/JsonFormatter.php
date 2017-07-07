<?php

namespace League\BooBoo\Formatter;

class JsonFormatter extends AbstractFormatter
{
    public function format($e)
    {
        if ($e instanceof \ErrorException) {
            $arrays = $this->handleErrors($e);
        } else {
            $arrays = $this->formatExceptions($e);
        }

        return json_encode($arrays);
    }

    public function handleErrors(\ErrorException $e)
    {
        $severity = $this->determineSeverityTextValue($e->getSeverity());
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();

        $error = [
            'message' => $message,
            'severity' => $severity,
            'file' => $file,
            'line' => $line,
        ];
        return $error;
    }

    protected function formatExceptions($e)
    {
        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTrace();

        $error = [
            'severity' => 'Exception',
            'type' => $type,
            'code' => $e->getCode(),
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'trace' => $trace,
        ];

        if ($e->getPrevious()) {
            $error = [$error];
            $newError = $this->formatExceptions($e->getPrevious());
            array_unshift($error, $newError);
        }

        return $error;
    }
}
