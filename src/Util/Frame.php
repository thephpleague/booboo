<?php
/**
 * File used from Filp/Whoops
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace League\BooBoo\Util;

use InvalidArgumentException;
use Serializable;

class Frame implements Serializable
{
    /**
     * @var array
     */
    protected $frame;

    /**
     * @param array []
     */
    public function __construct(array $frame)
    {
        $this->frame = $frame;
    }

    /**
     * @param  bool $shortened
     * @return string|null
     */
    public function getFile()
    {
        if (empty($this->frame['file'])) {
            return null;
        }

        $file = $this->frame['file'];

        // Check if this frame occurred within an eval().
        // @todo: This can be made more reliable by checking if we've entered
        // eval() in a previous trace, but will need some more work on the upper
        // trace collector(s).
        if (preg_match('/^(.*)\((\d+)\) : (?:eval\(\)\'d|assert) code$/', $file, $matches)) {
            $file = $this->frame['file'] = $matches[1];
            $this->frame['line'] = (int)$matches[2];
        }

        return $file;
    }

    /**
     * @return int|null
     */
    public function getLine()
    {
        return isset($this->frame['line']) ? $this->frame['line'] : null;
    }

    /**
     * @return string|null
     */
    public function getClass()
    {
        return isset($this->frame['class']) ? $this->frame['class'] : null;
    }

    /**
     * @return string|null
     */
    public function getFunction()
    {
        return isset($this->frame['function']) ? $this->frame['function'] : null;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return isset($this->frame['args']) ? (array)$this->frame['args'] : array();
    }

    /**
     * Returns the array containing the raw frame data from which
     * this Frame object was built
     *
     * @return array
     */
    public function getRawFrame()
    {
        return $this->frame;
    }

    /**
     * Implements the Serializable interface.
     *
     * @see Serializable::serialize
     * @return string
     */
    public function serialize()
    {
        $frame = $this->frame;
        return serialize($frame);
    }

    /**
     * Unserializes the frame data.
     *
     * @see Serializable::unserialize
     * @param string $serializedFrame
     */
    public function unserialize($serializedFrame)
    {
        $frame = unserialize($serializedFrame);
        $this->frame = $frame;
    }

    /**
     * Compares Frame against one another
     * @param  Frame $frame
     * @return bool
     */
    public function equals(Frame $frame)
    {
        if (!$this->getFile() || $this->getFile() === 'Unknown' || !$this->getLine()) {
            return false;
        }
        return $frame->getFile() === $this->getFile() && $frame->getLine() === $this->getLine();
    }
}
