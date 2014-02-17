<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller\Back;

use ScContent\Controller\AbstractBack,
    ScContent\Service\Back\ThemeService,
    ScContent\Options\ModuleOptions,
    //
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ThemeController extends AbstractBack
{
    /**
     * @var \ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var \ScContent\Service\Back\ThemeService
     */
    protected $themeService;

    /**
     * Show list of themes.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel();
        $service = $this->getThemeService();
        $view->registeredThemes = $service->getRegisteredThemes();
        $view->options = $this->getModuleOptions();
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $view->messages = $flashMessenger->getMessages();
        }
        return $view;
    }

    /**
     * Enable theme.
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function enableAction()
    {
        $themeName = $this->params()->fromRoute('theme');
        if (! $themeName) {
            $this->flashMessenger()->addMessage(
                $this->scTranslate('Theme was not specified.')
            );
            return $this->redirect()
                ->toRoute('sc-admin/theme')
                ->setStatusCode(303);
        }

        $service = $this->getThemeService();
        if (! $service->enableTheme($themeName)) {
            foreach ($service->getMessages() as $message) {
                $this->flashMessenger()->addMessage($message);
            }
        }
        return $this->redirect()->toRoute('sc-admin/theme');
    }

    /**
     * Disable theme.
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function disableAction()
    {
        $themeName = $this->params()->fromRoute('theme');
        if (! $themeName) {
            $this->flashMessenger()->addMessage(
                $this->scTranslate('Theme was not specified.')
            );
            return $this->redirect()
                ->toRoute('sc-admin/theme')
                ->setStatusCode(303);
        }
        $service = $this->getThemeService();
        if (! $service->disableTheme($themeName)) {
            foreach ($service->getMessages() as $message) {
                $this->flashMessenger()->addMessage($message);
            }
        }

        return $this->redirect()->toRoute('sc-admin/theme');
    }

    /**
     * Set the default theme.
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function defaultAction()
    {
        $themeName = $this->params()->fromRoute('theme');
        if (! $themeName) {
            $this->flashMessenger()->addMessage(
                $this->scTranslate('Theme was not specified.')
            );
            return $this->redirect()
                ->toRoute('sc-admin/theme')
                ->setStatusCode(303);
        }
        $side = $this->params()->fromRoute('side', 'frontend');
        $service = $this->getThemeService();
        if (! $service->setDefault($themeName, $side)) {
            foreach ($service->getMessages() as $message) {
                $this->flashMessenger()->addMessage($message);
            }
        }
        return $this->redirect()->toRoute('sc-admin/theme');
    }

    /**
     * @param \ScContent\Options\ModuleOptions $options
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
    }

    /**
     * @return \ScContent\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions instanceof ModuleOptions) {
            $serviceLocator = $this->getServiceLocator();
            $this->moduleOptions = $serviceLocator->get(
                'ScOptions.ModuleOptions'
            );
        }
        return $this->moduleOptions;
    }

    /**
     * @param  \ScContent\Service\Back\ThemeService $service
     * @return void
     */
    public function setThemeService(ThemeService $service)
    {
        $this->themeService = $service;
    }

    /**
     * @return \ScContent\Service\Back\ThemeService
     */
    public function getThemeService()
    {
        if (! $this->themeService instanceof ThemeService) {
            $serviceLocator = $this->getServiceLocator();
            $this->themeService = $serviceLocator->get(
                'ScService.Back.Theme'
            );
        }
        return $this->themeService;
    }
}
