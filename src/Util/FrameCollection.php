<?php
/**
 * File used from Filp/Whoops
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace League\BooBoo\Util;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Serializable;
use UnexpectedValueException;

/**
 * Exposes a fluent interface for dealing with an ordered list
 * of stack-trace frames.
 */
class FrameCollection implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * @var array[]
     */
    private $frames;

    /**
     * @param array $frames
     */
    public function __construct(array $frames)
    {
        $this->frames = array_map(function ($frame) {
            return new Frame($frame);
        }, $frames);
    }

    /**
     * Returns an array with all frames, does not affect
     * the internal array.
     *
     * @todo   If this gets any more complex than this,
     *         have getIterator use this method.
     * @see    FrameCollection::getIterator
     * @return array
     */
    public function getArray()
    {
        return $this->frames;
    }

    /**
     * @see IteratorAggregate::getIterator
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->frames);
    }

    /**
     * @see ArrayAccess::offsetExists
     * @param int $offset
     */
    public function offsetExists($offset)
    {
        return isset($this->frames[$offset]);
    }

    /**
     * @see ArrayAccess::offsetGet
     * @param int $offset
     */
    public function offsetGet($offset)
    {
        return $this->frames[$offset];
    }

    /**
     * @see ArrayAccess::offsetSet
     * @param int $offset
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception(__CLASS__ . ' is read only');
    }

    /**
     * @see ArrayAccess::offsetUnset
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        throw new \Exception(__CLASS__ . ' is read only');
    }

    /**
     * @see Countable::count
     * @return int
     */
    public function count()
    {
        return count($this->frames);
    }

    /**
     * @param Frame[] $frames Array of Frame instances, usually from $e->getPrevious()
     */
    public function prependFrames(array $frames)
    {
        $this->frames = array_merge($frames, $this->frames);
    }

    /**
     * Gets the innermost part of stack trace that is not the same as that of outer exception
     *
     * @param  FrameCollection $parentFrames Outer exception frames to compare tail against
     * @return Frame[]
     */
    public function topDiff(FrameCollection $parentFrames)
    {
        $diff = $this->frames;

        $parentFrames = $parentFrames->getArray();
        $p = count($parentFrames) - 1;

        for ($i = count($diff) - 1; $i >= 0 && $p >= 0; $i--) {
            /** @var Frame $tailFrame */
            $tailFrame = $diff[$i];
            if ($tailFrame->equals($parentFrames[$p])) {
                unset($diff[$i]);
            }
            $p--;
        }
        return $diff;
    }
}
