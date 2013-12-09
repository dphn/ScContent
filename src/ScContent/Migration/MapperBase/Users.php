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
class Users extends AbstractMigrationMapper
{
    /**
     * @return void
     */
    public function up()
    {
        $table = $this->createTable('sc_users')
            ->addColumn(new Column\Integer(
                'user_id',
                true,
                null,
                ['auto_increment' => true]
            ))
            ->addColumn(
                (new Column\Varchar('username', 255))
                    ->setNullable(true)
            )
            ->addColumn(
                (new Column\Varchar('email', 255))
                    ->setNullable(true)
            )
            ->addColumn(
                (new Column\Varchar('display_name', 50))
                    ->setNullable(true)

            )
            ->addColumn(new Column\Varchar('password', 128))
            ->addColumn(new Column\Integer('registered'))
            ->addColumn(
                (new Column\Integer('state'))
                    ->setNullable(true)
            )
            ->addColumn(new Column\Varchar('locale', 64))
            ->addColumn(new Column\Varchar('timezone', 255))
            ->addConstraint(new Constraint\PrimaryKey('user_id'));

        $sql = $this->toString($table);
        if ('mysql' == $this->getPlatformName()) {
            $sql .= ' ENGINE=InnoDB CHARSET=utf8';
        }
        $this->execute($sql);

        $this->execute(new CreateIndex(
           'sc_users',
           CreateIndex::Unique,
           'i_username',
           'username'
        ));

        $this->execute(new CreateIndex(
            'sc_users',
            CreateIndex::Unique,
            'i_email',
            'email'
        ));
    }

    /**
     * @return void
     */
    public function down()
    {
        $table = $this->dropTable('sc_users');
        $this->execute($table);
    }
}
