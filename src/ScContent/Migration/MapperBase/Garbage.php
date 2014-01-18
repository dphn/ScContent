<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Migration\MapperBase;

use ScContent\Mapper\AbstractMigrationMapper,
    ScContent\Db\Sql\Ddl\CreateIndex,
    //
    Zend\Db\Sql\Ddl\Constraint,
    Zend\Db\Sql\Ddl\Column;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Garbage extends AbstractMigrationMapper
{
    /**
     * @return void
     */
    public function up()
    {
        $table = $this->createTable('sc_garbage')
            ->addColumn(new Column\Varchar('name', 255))
            ->addColumn(new Column\Varchar('spec', 255))
            ->addColumn(new Column\Integer('failures', false, 0))
            ->addConstraint(new Constraint\UniqueKey('name', 'i_name'));

        $sql = $this->toString($table);
        if ('mysql' == $this->getPlatformName()) {
            $sql .= ' ENGINE=InnoDB CHARSET=utf8';
        }
        $this->execute($sql);
    }

    /**
     * @return void
     */
    public function down()
    {
        $table = $this->dropTable('sc_garbage');
        $this->execute($table);
    }
}
