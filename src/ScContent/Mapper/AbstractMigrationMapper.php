<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper;

use Zend\Db\Sql\Ddl;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractMigrationMapper extends AbstractDbMapper
{
    /**
     * @return void
     */
    abstract function up();

    /**
     * @return void
     */
    abstract function down();

    /**
     * @param string $table
     * @param boolean $isTemporary
     * @return Zend\Db\Sql\Ddl\CreateTable
     */
    public function createTable($table = '', $isTemporary = false)
    {
        return new Ddl\CreateTable($table, $isTemporary);
    }

    /**
     * @param string $table
     * @return Zend\Db\Sql\Ddl\DropTable
     */
    public function dropTable($table = '')
    {
        return new Ddl\DropTable($table);
    }

    /**
     * @param string $table
     * @return Zend\Db\Sql\Ddl\AlterTable
     */
    public function alterTable($table = '')
    {
        return new Ddl\AlterTable($table);
    }
}
