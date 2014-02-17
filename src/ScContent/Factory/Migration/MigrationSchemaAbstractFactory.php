<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Migration;

use Zend\ServiceManager\AbstractFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class MigrationSchemaAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param  string $name
     * @param  string $requestedName
     * @return boolean
     */
    public function canCreateServiceWithName(
        ServiceLocatorInterface $serviceLocator,
        $name,
        $requestedName
    ) {
        if (false === strpos(strtolower($requestedName), 'migration')) {
            return false;
        }

        $class = str_replace('.', '\\', $requestedName);

        if (! class_exists($class)) {
            return false;
        }

        if (! is_subclass_of(
            $class, 'ScContent\Migration\AbstractMigrationSchema'
        )) {
            return false;
        }

        return true;
    }

    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return ScContent\Migration\AbstractMigrationSchema
     */
    public function createServiceWithName(
        ServiceLocatorInterface $serviceLocator,
        $name,
        $requestedName
    ) {
        static $builder;
        if (! $builder) {
            $adapter = $serviceLocator->get('ScDb.Adapter');
            $builder = new MapperBuilder($adapter);
        }
        $class = str_replace('.', '\\', $requestedName);
        $schema = new $class();
        $schema->setBuilder($builder);
        return $schema;
    }
}
