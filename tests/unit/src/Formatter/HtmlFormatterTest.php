<?php

use Savage\BooBoo\Formatter\HtmlFormatter;

class HtmlFormatterTest extends PHPUnit_Framework_TestCase {

    public function testErrorExceptionFormatting() {
        $exception = new \ErrorException('whoops', 0, E_ERROR, 'index.php', 11);
        $formatter = new HtmlFormatter();
        $result = $formatter->format($exception);

        $expected = "<br /><strong>Fatal Error</strong>: whoops in <strong>index.php</strong> on line <strong>11</strong><br />";

        $this->assertEquals($expected, $result);
    }

    public function testRegularExceptionErrorFormatting() {
        $exception = new \Exception('whoops');
        $file = $exception->getFile();
        $line = $exception->getLine();
        $formatter = new HtmlFormatter();
        $result = $formatter->format($exception);
        $expected = "<br /><strong>Fatal error:</strong> Uncaught exception 'Exception' with message 'whoops' in {$file} on line {$line}<br />";
        // Use strpos to assert the string is in the other string.
        $this->assertNotFalse(strpos($result, $expected));
    }

    public function testNestedExceptionsDisplayBothMessages() {
        $exception = new \Exception('whoops');
        $exception2 = new Exception('bang', 0, $exception);

        $formatter = new HtmlFormatter();
        $result = $formatter->format($exception2);
        $expectedString1 = "'Exception' with message 'whoops'";
        $expectedString2 = "'Exception' with message 'bang'";

        $position1 = strpos($result, $expectedString1);
        $position2 = strpos($result, $expectedString2);

        $this->assertNotFalse($position1);
        $this->assertNotFalse($position2);
        $this->assertGreaterThan($position1, $position2);
    }

}