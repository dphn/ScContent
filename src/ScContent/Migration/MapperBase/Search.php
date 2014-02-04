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
 * Although the new version InnoDb support full text search,
 * this functionality is added for compatibility.
 *
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Search extends AbstractMigrationMapper
{
    /**
     * @return void
     */
    public function up()
    {
        $table = $this->createTable('sc_search')
            ->addColumn(new Column\Integer('id', true, null))
            ->addColumn(new Column\Varchar('name', 255))
            ->addColumn(new Column\Varchar('title', 255))
            ->addColumn(new Column\Text('content'))
            ->addColumn(new Column\Text('description'))
            ->addConstraint(new Constraint\PrimaryKey('id'));

        $sql = $this->toString($table);
        if ('mysql' == $this->getPlatformName()) {
            $sql .= ' ENGINE=MyISAM CHARSET=utf8';
        }
        $this->execute($sql);

        $this->execute(new CreateIndex(
            'sc_search',
            CreateIndex::Fulltext,
            'i_search',
            [
                'name',
                'title',
                'description',
                'content',
            ]
        ));
    }

    /**
     * @return void
     */
    public function down()
    {
        $table = $this->dropTable('sc_search');
        $this->execute($table);
    }
}
