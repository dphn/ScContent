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
class Layout extends AbstractMigrationMapper
{
    /**
     * @return void
     */
    public function up()
    {
        $table = $this->createTable('sc_layout')
            ->addColumn(new Column\Integer(
                'id',
                true,
                null,
                ['auto_increment' => true]
            ))
            ->addColumn(new Column\Varchar('theme', 128))
            ->addColumn(new Column\Varchar('region', 128))
            ->addColumn(new Column\Varchar('name', 128))
            ->addColumn(new Column\Varchar('display_name', 255))
            ->addColumn(new Column\Text('description'))
            ->addColumn(new Column\Text('options'))
            ->addColumn(new Column\Integer('position', false))
            ->addConstraint(new Constraint\PrimaryKey('id'));

        $sql = $this->toString($table);
        if ('mysql' == $this->getPlatformName()) {
            $sql .= ' ENGINE=InnoDB CHARSET=utf8';
        }
        $this->execute($sql);

        $this->execute(new CreateIndex(
           'sc_layout',
           CreateIndex::Unique,
           'i_layout',
           ['theme', 'name']
        ));

        $this->execute(new CreateIndex(
            'sc_layout',
            CreateIndex::Key,
            'i_layout_search',
            ['theme', 'region', 'position']
        ));
    }

    /**
     * @return void
     */
    public function down()
    {
        $table = $this->dropTable('sc_layout');
        $this->execute($table);
    }
}
