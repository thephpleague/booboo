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
     * @link https://www.php.net/manual/en/iteratoraggregate.getiterator
     * @return \Traversable|\ArrayIterator
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->frames);
    }

    /**
     * @see ArrayAccess::offsetExists
     * @link https://www.php.net/manual/en/arrayaccess.offsetexists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->frames[$offset]);
    }

    /**
     * @see ArrayAccess::offsetGet
     * @link https://www.php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->frames[$offset];
    }

    /**
     * @see ArrayAccess::offsetSet
     * @link https://www.php.net/manual/en/arrayaccess.offsetset.php
     * @param int $offset
     * @return void
     * @throws \Exception
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \Exception(__CLASS__ . ' is read only');
    }

    /**
     * @see ArrayAccess::offsetUnset
     * @link https://www.php.net/manual/en/arrayaccess.offsetunset.php
     * @param int $offset
     * @return void
     * @throws \Exception
     */
    public function offsetUnset($offset): void
    {
        throw new \Exception(__CLASS__ . ' is read only');
    }

    /**
     * @see Countable::count
     * @link https://www.php.net/manual/en/countable.count
     * @return int
     */
    public function count(): int
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
