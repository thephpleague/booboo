<?php

use League\BooBoo\Formatter;

/**
 * Class AbstractFormatterExt
 *
 * Because these methods are used often and largely stand alone, testing them
 * on their own makes sense. This stub class exists solely to ensure testing these
 * methods is possible.
 */
class AbstractFormatterExt extends Formatter\AbstractFormatter
{
    public function format($e)
    {
        throw new \Exception('This method is not implemented');
    }

    public function getSeverity($severity)
    {
        return $this->determineSeverityTextValue($severity);
    }
}

class AbstractFormatterTest extends PHPUnit_Framework_TestCase
{
    private $formatter;

    public function setUp()
    {
        $this->formatter = new AbstractFormatterExt();
    }

    public function testSeverityTextCorrect()
    {
        $this->assertEquals('Fatal Error', $this->formatter->getSeverity(E_ERROR));
        $this->assertEquals('Fatal Error', $this->formatter->getSeverity(E_USER_ERROR));
        $this->assertEquals('Fatal Error', $this->formatter->getSeverity(E_CORE_ERROR));
        $this->assertEquals('Fatal Error', $this->formatter->getSeverity(E_COMPILE_ERROR));
        $this->assertEquals('Parse Error', $this->formatter->getSeverity(E_PARSE));
        $this->assertEquals('Warning', $this->formatter->getSeverity(E_WARNING));
        $this->assertEquals('Warning', $this->formatter->getSeverity(E_USER_WARNING));
        $this->assertEquals('Warning', $this->formatter->getSeverity(E_CORE_WARNING));
        $this->assertEquals('Warning', $this->formatter->getSeverity(E_COMPILE_WARNING));
        $this->assertEquals('Notice', $this->formatter->getSeverity(E_NOTICE));
        $this->assertEquals('Notice', $this->formatter->getSeverity(E_USER_NOTICE));
        $this->assertEquals('Strict Standards', $this->formatter->getSeverity(E_STRICT));
        $this->assertEquals('Catchable Error', $this->formatter->getSeverity(E_RECOVERABLE_ERROR));
        $this->assertEquals('Deprecated', $this->formatter->getSeverity(E_DEPRECATED));
        $this->assertEquals('Deprecated', $this->formatter->getSeverity(E_USER_DEPRECATED));
    }

    public function testInvalidSeverityGeneratesNotDeterminedMessage()
    {
        $severity = $this->formatter->getSeverity(1234);
        $this->assertEquals('Unknown Error', $severity);
    }

    public function testGetAndSetErrorSeverityLevels()
    {
        $this->assertEquals(E_ALL, $this->formatter->getErrorLimit());

        $this->formatter->setErrorLimit(E_ERROR);
        $this->assertEquals(E_ERROR, $this->formatter->getErrorLimit());
    }

    public function testContentType()
    {
        $this->assertSame('text/plain', $this->formatter->getContentType());
    }
}
