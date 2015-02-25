<?php
/**
 * File modified from Filp/Whoops
 * @author Filipe Dobreira <http://github.com/filp>
 * @author Brandon Savage <http://github.com/brandonsavage>
 */

namespace League\BooBoo\Util;

use Exception;
use ErrorException;
use League\BooBoo\Util;

class Inspector
{
    /**
     * @var Exception
     */
    private $exception;

    /**
     * @var Util\FrameCollection
     */
    private $frames;

    /**
     * @var Util\Inspector
     */
    private $previousExceptionInspector;

    /**
     * @param Exception $exception The exception to inspect
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return Exception
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
     * @return string
     */
    public function getExceptionMessage()
    {
        return $this->exception->getMessage();
    }

    /**
     * Does the wrapped Exception has a previous Exception?
     * @return bool
     */
    public function hasPreviousException()
    {
        return $this->previousExceptionInspector || $this->exception->getPrevious();
    }

    /**
     * Returns an Inspector for a previous Exception, if any.
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
     * Returns an iterator for the inspected exception's
     * frames.
     * @return Util\FrameCollection
     */
    public function getFrames()
    {
        if ($this->frames === null) {
            $frames = $this->exception->getTrace();

            // If we're handling an ErrorException thrown by BooBoo,
            // get rid of the last frame, which matches the handleError method,
            // and do not add the current exception to trace. We ensure that
            // the next frame does have a filename / linenumber, though.
            if ($this->exception instanceof ErrorException) {
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
}
