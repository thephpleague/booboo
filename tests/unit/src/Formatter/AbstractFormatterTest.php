<?php

use Savage\BooBoo\Formatter;

/**
 * Class AbstractFormatterExt
 *
 * Because these methods are used often and largely stand alone, testing them
 * on their own makes sense. This stub class exists solely to ensure testing these
 * methods is possible.
 */
class AbstractFormatterExt extends Formatter\AbstractFormatter {

    public function format(\Exception $e) {
        throw new \Exception('This method is not implemented');
    }

    public function getSeverity($severity) {
        return $this->determineSeverityTextValue($severity);
    }
}

class AbstractFormatterTest extends PHPUnit_Framework_TestCase {

    public function testSeverityTextCorrect() {
        $formatter = new AbstractFormatterExt;
        $this->assertEquals('Fatal Error', $formatter->getSeverity(E_ERROR));
        $this->assertEquals('Fatal Error', $formatter->getSeverity(E_USER_ERROR));
        $this->assertEquals('Fatal Error', $formatter->getSeverity(E_CORE_ERROR));
        $this->assertEquals('Fatal Error', $formatter->getSeverity(E_COMPILE_ERROR));
        $this->assertEquals('Parse Error', $formatter->getSeverity(E_PARSE));
        $this->assertEquals('Warning', $formatter->getSeverity(E_WARNING));
        $this->assertEquals('Warning', $formatter->getSeverity(E_USER_WARNING));
        $this->assertEquals('Warning', $formatter->getSeverity(E_CORE_WARNING));
        $this->assertEquals('Warning', $formatter->getSeverity(E_COMPILE_WARNING));
        $this->assertEquals('Notice', $formatter->getSeverity(E_NOTICE));
        $this->assertEquals('Notice', $formatter->getSeverity(E_USER_NOTICE));
        $this->assertEquals('Strict Standards', $formatter->getSeverity(E_STRICT));
        $this->assertEquals('Catchable Error', $formatter->getSeverity(E_RECOVERABLE_ERROR));
        $this->assertEquals('Deprecated', $formatter->getSeverity(E_DEPRECATED));
        $this->assertEquals('Deprecated', $formatter->getSeverity(E_USER_DEPRECATED));
    }

    public function testInvalidSeverityGeneratesNotDeterminedMessage() {
        $formatter = new AbstractFormatterExt();
        $severity = $formatter->getSeverity(1234);
        $this->assertEquals('Unknown Error', $severity);
    }

    public function testGetAndSetErrorSeverityLevels() {
        $formatter = new AbstractFormatterExt();
        $this->assertEquals(E_ALL, $formatter->getErrorLimit());

        $formatter->setErrorLimit(E_ERROR);
        $this->assertEquals(E_ERROR, $formatter->getErrorLimit());
    }

}