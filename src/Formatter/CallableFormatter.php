<?php
namespace League\BooBoo\Formatter;

use Exception;

class CallableFormatter extends AbstractFormatter
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param Exception $e
     * @return mixed
     */
    public function format($e)
    {
        return call_user_func($this->callable, $e);
    }
}
