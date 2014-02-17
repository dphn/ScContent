<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Theme;

use ScContent\Options\ModuleOptions,
    ScContent\Exception\IoCException,
    //
    Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\View\Http\ViewManager,
    Zend\Filter\Word\CamelCaseToDash,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractThemeStrategy
{
    /**
     * @const string
     */
    const DefaultTemplatesPath = '{theme}/template/{side}/';

    /**
     * @const string
     */
    const DefaultLayoutsPath = '{theme}/layout/{side}/';

    /**
     * @const string
     */
    const DefaultLayout = 'index';

    /**
     * @var string
     */
    protected static $side = 'backend';

    /**
     * @var \Zend\Mvc\View\Http\ViewManager
     */
    protected $viewManager;

    /**
     * @var \ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var \Zend\Filter\Word\CamelCaseToDash
     */
    protected $inflector;

    /**
     * @param  \Zend\Mvc\View\Http\ViewManager $viewManager
     * @return AbstractThemeStrategy
     */
    public function setViewManager(ViewManager $viewManager)
    {
        $this->viewManager = $viewManager;
        return $this;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \Zend\Mvc\View\Http\ViewManager
     */
    public function getViewManager()
    {
        if (! $this->viewManager instanceof ViewManager) {
            throw new IoCException(
                'The view manager was not set.'
            );
        }
        return $this->viewManager;
    }

    /**
     * @param  \ScContent\Options\ModuleOptions $options
     * @return AbstractThemeStrategy
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
        return $this;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if(!$this->moduleOptions instanceof ModuleOptions) {
            throw new IoCException(
                'The module options were not set.'
            );
        }
        return $this->moduleOptions;
    }

    /**
     * @param \Zend\Mvc\MvcEvent $event
     */
    public abstract function update(MvcEvent $event);

    /**
     * Inject a template into the content view model, if none present
     *
     * @param  \Zend\Mvc\MvcEvent
     * @return AbstractThemeStrategy
     */
    protected function injectContentTemplate(MvcEvent $event)
    {
        $model = $event->getResult();

        if (! $model instanceof ViewModel) {
            return $this;
        }
        if ($model->getTemplate()) {
            return $this;
        }

        $routeMatch   = $event->getRouteMatch();
        $themeName    = $this->getThemeName();
        $themeOptions = $this->getThemeOptions();

        $options = [];
        if (isset($themeOptions[static::$side])) {
            $options = $themeOptions[static::$side];
        }

        if (isset($options['templates'])) {
            $templatesPath = rtrim($options['templates'], '/') . '/';
        } else {
            $templatesPath = str_replace(
                ['{theme}', '{side}'],
                [$themeName, static::$side],
                self::DefaultTemplatesPath
            );
        }

        $controller = $event->getTarget();
        if ($controller) {
            $controller = get_class($controller);
        } else {
            $controller = $routeMatch->getParam('controller', '');
        }
        $controller = $this->deriveControllerClass($controller);
        $template = $templatesPath . $this->inflectName($controller);

        $action  = $routeMatch->getParam('action');
        if (null !== $action) {
            $template .= '/' . $this->inflectName($action);
        }

        $model->setTemplate($template);

        return $this;
    }

    /**
     * Inject a template into the layout view model, if none present
     *
     * @param  \Zend\Mvc\MvcEvent
     * @return AbstractThemeStrategy
     */
    protected function injectLayoutTemplate(MvcEvent $event)
    {
        $model = $event->getResult();

        if (! $model instanceof ViewModel) {
            return $this;
        }
        if ($model->terminate()) {
            return $this;
        }

        $viewManager  = $this->getViewManager();
        $renderer     = $viewManager->getRenderer();

        $themeName    = $this->getThemeName();
        $themeOptions = $this->getThemeOptions();

        $options = [];
        if (isset($themeOptions[static::$side])) {
            $options = $themeOptions[static::$side];
        }

        if (isset($options['layouts'])) {
            $layoutsPath = rtrim($options['layouts'], '/') . '/';
        } else {
            $layoutsPath = str_replace(
                ['{theme}', '{side}'],
                [$themeName, static::$side],
                self::DefaultLayoutsPath
            );
        }

        $layout = $event->getViewModel();
        if ($layout && 'layout/layout' !== $layout->getTemplate()) {
            $layout->setTemplate(
                str_replace('{layouts}', $layoutsPath, $layout->getTemplate())
            );
            return $this;
        }

        $defaultLayout = self::DefaultLayout;
        if(isset($options['default_layout'])) {
            $defaultLayout = $options['default_layout'];
        }
        $renderer->layout()->setTemplate($layoutsPath . $defaultLayout);

        return $this;
    }

    /**
     * @return string
     */
    protected function getThemeName()
    {
        $moduleOptions  = $this->getModuleOptions();
        if (static::$side === self::$side) {
            return $moduleOptions->getBackendThemeName();
        }
        return $moduleOptions->getFrontendThemeName();
    }

    /**
     * @return array
     */
    protected function getThemeOptions()
    {
        $moduleOptions  = $this->getModuleOptions();
        if (static::$side === self::$side) {
            return $moduleOptions->getBackendTheme();
        }
        return $moduleOptions->getFrontendTheme();
    }

    /**
     * @param  string $name
     * @return string
     */
    protected function inflectName($name)
    {
        if(!$this->inflector instanceof CamelCaseToDash) {
            $this->inflector = new CamelCaseToDash();
        }
        $name = $this->inflector->filter($name);
        return strtolower($name);
    }

    /**
     * @param  string|object $controller
     * @return string
     */
    protected function deriveControllerClass($controller)
    {
        if(is_object($controller)) {
            $controller = get_class($controller);
        }

        if(strstr($controller, '\\')) {
            $controller = substr($controller, strrpos($controller, '\\') + 1);
        }

        if((10 < strlen($controller))
            && ('Controller' == substr($controller, -10))
        ) {
            $controller = substr($controller, 0, -10);
        }

        return $controller;
    }
}
