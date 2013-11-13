<?php
namespace ScContent\Controller\Back;

use ScContent\Controller\AbstractBack,
    ScContent\Service\Back\LayoutService,
    ScContent\Options\ModuleOptions,
    ScContent\Entity\Back\Theme,
    ScContent\Exception\RuntimeException,
    //
    Zend\View\Model\ViewModel;

class LayoutController extends AbstractBack
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var ScContent\Service\Back\LayoutService
     */
    protected $layoutService;

    /**
     * @var ScContent\Entity\Back\Theme
     */
    protected $theme;

    /**
     * Shows a layout.
     *
     * @return Zend\Stdlib\ResponseInterface | Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $service = $this->getLayoutService();
        $moduleOptions = $this->getModuleOptions();
        try {
            $theme = $this->getTheme($this->params()->fromRoute('theme'));
        } catch (RuntimeException $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
            return $this->redirect()
                ->toRoute('sc-admin/themes')
                ->setStatusCode(303);
        }
        if (! $service->isThemeRegistered($theme->getName())) {
            $this->flashMessenger()->addMessage(sprintf(
                $this->translate("Theme '%s' was not enabled."),
                $theme->getName()
            ));

            return $this->redirect()
                ->toRoute('sc-admin/themes')
                ->setStatusCode(303);
        }

        return new ViewModel (array(
            'regions' => $service->getRegions($theme->getName()),
            'theme' => $theme
        ));
    }

    /**
     * @param ScContent\Options\ModuleOptions $options
     * @return void
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

    /**
     * @param ScContent\Service\Back\LayoutService $service
     * @return void
     */
    public function setLayoutService(LayoutService $service)
    {
        $this->layoutService = $service;
    }

    /**
     * @return ScContent\Service\Back\LayoutService
     */
    public function getLayoutService()
    {
        if (! $this->layoutService instanceof LayoutService) {
            $serviceLocator = $this->getServiceLocator();
            $this->layoutService = $serviceLocator->get(
                'sc-service.back.layout'
            );
        }
        return $this->layoutService;
    }

    /**
     * @param ScContent\Entity\Back\Theme $theme
     * @return void
     */
    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * @param string $name
     * @return ScContent\Entity\Back\Theme
     */
    public function getTheme($name = null)
    {
        if (! $this->theme instanceof Theme) {
            $moduleOptions = $this->getModuleOptions();
            $theme = new Theme();
            if (empty($name)) {
                $name = $moduleOptions->getBackendThemeName();
            }
            $themes = $moduleOptions->getThemes();
            if (! isset($themes[$name])) {
                throw new RuntimeException(sprintf(
                    $this->translate("Unknown theme '%s'."), $name
                ));
            }
            $options = $themes[$name];
            $theme->exchangeArray($options);
            $theme->setName($name);
            $this->theme = $theme;
        }
        return $this->theme;
    }
}
