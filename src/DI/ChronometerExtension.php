<?php

namespace FreezyBee\Chronometer\DI;

use Nette;
use Nette\DI\CompilerExtension;

/**
 * Class ChronometerExtension
 * @package FreezyBee\Chronometer\DI
 */
class ChronometerExtension extends CompilerExtension
{
    /**
     * @var array
     */
    private $defaults = [
        'enable' => true,
        'logFilename' => 'chronometer.log',
        'jsonFilename' => 'chronometer.json'
    ];

    /**
     *
     */
    public function loadConfiguration()
    {
        $config = $this->getConfig($this->defaults);
        
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('timer'))
            ->setClass('FreezyBee\Chronometer\Timer')
            ->setArguments([$config['logFilename']]);

        $builder->addDefinition($this->prefix('parser'))
            ->setClass('FreezyBee\Chronometer\LogParser')
            ->setArguments([$config['logFilename'], $config['jsonFilename']]);

        if (!$config['enable']) {
            return;
        }
        
        $builder->getDefinition('application')
            ->addSetup('$service->onShutdown[] = ?', [[$this->prefix('@timer'), 'applicationShutdown']])
            ->addSetup('$service->onError[] = ?', [[$this->prefix('@timer'), 'applicationError']]);
    }
}
