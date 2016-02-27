<?php

namespace FreezyBee\Chronometer;

use Nette\Application\Application;
use Nette\DI\Container;
use Nette\Object;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Tracy\Debugger;

/**
 * Class Timer
 * @package FreezyBee\Chronometer
 */
class Timer extends Object
{
    /**
     * @var string
     */
    private $logFilename;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    static private $events = [];

    /**
     * Timer constructor.
     * @param string $logFilename
     * @param Container $container
     */
    public function __construct($logFilename, Container $container)
    {
        $this->logFilename = $logFilename;
        $this->container = $container;
    }

    /**
     * @param string $name
     */
    public static function startMeasure($name)
    {
        self::$events[$name]['start'] = microtime(true);
    }

    /**
     * @param string $name
     */
    public static function stopMeasure($name)
    {
        self::$events[$name]['stop'] = microtime(true);
    }

    /* *********** internal *********** */

    /**
     * @internal
     */
    public function applicationShutdown()
    {
        $this->generateOutput('success');
    }

    /**
     * @internal
     */
    public function applicationError()
    {
        $this->generateOutput('error');
    }

    /**
     * @param $status
     */
    private function generateOutput($status)
    {
        /** @var Application $application */
        $application = $this->container->getService('application.application');
        $requests = $application->getRequests();

        $presenter = '';
        $action = '';
        $parameters = [];

        foreach ($requests as $request) {
            $presenter = $request->getPresenterName();
            $parameters = $request->getParameters();
            $action = isset($parameters['action']) ? $parameters['action'] : '';
            break;
        }

        $data = [];
        $data['status'] = $status;
        $data['presenter'] = $presenter;
        $data['action'] = $action;
        $data['parameters'] = $parameters;
        $data['events'] = [];

        $startTime = Debugger::$time;
        $stopTime = microtime(true);

        foreach (self::$events as $name => $values) {
            $start = isset($values['start']) ? $values['start'] : $startTime;
            $stop = isset($values['stop']) ? $values['stop'] : $stopTime;
            $data['events'][$name] = round($stop - $start, 6);
        }

        $data['totalTime'] = round($stopTime - $startTime, 6);

        $this->saveOutput($data);
    }

    /**
     * @param array $data
     */
    private function saveOutput(array $data)
    {
        $file = Debugger::$logDirectory . '/' . $this->logFilename;

        try {
            $message = Json::encode($data);
        } catch (JsonException $e) {
            $message = '{"status":"invalid data"}';
        }

        if (!@file_put_contents($file, $message . ',' . PHP_EOL, FILE_APPEND | LOCK_EX)) {
            throw new \RuntimeException("Unable to write to log file '$file'. Is directory writable?");
        }
    }
}
