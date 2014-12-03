<?php

namespace Savage\ShitHappens\Handler;

interface HandlerInterface {

    public function handle(\Exception $e);

}