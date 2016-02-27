<?php

namespace FreezyBee\Chronometer;

use Nette\Object;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Tracy\Debugger;

/**
 * Class LogParser
 * @package FreezyBee\Chronometer
 */
class LogParser extends Object
{
    /**
     * @var string
     */
    private $logFilename;

    /**
     * LogParser constructor.
     * @param $logFilename
     */
    public function __construct($logFilename)
    {
        $this->logFilename = $logFilename;
    }

    /**
     * @param bool $asArray
     * @return array|string|false
     */
    public function getLog($asArray = false)
    {
        $file = Debugger::$logDirectory . '/' . $this->logFilename;
Debugger::$maxLen = 2000;
        if (file_exists($file)) {
            $json = '[' . file_get_contents($file) . '{}]';
        } else {
            return false;
        }

        if ($asArray) {
            try {
                return Json::decode($json);
            } catch (JsonException $e) {
                return false;
            }
        } else {
            return $json;
        }
    }
}
