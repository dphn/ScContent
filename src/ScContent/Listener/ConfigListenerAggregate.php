<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener;

use ScContent\Module,
    //
    Zend\EventManager\ListenerAggregateInterface,
    Zend\EventManager\EventManagerInterface,
    Zend\ModuleManager\Listener\ConfigListener,
    Zend\ModuleManager\ModuleEvent,
    Zend\Stdlib\ArrayUtils;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ConfigListenerAggregate implements ListenerAggregateInterface
{
    /**
     * @param Zend\EventManager\EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            ModuleEvent::EVENT_LOAD_MODULES_POST,
            [$this, 'mergeSettings'],
            10000
        );
        $this->listeners[] = $events->attach(
            ModuleEvent::EVENT_LOAD_MODULES_POST,
            [$this, 'adjustZfcUser'],
            10000
        );
    }

    /**
     * @param Zend\EventManager\EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param Zend\ModuleManager\ModuleEvent $event
     * @return void
     */
    public function mergeSettings(ModuleEvent $event)
    {
        $module = new Module();
        $baseDir = $module->getDir();
        unset($module);

        $file = $baseDir . DS . 'settings' . DS . 'module.settings.php';

        $settings = include(
            $baseDir . DS . 'settings' . DS . 'module.settings.php'
        );

        $configListener = $event->getConfigListener();
        if (! $configListener instanceof ConfigListener) {
            return;
        }
        $config = $configListener->getMergedConfig(false);

        $config = ArrayUtils::merge($config, $settings);

        $configListener->setMergedConfig($config);
    }

    /**
     * @param Zend\ModuleManager\ModuleEvent $event
     * @return void
     */
    public function adjustZfcUser(ModuleEvent $event)
    {
        $configListener = $event->getConfigListener();
        if (! $configListener instanceof ConfigListener) {
            return;
        }
        $config = $configListener->getMergedConfig(false);

        if (! isset($config['zfcuser'])) {
            $config['zfcuser'] = [];
        }
        $config['zfcuser']['table_name'] = 'sc_users';

        if (isset($config['sc']['frontend_theme_name'])) {
            $theme = $config['sc']['frontend_theme_name'];
            if (isset($config['sc']['themes'][$theme]['zfcuser_template_path'])) {
                $config['view_manager']['template_path_stack']['zfcuser'] = $config['sc']['themes'][$theme]['zfcuser_template_path'];
            }
        }

        $configListener->setMergedConfig($config);
    }
}
