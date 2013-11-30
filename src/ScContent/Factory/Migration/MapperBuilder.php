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

use ScContent\Mapper\AbstractMigrationMapper,
    ScContent\Exception\DomainException,
    //
    Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class MapperBuilder
{
    /**
     * @var Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param string $name
     * @throws ScContent\Exception\DomainException
     * @return ScContent\Mapper\AbstractMigrationMapper
     */
    public function make($name)
    {
        $class = str_replace('.', '\\', $name);
        if (! class_exists($class)) {
            throw new DomainException(sprintf(
	           "The class '%s' was not found.",
               $class
            ));
        }

        $mapper = new $class();
        if (! $mapper instanceof AbstractMigrationMapper) {
            throw new DomainException(sprintf(
                "The mapper class '%s' must inherit from 'ScContent\Mapper\AbstractMigrationMapper'.",
                $class
            ));
        }
        $mapper->setAdapter($this->adapter);

        return $mapper;
    }
}
