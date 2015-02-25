<?php

namespace League\BooBoo;

use League\BooBoo\Formatter;
use League\BooBoo\Handler;
use Mockery;

function error_get_last()
{
    return [
        'type' => RunnerExt::$LAST_ERROR,
        'message' => 'error in file',
        'file' => 'test.php',
        'line' => 8,
    ];
}

class RunnerExt extends Runner {

    static public $LAST_ERROR = E_ERROR;

    public function getSilence() {
        return $this->silenceErrors;
    }

    public function register() {
        parent::register();
        $this->registered = true;
    }

    public function deregister() {
        parent::deregister();
        $this->registered = false;
    }

    protected function terminate()
    {
        return;
    }

}

class RunnerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Runner
     */
    protected $runner;

    /**
     * @var Mockery\MockInterface
     */
    protected $formatter;
    protected $handler;

    protected function setUp() {
        ini_set('display_errors', true);
        $this->runner = new Runner;

        $this->formatter = Mockery::mock('League\BooBoo\Formatter\AbstractFormatter');
        $this->handler = Mockery::mock('League\BooBoo\Handler\HandlerInterface');
        $this->runner->pushFormatter($this->formatter);
    }

    /**
     * @expectedException \League\BooBoo\Exception\NoFormattersRegisteredException
     */
    public function testNoFormatterRaisesException() {
        $runner = new Runner;
        $runner->register();
    }

    public function testHandlerMethods() {
        $runner = new Runner;

        $this->assertEmpty($runner->getHandlers());

        $runner->pushHandler(Mockery::mock('League\BooBoo\Handler\HandlerInterface'));
        $runner->pushHandler(Mockery::mock('League\BooBoo\Handler\HandlerInterface'));

        $this->assertEquals(2, count($runner->getHandlers()));
        $this->assertInstanceOf('League\BooBoo\Handler\HandlerInterface', $runner->popHandler());
        $this->assertEquals(1, count($runner->getHandlers()));

        $runner->clearHandlers();
        $this->assertEmpty($runner->getHandlers());
    }

    public function testFormatterMethods() {
        $runner = new Runner;

        $this->assertEmpty($runner->getFormatters());

        $runner->pushFormatter(Mockery::mock('League\BooBoo\Formatter\FormatterInterface'));
        $runner->pushFormatter(Mockery::mock('League\BooBoo\Formatter\FormatterInterface'));

        $this->assertEquals(2, count($runner->getFormatters()));
        $this->assertInstanceOf('League\BooBoo\Formatter\FormatterInterface', $runner->popFormatter());
        $this->assertEquals(1, count($runner->getFormatters()));

        $runner->clearFormatters();
        $this->assertEmpty($runner->getFormatters());
    }

    public function testConstructorAssignsHandlersAndFormatters() {
        $runner = new Runner(
            [
                Mockery::mock('League\BooBoo\Formatter\FormatterInterface'),
                Mockery::mock('League\BooBoo\Formatter\FormatterInterface'),
            ],
            [
                Mockery::mock('League\BooBoo\Handler\HandlerInterface'),
            ]
        );

        $this->assertCount(2, $runner->getFormatters());
        $this->assertCount(1, $runner->getHandlers());
    }

    public function testErrorsSilencedWhenSilenceTrue() {
        $formatter = Mockery::mock('League\BooBoo\Formatter\FormatterInterface');
        $formatter->shouldReceive('getErrorLimit')->never();
        $formatter->shouldReceive('format')->never();

        $runner = new Runner();
        $runner->silenceAllErrors(true);
        $runner->pushFormatter($formatter);

        // Now we fake an error
        $runner->errorHandler(E_WARNING, 'warning', 'index.php', 11);


        // Let's verify that it wasn't called.
        try {
            $formatter->mockery_verify();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @expectedException \ErrorException
     */
    public function testThrowErrorsAsExceptions() {
        $this->runner->treatErrorsAsExceptions(true);
        $this->runner->errorHandler(E_WARNING, 'test', 'test.php', 11);
    }

    public function testFormattersFormatCode() {
        $this->formatter->shouldReceive('getErrorLimit')->andReturn(E_ALL);
        $this->formatter->shouldReceive('format')->twice()->andReturn('');

        $this->runner->errorHandler(E_WARNING, 'warning', 'index.php', 11);
        $this->runner->exceptionHandler(new \Exception);

        try {
            $this->formatter->mockery_verify();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testErrorReportingOffSilencesErrors() {
        error_reporting(0);
        $result = $this->runner->errorHandler(E_WARNING, 'error', 'index.php', 11);
        $this->assertTrue($result);
        error_reporting(E_ALL);

    }

    public function testErrorsSilencedWhenErrorReportingOff() {
        if(strpos(phpversion(), 'hhvm')) {
            $this->markTestSkipped();
        }
        $er = ini_get('display_errors');
        ini_set('display_errors', 0);

        $runner = new RunnerExt();
        ini_set('display_errors', $er);

        $this->assertTrue($runner->getSilence());
    }

    public function testRegisterAndDeregister() {
        $formatter = Mockery::mock('League\BooBoo\Formatter\FormatterInterface');
        $formatter->shouldIgnoreMissing();

        $runner = new RunnerExt([$formatter]);

        $runner->register();
        $this->assertTrue($runner->registered);

        $runner->deregister();
        $this->assertFalse($runner->registered);
    }

    public function testErrorPageHandler() {
        $this->runner->setErrorPageFormatter($this->formatter);
        $this->runner->silenceAllErrors(true);
        $this->formatter->shouldReceive('format')->andReturn('');

        $this->runner->exceptionHandler(new \Exception);

        try {
            $this->formatter->mockery_verify();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testHandlersAreRun()
    {
        $runner = new Runner;

        $this->assertEmpty($runner->getHandlers());

        $handler = Mockery::mock('League\BooBoo\Handler\HandlerInterface');
        $handler->shouldReceive('handle')->once()->with(Mockery::type('Exception'));

        $runner->pushHandler($handler);
        $runner->exceptionHandler(new \Exception);
    }

    public function testShutdownHandler()
    {

        $formatter = Mockery::mock('League\BooBoo\Formatter\FormatterInterface');
        $formatter->shouldReceive('getErrorLimit')->andReturn(E_ERROR);
        $formatter->shouldReceive('format');

        $runner = new RunnerExt([$formatter]);
        $runner->shutdownHandler();
    }


    public function testShutdownHandlerIgnoresNonfatal()
    {
        RunnerExt::$LAST_ERROR = E_WARNING;
        $formatter = Mockery::mock('League\BooBoo\Formatter\FormatterInterface');
        $formatter->shouldNotHaveReceived('getErrorLimit');
        $formatter->shouldNotHaveReceived('format');

        $runner = new RunnerExt([$formatter]);
        $runner->shutdownHandler();

        RunnerExt::$LAST_ERROR = E_ERROR;
    }
}