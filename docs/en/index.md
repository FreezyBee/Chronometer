Quickstart
==========


Installation
------------

The best way to install FreezyBee/Chronometer is using  [Composer](http://getcomposer.org/):

```sh
$ composer require freezy-bee/chronometer
```

With Nette `2.3` and newer, you can enable the extension using your neon config.

```yml
extensions:
	chronometer: FreezyBee\Chronometer\DI\ChronometerExtension
```

Full configuration
------------------

```yml
chronometer:
    enable: false # defaul true
    logFilename: test.log # path from Nette Debugger dir - default chronometer.log
    jsonFilename: test.log # path from Nette Debugger dir - default chronometer.json
```

Example
-------

```php

use FreezyBee\Chronometer\Timer;

class InfinityPresenter extends BasePresenter
{
    public function actionCountToInfinity()
    {
        Timer::startMeasure('nameX');

        // some P problem ...

        Timer::startMeasure('nameY');
            
        // some NP-hard problem ...

        Timer::stopMeasure('nameY');        

        // some code ...

        Timer::stopMeasure('nameX');        

        // you can mearuse a time from start a application
        Timer::stopMeasure('measureFromStartApplication');

        // or you can mearuse a time to shutdown a application
        Timer::startMeasure('measureToShutdownApplication');
    }
    ...
}
```

Output from FreezyBee\Chronometer\LogParser

```php

use FreezyBee\Chronometer\LogParser;

class AnalyzatorPresenter extends BasePresenter
{
    /** @var LogParser @inject */
    public $chronometerParser;

    public function renderShow()
    {
        // return json string
        dump($this->chronometerParser->getLog());

        // return array
        dump($this->chronometerParser->getLog(true));

        // generate valid .json file from log
        $this->chronometerParser->generateJsonLogFile()
    }
    ...
}
```

```json
{
    "data": [
        {
            "status": "success",
            "presenter": "Homepage",
            "action": "default",
            "parameters": {
                "action": "default",
                "id": null
            },
            "events": {
                "nameX": 0.002293,
                "nameY": 0.003824
            },
            "totalTime": 0.004799
        },
        {
            ...
        }
    ]
}
```