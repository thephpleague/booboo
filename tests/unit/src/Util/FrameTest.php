<?php

use League\BooBoo\Util\Frame;
use PHPUnit\Framework\TestCase;

class FrameTest extends TestCase {

    /**
     * @var Frame
     */
    protected $frame;


    protected $frameArr = array();

    protected function setUp() : void
    {

        $frame = [
            'file' => __DIR__.'/../../index.php',
            'line' => 11,
            'class' => 'ABC',
            'function' => 'ghi',
            'args' => [
                'abc',
                'def',
                'ghi'
            ],
        ];

        $this->frame = new Frame($frame);
        $this->frameArr = $frame;

    }

    public function testGetFile() {
        $file = $this->frame->getFile();
        $this->assertEquals(__DIR__.'/../../index.php', $file);
    }

    public function testGetLine() {
        $this->assertEquals(11, $this->frame->getLine());
    }

    public function testGetClass() {
        $this->assertEquals('ABC', $this->frame->getClass());
    }

    public function testGetFunction() {
        $this->assertEquals('ghi', $this->frame->getFunction());
    }

    public function testGetArgs() {
        $args = $this->frame->getArgs();
        $this->assertCount(3, $args);
    }

    public function testGetFileContents()
    {
        $this->assertEquals("test" . PHP_EOL . "content", $this->frame->getFileContents());
    }

    public function testComments()
    {
        $this->frame->addComment('Comment');

        $this->assertEquals([[
            'comment' => 'Comment',
            'context' => 'global',
        ]], $this->frame->getComments());
    }

    public function testGetRawFrame() {
        $this->assertEquals($this->frame->getRawFrame(), $this->frameArr);
    }

    public function testGetFileLines()
    {
        $this->assertEquals(['test', 'content'], $this->frame->getFileLines());
        $this->assertEquals(['test'], $this->frame->getFileLines(0, 1));
        $this->assertEquals([1 => 'content'], $this->frame->getFileLines(1, 1));
    }

    public function testGetFileLinesStartDefaultsZero()
    {
        $this->assertEquals(['test', 'content'], $this->frame->getFileLines(-100, 2));
    }

    public function testGetFileLinesLengthLessThanZeroRaisesException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->frame->getFileLines(0, -1);
    }

    public function testFramesAreEqual() {
        $this->assertTrue($this->frame->equals($this->frame));
    }

    public function testFramesAreNotEqual() {
        $notEqualFrame = $this->frameArr;
        $notEqualFrame['file'] = 'test.php';
        $this->assertFalse($this->frame->equals(new Frame($notEqualFrame)));
    }

    public function testSerialize() {
        $serialized = serialize($this->frame);
        $unserializedObj = unserialize($serialized);
        $this->assertTrue(($this->frame == $unserializedObj));
    }
}
