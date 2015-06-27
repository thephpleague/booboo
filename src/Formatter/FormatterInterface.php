<?php

namespace League\BooBoo\Formatter;

interface FormatterInterface
{
    public function format($e);

    public function setErrorLimit($limit);

    public function getErrorLimit();
}
