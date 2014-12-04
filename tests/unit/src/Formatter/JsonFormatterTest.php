<?php

use Savage\ShitHappens\Formatter\JsonFormatter;

class JsonFormatterTest extends PHPUnit_Framework_TestCase {

    public function testErrorExceptionFormatting() {
        $exception = new \ErrorException('whoops', 0, E_ERROR, 'index.php', 11);
        $formatter = new JsonFormatter();
        $result = $formatter->format($exception);

        $expected = json_encode([
            'message' => 'whoops',
            'severity' => 'Error',
            'file' => 'index.php',
            'line' => 11,
        ]);

        $this->assertEquals($expected, $result);
    }

    public function testRegularExceptionErrorFormatting() {
        $exception = new \Exception('whoops');
        $trace = $exception->getTrace();
        $line = $exception->getLine();
        $file = $exception->getFile();
        $formatter = new JsonFormatter();
        $result = $formatter->format($exception);

        $result = json_decode($result, true);
        // Exception traces change.
        unset($result['trace']);

        $expected = [
            'severity' => 'Exception',
            'type' => 'Exception',
            'message' => 'whoops',
            'file' => $file,
            'line' => $line,
        ];

        $this->assertEquals($expected, $result);
    }

}