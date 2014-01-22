<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Theme;

use ScContent\Entity\Front\Regions,
    ScContent\Options\ModuleOptions,
    ScContent\Entity\WidgetInterface,
    ScContent\Exception\IoCException,
    //
    BjyAuthorize\Provider\Identity\ProviderInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FrontendRegionsProxy
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var BjyAuthorize\Provider\Identity\ProviderInterface
     */
    protected $identityProvider;

    /**
     * @var ScContent\Entity\Front\Regions
     */
    protected $regions;

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
            throw new IoCException(
                'The module options was not set.'
            );
        }
        return $this->moduleOptions;
    }

    /**
     * @param BjyAuthorize\Provider\Identity\ProviderInterface $provider
     * @return void
     */
    public function setIdentityProvider(ProviderInterface $provider)
    {
        $this->identityProvider = $provider;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return BjyAuthorize\Provider\Identity\ProviderInterface
     */
    public function getIdentityProvider()
    {
        if (! $this->identityProvider instanceof ProviderInterface) {
            throw new IoCException(
                'The identity provider was not set.'
            );
        }
        return $this->identityProvider;
    }

    /**
     * @param ScContent\Entity\Front\Regions $regions
     * @return void
     */
    public function setRegions(Regions $regions)
    {
        $this->regions = $regions;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Entity\Front\Regions
     */
    public function getRegions()
    {
        if (! $this->regions instanceof Regions) {
            throw new IoCException(
                'The regions was not set.'
            );
        }
        return $this->regions;
    }

    /**
     * @param ScContent\Entity\WidgetInterface $widget
     * @return void
     */
    public function addItem(WidgetInterface $widget)
    {
        $moduleOptions = $this->getModuleOptions();
        if (! $moduleOptions->regionExists(
            $widget->getTheme(),
            $widget->getRegion()
        )) {
            return;
        }

        if (! $moduleOptions->widgetExists($widget->getName())) {
            return;
        }

        $identityProvider = $this->getIdentityProvider();
        foreach ($identityProvider->getIdentityRoles() as $role) {
            if ($widget->isApplicable($role)) {
                $this->getRegions()->addItem($widget);
                break;
            }
        }
    }
}
