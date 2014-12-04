<?php

use Savage\ShitHappens\Formatter\HtmlFormatter;

class HtmlFormatterTest extends PHPUnit_Framework_TestCase {

    public function testErrorExceptionFormatting() {
        $exception = new \ErrorException('whoops', 0, E_ERROR, 'index.php', 11);
        $formatter = new HtmlFormatter();
        $result = $formatter->format($exception);

        $expected = "<br /><strong>Error</strong>: whoops in <strong>index.php</strong> on line <strong>11</strong><br />";

        $this->assertEquals($expected, $result);
    }

    public function testRegularExceptionErrorFormatting() {
        $exception = new \Exception('whoops');
        $trace = $exception->getTraceAsString();
        $line = $exception->getLine();
        $file = $exception->getFile();
        $formatter = new HtmlFormatter();
        $result = $formatter->format($exception);

        $expected = "<br /><strong>Fatal error:</strong> Uncaught exception 'Exception' with message 'whoops' in {$file} on line {$line}<br />{$trace}<br />";

        $this->assertEquals($expected, $result);
    }

}