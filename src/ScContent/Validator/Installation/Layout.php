<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\Installation;

use ScContent\Options\ModuleOptions,
    ScContent\Mapper\Installation\LayoutMapper,
    ScContent\Exception\IoCException,
    //
    Zend\Validator\Exception\InvalidArgumentException,
    Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Layout extends AbstractValidator
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var ScContent\Mapper\Installation\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * @param ScContent\Options\ModuleOptions $options
     * @return void
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions instanceof ModuleOptions) {
            throw new IoCException('The module options were not set.');
        }
        return $this->moduleOptions;
    }

    /**
     * @param ScContent\Mapper\Installation\LayoutMapper $mapper
     * @return void
     */
    public function setLayoutMapper(LayoutMapper $mapper)
    {
        $this->layoutMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Installation\LayoutMapper
     */
    public function getLayoutMapper()
    {
        if (! $this->layoutMapper instanceof LayoutMapper) {
            throw new IoCException('The layout mapper was not set.');
        }
        return $this->layoutMapper;
    }

    /**
     * @param null $value Not used
     * @return boolean
     */
    public function isValid($value = null)
    {
        $options = $this->getModuleOptions();
        $mapper = $this->getLayoutMapper();

        $requiredWidgets = [];
        $widgets = $options->getWidgets();
        foreach ($widgets as $widgetName => $widget) {
            if (isset($widget['options']['unique'])
                && $widget['options']['unique']
            ) {
                $requiredWidgets[] = $widgetName;
            }
        }

        $result = $mapper->findExistingWidgets(
            $options->getFrontendThemeName(),
            $requiredWidgets
        );
        $missing = array_diff($requiredWidgets, $result);
        if (! empty($missing)) {
            return false;
        }
        return true;
    }
}
