<?php

namespace Savage\ShitHappens\Formatter;

class JsonFormatter extends AbstractFormatter {

    public function format(\Exception $e) {
        if($e instanceof \ErrorException) {
            return $this->handleErrors($e);
        }

        return $this->formatExceptions($e);
    }

    public function handleErrors(\ErrorException $e) {
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
        return json_encode($error);
    }

    protected function formatExceptions(\Exception $e) {
        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTrace();

        $error = [
            'severity' => 'Exception',
            'type' => $type,
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'trace' => $trace
        ];
        return json_encode($error);
    }

}