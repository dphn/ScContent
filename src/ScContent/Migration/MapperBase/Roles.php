<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
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
class Roles extends AbstractMigrationMapper
{
    /**
     * @return void
     */
    public function up()
    {
        $table = $this->createTable('sc_roles')
            ->addColumn(new Column\Integer(
                'id',
                true,
                null,
                array('auto_increment' => true)
            ))
            ->addColumn(new Column\Varchar('role_id', 255))
            ->addColumn(new Column\Integer('is_default', false, 0))
            ->addColumn(new Column\Integer('parent_id'))
            ->addConstraint(new Constraint\PrimaryKey('id'));

        $sql = $this->toString($table);
        if ('mysql' == $this->getPlatformName()) {
            $sql .= ' ENGINE=InnoDB CHARSET=utf8';
        }
        $this->execute($sql);

        $this->execute(new CreateIndex(
    	   'sc_roles',
           CreateIndex::Unique,
           'i_unique_role',
           'role_id'
        ));

        $this->execute(new CreateIndex(
    	   'sc_roles',
           CreateIndex::Key,
           'i_parent_id',
           'parent_id'
        ));
    }

    /**
     * @return void
     */
    public function down()
    {
        $table = $this->dropTable('sc_roles');
        $this->execute($table);
    }
}
