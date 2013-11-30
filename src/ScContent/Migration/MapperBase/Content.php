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
class Content extends AbstractMigrationMapper
{
    /**
     * @return void
     */
    public function up()
    {
        $table = $this->createTable('sc_content')
            ->addColumn(new Column\Integer(
               'id',
               true,
               null,
               array('auto_increment' => true)
            ))
            ->addColumn(new Column\Integer('left_key', false))
            ->addColumn(new Column\Integer('right_key', false))
            ->addColumn(new Column\Integer('level', false))
            ->addColumn(new Column\Integer('trash', false, 0))
            ->addColumn(new Column\Varchar('type', 32))
            ->addColumn(new Column\Varchar('status', 32))
            ->addColumn(new Column\Varchar('title', 255))
            ->addColumn(new Column\Varchar('name', 255))
            ->addColumn(new Column\Text('content'))
            ->addColumn(new Column\Text('description'))
            ->addColumn(new Column\Integer('created', false))
            ->addColumn(new Column\Integer('modified', false))
            ->addColumn(new Column\Integer('author', false))
            ->addColumn(new Column\Integer('editor', false))
            ->addColumn(new Column\Varchar('spec', 255))
            ->addConstraint(new Constraint\PrimaryKey('id'));

        $sql = $this->toString($table);
        if ('mysql' == $this->getPlatformName()) {
            $sql .= ' ENGINE=InnoDB CHARSET=utf8';
        }
        $this->execute($sql);

        $this->execute(new CreateIndex(
           'sc_content',
            CreateIndex::Unique,
            'i_name',
            'name'
        ));

        $this->execute(new CreateIndex(
           'sc_content',
            CreateIndex::Key,
            'i_title',
            'title'
        ));

        $this->execute(new CreateIndex(
           'sc_content',
            CreateIndex::Key,
            'i_sets',
            array(
                'left_key',
                'right_key',
                'level',
                'trash',
                'type',
            )
        ));

        $this->execute(new CreateIndex(
            'sc_content',
            CreateIndex::Key,
            'i_info',
            array(
                'author',
                'editor',
                'created',
                'modified',
            )
        ));
    }

    /**
     * @return void
     */
    public function down()
    {
        $table = $this->dropTable('sc_content');
        $this->execute($table);
    }
}
