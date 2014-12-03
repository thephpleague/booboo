<?php

namespace Aura\Error\Formatter;

interface FormatterInterface {

    public function format(\Exception $e);

}