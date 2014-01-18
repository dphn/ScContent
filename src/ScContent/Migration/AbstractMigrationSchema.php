<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Migration;

use ScContent\Factory\Migration\MapperBuilder,
    ScContent\Exception\IoCException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractMigrationSchema implements SchemaInterface
{
    /**
     * @var ScContent\Factory\Migration\MapperBuilder
     */
    protected $builder;

    /**
     * @return void
     */
    abstract function up();

    /**
     * @return void
     */
    abstract function down();

    /**
     * @param ScContent\Factory\Migration\MapperBuilder $builder
     */
    public function setBuilder(MapperBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Factory\Migration\MapperBuilder
     */
    public function getBuilder()
    {
        if (! $this->builder instanceof MapperBuilder) {
            throw new IoCException(
	           'The builder was not set.'
            );
        }
        return $this->builder;
    }

    /**
     * @param string $name
     * @return ScContent\Mapper\AbstractMigrationMapper
     */
    protected function buildMapper($name)
    {
        $builder = $this->getBuilder();
        $mapper = $builder->make($name);
        return $mapper;
    }
}
