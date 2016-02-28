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
     * @var string
     */
    private $jsonFilename;

    /**
     * LogParser constructor.
     * @param $logFilename
     * @param $jsonFilename
     */
    public function __construct($logFilename, $jsonFilename)
    {
        $this->logFilename = $logFilename;
        $this->jsonFilename = $jsonFilename;
    }

    /**
     * @param bool $asArray
     * @return array|string|false
     */
    public function getLog($asArray = false)
    {
        $json = $this->loadLogData();

        if ($json === false) {
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

    /**
     *
     */
    public function generateJsonLogFile()
    {
        $file = Debugger::$logDirectory . '/' . $this->jsonFilename;

        $json = $this->loadLogData();

        try {
            Json::decode($json);
        } catch (JsonException $e) {
            $json = '{"status": "invalid json"}';
        }

        if (!@file_put_contents($file, $json, LOCK_EX)) {
            throw new \RuntimeException("Unable to write to json file '$file'. Is directory writable?");
        }
    }

    /**
     * @return string|false
     */
    private function loadLogData()
    {
        $file = Debugger::$logDirectory . '/' . $this->logFilename;

        if (file_exists($file)) {
            $string = file_get_contents($file);
            if ($string === false) {
                return false;
            } else {
                return '{"data": [' . rtrim($string, "\r\n ,") . ']}';
            }
        } else {
            return false;
        }

    }
}
