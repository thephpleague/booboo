<?php

use Savage\BooBoo\Formatter;

class NullFormatterTest extends PHPUnit_Framework_TestCase {

    public function testNoResponseForAnyException() {
        $formatter = new Formatter\NullFormatter();
        $this->assertNull($formatter->format(new \Exception));
        $this->assertNull($formatter->format(new \ErrorException));
    }
}