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
     * @var string
     */
    protected $fileContentsCache;

    /**
     * @var array
     */
    protected $comments = [];

    /**
     * @param array $frame
     */
    public function __construct(array $frame)
    {
        $this->frame = $frame;
    }

    /**
     * @param boolean $shortened
     *
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
     * @return integer|null
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
        return isset($this->frame['args']) ? (array)$this->frame['args'] : [];
    }

    /**
     * Returns the full contents of the file for this frame, if it's known
     *
     * @return string|null
     */
    public function getFileContents()
    {
        if ($this->fileContentsCache === null && $filePath = $this->getFile()) {
            if ($filePath === "Unknown" || !is_file($filePath)) {
                return null;
            }

            $this->fileContentsCache = file_get_contents($filePath);
        }

        return $this->fileContentsCache;
    }

    /**
     * Adds a comment to this frame, that can be received and
     * used by other handlers. For example, the PrettyPage handler
     * can attach these comments under the code for each frame
     *
     * An interesting use for this would be, for example, code analysis
     * & annotations
     *
     * @param string $comment
     * @param string $context Optional string identifying the origin of the comment
     */
    public function addComment($comment, $context = 'global')
    {
        $this->comments[] = [
            'comment' => $comment,
            'context' => $context,
        ];
    }

    /**
     * Returns all comments for this frame. Optionally allows
     * a filter to only retrieve comments from a specific
     * context
     *
     * @param string $filter
     *
     * @return array
     */
    public function getComments($filter = null)
    {
        $comments = $this->comments;

        if ($filter !== null) {
            $comments = array_filter($comments, function ($c) use ($filter) {
                return $c['context'] == $filter;
            });
        }

        return $comments;
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
     * Returns the contents of the file for this frame as an
     * array of lines, and optionally as a clamped range of lines
     *
     * NOTE: lines are 0-indexed
     *
     * @example
     *     Get all lines for this file
     *     $frame->getFileLines(); // => array( 0 => '<?php', 1 => '...', ...)
     * @example
     *     Get one line for this file, starting at line 10 (zero-indexed, remember!)
     *     $frame->getFileLines(9, 1); // array( 10 => '...', 11 => '...')
     *
     * @param integer $start
     * @param integer $length
     *
     * @return string[]|null
     *
     * @throws \InvalidArgumentException if $length is less than or equal to 0
     */
    public function getFileLines($start = 0, $length = null)
    {
        $contents = $this->getFileContents();

        if (isset($contents)) {
            $lines = explode(PHP_EOL, $contents);
            // Get a subset of lines from $start to $end
            if ($length !== null) {
                $start  = (int) $start;
                $length = (int) $length;

                if ($start < 0) {
                    $start = 0;
                }

                if ($length <= 0) {
                    throw new \InvalidArgumentException(sprintf('$length must be greater than 0'));
                }

                $lines = array_slice($lines, $start, $length, true);
            }

            return $lines;
        }
    }

    /**
     * Implements the Serializable interface.
     *
     * @see Serializable::serialize
     *
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
     * @param string $serializedFrame
     *
     * @see Serializable::unserialize
     */
    public function unserialize($serializedFrame)
    {
        $frame = unserialize($serializedFrame);

        $this->frame = $frame;
    }

    /**
     * Compares Frame against one another
     *
     * @param Frame $frame
     *
     * @return boolean
     */
    public function equals(Frame $frame)
    {
        if (!$this->getFile() || $this->getFile() === 'Unknown' || !$this->getLine()) {
            return false;
        }

        return $frame->getFile() === $this->getFile() && $frame->getLine() === $this->getLine();
    }
}
