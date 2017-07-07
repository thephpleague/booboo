<?php

use League\BooBoo\Formatter\JsonFormatter;

class JsonFormatterTest extends PHPUnit_Framework_TestCase
{
    private $formatter;

    public function setUp()
    {
        $this->formatter = new JsonFormatter();
    }

    public function testErrorExceptionFormatting()
    {
        $exception = new \ErrorException('whoops', 0, E_ERROR, 'index.php', 11);
        $result = $this->formatter->format($exception);

        $expected = json_encode([
            'message' => 'whoops',
            'severity' => 'Fatal Error',
            'file' => 'index.php',
            'line' => 11,
        ]);

        $this->assertEquals($expected, $result);
    }

    public function testRegularExceptionErrorFormatting()
    {
        $exception = new \Exception('whoops');
        $trace = $exception->getTrace();
        $line = $exception->getLine();
        $file = $exception->getFile();
        $result = $this->formatter->format($exception);

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

    public function testNestedExceptionsDisplayBothMessages()
    {
        $exception = new \Exception('whoops');
        $exception2 = new Exception('bang', 0, $exception);

        $result = $this->formatter->format($exception2);

        $result = json_decode($result, true);

        $this->assertEquals(2, count($result));
        $this->assertEquals('whoops', $result[0]['message']);
        $this->assertEquals('bang', $result[1]['message']);
    }

    public function testContentType()
    {
        $this->assertSame('application/json', $this->formatter->getContentType());
    }
}
