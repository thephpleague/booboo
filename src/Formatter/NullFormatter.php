<?php

namespace Savage\ShitHappens\Formatter;

class NullFormatter extends AbstractFormatter {

    public function format(\Exception $e) {
        return; // Silence the error.
    }

}