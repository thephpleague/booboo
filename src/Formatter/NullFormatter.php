<?php

namespace League\BooBoo\Formatter;

class NullFormatter extends AbstractFormatter
{

    public function format(\Exception $e)
    {
        return; // Silence the error.
    }
}
