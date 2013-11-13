<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service\Back;

use ScContent\Service\Back\ContentListProvider,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListProviderFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Back\ContentListProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $optionsProvider = $serviceLocator->get(
            'sc-service.back.content.list.options.provider'
        );
        $searchMapper = $serviceLocator->get('sc-mapper.back.content.search');
        $listMapper = $serviceLocator->get('sc-mapper.back.content.list');

        $service = new ContentListProvider();

        $service->setOptionsProvider($optionsProvider);
        $service->setMapper($searchMapper, 'search');
        $service->setMapper($listMapper, 'list');

        return $service;
    }
}
