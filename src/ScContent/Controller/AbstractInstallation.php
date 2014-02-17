<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller;

use ScContent\Listener\Installation\InstallationInspector,
    //
    Zend\Mvc\Controller\AbstractActionController;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractInstallation extends AbstractActionController
{
    /**
     * @var \ScContent\Service\Installation\InstallationInspector
     */
    protected $installationInspector;

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
        $installationInspector = $this->getInstallationInspector();

        $options = $installationInspector->getCurrentSetup();
        if (isset($options['redirect_on_success'])) {
            $redirect = $this->url()->fromRoute($options['redirect_on_success']);
        }
        return $redirect;
    }

    /**
     * @param  \ScContent\Listener\Installation\InstallationInspector $service
     * @return void
     */
    public function setInstallationInspector(InstallationInspector $service)
    {
        $this->installationInspector = $service;
    }

    /**
     * @return \ScContent\Listener\Installation\InstallationInspector
     */
    public function getInstallationInspector()
    {
        if (! $this->installationInspector instanceof InstallationInspector) {
            $serviceLocator = $this->getServiceLocator();
            $this->installationInspector = $serviceLocator->get(
                'ScListener.Installation.Inspector'
            );
        }
        return $this->installationInspector;
    }
}
