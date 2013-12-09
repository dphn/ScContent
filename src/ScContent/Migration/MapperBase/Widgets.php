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
class Widgets extends AbstractMigrationMapper
{
    /**
     * @return void
     */
    public function up()
    {
        $table = $this->createTable('sc_widgets')
            ->addColumn(new Column\Integer('widget', false))
            ->addColumn(new Column\Integer('content', false))
            ->addColumn(new Column\Integer('enabled', false, 0));

        $sql = $this->toString($table);
        if ('mysql' == $this->getPlatformName()) {
            $sql .= ' ENGINE=InnoDB CHARSET=utf8';
        }
        $this->execute($sql);

        $this->execute(new CreateIndex(
            'sc_widgets',
            CreateIndex::Unique,
            'i_widgets',
            ['widget', 'content']
        ));
    }

    /**
     * @return void
     */
    public function down()
    {
        $table = $this->dropTable('sc_widgets');
        $this->execute($table);
    }
}
