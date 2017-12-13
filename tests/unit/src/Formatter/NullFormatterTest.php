<?php

use League\BooBoo\Formatter;
use PHPUnit\Framework\TestCase;

class NullFormatterTest extends TestCase {

    public function testNoResponseForAnyException() {
        $formatter = new Formatter\NullFormatter();
        $this->assertNull($formatter->format(new \Exception));
        $this->assertNull($formatter->format(new \ErrorException));
    }
}