<?php

use League\BooBoo\Formatter\JsonFormatter;

class JsonFormatterTest extends PHPUnit_Framework_TestCase {

    public function testErrorExceptionFormatting() {
        $exception = new \ErrorException('whoops', 0, E_ERROR, 'index.php', 11);
        $formatter = new JsonFormatter();
        $result = $formatter->format($exception);

        $expected = json_encode([
            'message' => 'whoops',
            'severity' => 'Fatal Error',
            'file' => 'index.php',
            'line' => 11,
        ]);

        $this->assertEquals($expected, $result);
    }

    public function testRegularExceptionErrorFormatting() {
        $exception = new \Exception('whoops', 123);
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
            'code' => 123,
            'type' => 'Exception',
            'message' => 'whoops',
            'file' => $file,
            'line' => $line,
        ];

        $this->assertEquals($expected, $result);
    }

    public function testNestedExceptionsDisplayBothMessages() {
        $exception = new \Exception('whoops');
        $exception2 = new Exception('bang', 0, $exception);

        $formatter = new JsonFormatter();
        $result = $formatter->format($exception2);

        $result = json_decode($result, true);

        $this->assertEquals(2, count($result));
        $this->assertEquals('whoops', $result[0]['message']);
        $this->assertEquals('bang', $result[1]['message']);

    }

}