<?php

namespace Savage\ShitHappens\Formatter;

class NullFormatter implements FormatterInterface {

    public function format(\Exception $e) {
        return; // Silence the error.
    }

}