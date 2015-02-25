<?php

namespace League\BooBoo\Formatter;

interface FormatterInterface
{

    public function format(\Exception $e);

    public function setErrorLimit($limit);

    public function getErrorLimit();
}
