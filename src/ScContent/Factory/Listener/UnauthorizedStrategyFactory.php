<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Listener;

use ScContent\Listener\UnauthorizedStrategy,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class UnauthorizedStrategyFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Listener\UnauthorizedStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authService = $serviceLocator->get('zfcuser_auth_service');
        $moduleOptions = $serviceLocator->get('ScOptions.ModuleOptions');

        $listener = new UnauthorizedStrategy();

        $listener->setAuthService($authService);
        $theme = $moduleOptions->getFrontendTheme();
        if (isset($theme['access_denied_template'])) {
            $listener->setTemplate($theme['access_denied_template']);
        }

        return $listener;
    }
}
