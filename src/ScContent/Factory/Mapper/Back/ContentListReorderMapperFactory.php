<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Mapper\Back;

use ScContent\Mapper\Back\ContentListReorderMapper,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListReorderMapperFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Mapper\Back\ContentListReorderMapper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = $serviceLocator->get('ScDb.Adapter');
        $validatorManager = $serviceLocator->get('ValidatorManager');
        $validator = $validatorManager->get('ScValidator.Mapper.Nesting');
        $mapper = new ContentListReorderMapper($adapter, $validator);
        return $mapper;
    }
}
