<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Migration;

use ScContent\Migration\Schema,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class SchemaFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Migration\Schema
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        static $builder;
        if (! $builder) {
            $adapter = $serviceLocator->get('ScDb.Adapter');
            $builder = new MapperBuilder($adapter);
        }

        $schema = new Schema();
        $schema->setBuilder($builder);
        return $schema;
    }
}
