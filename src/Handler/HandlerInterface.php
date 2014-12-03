<?php

namespace Aura\Error\Handler;

interface HandlerInterface {

    public function handle(\Exception $e);

}