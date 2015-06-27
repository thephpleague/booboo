<?php
/**
 * File modified from Filp/Whoops
 * @author Filipe Dobreira <http://github.com/filp>
 * @author Brandon Savage <http://github.com/brandonsavage>
 */

namespace League\BooBoo\Util;

class Inspector
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var FrameCollection
     */
    private $frames;

    /**
     * @var Inspector
     */
    private $previousExceptionInspector;

    /**
     * @param \Exception $exception The exception to inspect
     */
    public function __construct($exception)
    {
        if (!($exception instanceof \Exception) && !($exception instanceof \Throwable)) {
            throw new \InvalidArgumentException('The exception was not valid');
        }
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return string
     */
    public function getExceptionName()
    {
        return get_class($this->exception);
    }

    /**
     * Does the wrapped Exception has a previous Exception?
     *
     * @return boolean
     */
    public function hasPreviousException()
    {
        return $this->previousExceptionInspector || $this->exception->getPrevious();
    }

    /**
     * Returns an Inspector for a previous Exception, if any.
     *
     * @return Inspector
     */
    public function getPreviousExceptionInspector()
    {
        if ($this->previousExceptionInspector === null) {
            $previousException = $this->exception->getPrevious();

            if ($previousException) {
                $this->previousExceptionInspector = new Inspector($previousException);
            }
        }

        return $this->previousExceptionInspector;
    }

    /**
     * Returns an iterator for the inspected exception's frames.
     *
     * @return FrameCollection
     */
    public function getFrames()
    {
        if ($this->frames === null) {
            $frames = $this->exception->getTrace();

            // If we're handling an \ErrorException thrown by BooBoo,
            // get rid of the last frame, which matches the handleError method,
            // and do not add the current exception to trace. We ensure that
            // the next frame does have a filename / linenumber, though.
            if ($this->exception instanceof \ErrorException) {
                foreach ($frames as $k => $frame) {
                    if (isset($frame['class']) &&
                        strpos($frame['class'], 'BooBoo') !== false
                    ) {
                        unset($frames[$k]);
                    }
                }
            }

            $this->frames = new FrameCollection($frames);

            if ($previousInspector = $this->getPreviousExceptionInspector()) {
                // Keep outer frame on top of the inner one
                $outerFrames = $this->frames;
                $newFrames = clone $previousInspector->getFrames();
                $newFrames->prependFrames($outerFrames->topDiff($newFrames));
                $this->frames = $newFrames;
            }
        }

        return $this->frames;
    }

    /**
     * Checks if the inspector has frames
     *
     * Note: this essentially generates the frames (which is usually done by the first call to getFrames)
     *
     * @return boolean
     */
    public function hasFrames()
    {
        $frames = $this->getFrames();

        return count($frames) > 0;
    }
}
