<?php

use Savage\BooBoo\Util\Frame;

class FrameTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Frame
     */
    protected $frame;


    protected $frameArr = array();

    protected function setUp() {

        $frame = [
            'file' => 'index.php',
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
        $this->assertEquals('index.php', $file);
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

    public function testGetRawFrame() {
        $this->assertEquals($this->frame->getRawFrame(), $this->frameArr);
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