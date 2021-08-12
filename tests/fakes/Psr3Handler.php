<?php

namespace Tests\Fakes;

use Psr\Log\LoggerInterface;

class Psr3Handler implements LoggerInterface
{
    public static $emergency = 0;
    public static $alert = 0;
    public static $critical = 0;
    public static $error = 0;
    public static $warning = 0;
    public static $notice = 0;
    public static $info = 0;
    public static $debug = 0;
    public static $log = 0;

    public function emergency($message, array $context = array())
    {
        self::$emergency++;
    }

    public function alert($message, array $context = array())
    {
        self::$alert++;
    }

    public function critical($message, array $context = array())
    {
        self::$critical++;
    }

    public function error($message, array $context = array())
    {
        self::$error++;
    }

    public function warning($message, array $context = array())
    {
        self::$warning++;
    }

    public function notice($message, array $context = array())
    {
        self::$notice++;
    }

    public function info($message, array $context = array())
    {
        self::$info++;
    }

    public function debug($message, array $context = array())
    {
        self::$debug++;
    }

    public function log($level, $message, array $context = array())
    {
        self::$log++;
    }

}
