<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller;

use ScContent\Options\ModuleOptions,
    //
    Zend\Mvc\Controller\AbstractActionController;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractInstallation extends AbstractActionController
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @return string
     */
    public function getRedirect()
    {
        $redirect = '/';
        $routeMatch = $this->getEvent()->getRouteMatch();
        if ($routeMatch->getParam('redirect')) {
            $redirect = $routeMatch->getParam('redirect');
        }
        if (! $routeMatch->getParam('step')
            || ! $routeMatch->getParam('member')
        ) {
            return $this->redirect()->toUrl($redirect)->setStatusCode(303);
        }
        $options = $this->getModuleOptions()->getInstallation();
        if (isset($options['redirect_on_success'])) {
            $redirect = $this->url()->fromRoute($options['redirect_on_success']);
        }
        return $redirect;
    }

    /**
     * @param ScContent\Options\ModuleOptions $options
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
    }

    /**
     * @return ScContent\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions instanceof ModuleOptions) {
            $serviceLocator = $this->getServiceLocator();
            $this->moduleOptions = $serviceLocator->get('sc-options.module');
        }
        return $this->moduleOptions;
    }
}
