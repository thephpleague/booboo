<?php

namespace League\BooBoo\Formatter;

class NullFormatter extends AbstractFormatter
{
    public function format($e)
    {
        return; // Silence the error.
    }
}
