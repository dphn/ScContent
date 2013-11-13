<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Migration;

use Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Schema implements SchemaInterface
{
    /**
     * @var Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Migrate to the database.
     * @return void
     */
    public function up($dataBase = 'mysql')
    {
        $queries[] = "CREATE TABLE IF NOT EXISTS `sc_content` (
            `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `left_key`    INT NOT NULL,
            `right_key`   INT NOT NULL,
            `level`       INT NOT NULL,
            `trash`       BOOLEAN NOT NULL DEFAULT '0',
            `type`        VARCHAR(32),
            `status`      VARCHAR(32),
            `title`       VARCHAR(255),
            `name`        VARCHAR(255),
            `content`     LONGTEXT,
            `description` TEXT,
            `created`     INT NOT NULL,
            `modified`    INT NOT NULL,
            `author`      INT NOT NULL,
            `editor`      INT NOT NULL,
            `spec`        VARCHAR(255),
            PRIMARY KEY (`id`),
            UNIQUE KEY (`name`),
            KEY (`title`),
            KEY `sets` (`left_key`,`right_key`,`level`,`trash`, `type`),
            KEY `info` (`author`, `editor`, `created`, `modified`)
        )" . (('mysql' == $dataBase) ? 'ENGINE=InnoDB CHARSET=utf8' : '');

        /* Although the new version InnoDb support full text search,
         * this functionality is added for compatibility.
         */
        $queries[] = "CREATE TABLE IF NOT EXISTS `sc_search` (
            `id`          INT UNSIGNED NOT NULL,
            `title`       VARCHAR(255) DEFAULT NULL,
            `description` TEXT,
            `content`     LONGTEXT,
            PRIMARY KEY (`id`),
            FULLTEXT (`title`, `description`, `content`)
        )" . (('mysql' == $dataBase) ? 'ENGINE=MyISAM CHARSET=utf8' : '');

        /* Caching operations to remove files
         */
        $queries[] = "CREATE TABLE IF NOT EXISTS `sc_garbage` (
            `name`     VARCHAR(255),
            `spec`     VARCHAR(255),
            `failures` BOOLEAN NOT NULL DEFAULT '0',
            UNIQUE KEY (`name`)
        )". (('mysql' == $dataBase) ? 'ENGINE=InnoDB CHARSET=utf8' : '');

        /* Registration of all widgets that have ever been shown
         * in the configuration.
         */
        $queries[] = "CREATE TABLE IF NOT EXISTS `sc_layout` (
            `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `theme`    VARCHAR (128),
            `region`   VARCHAR (128),
            `name`     VARCHAR (128),
            `options`  LONGTEXT,
            `position` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`theme`, `region`, `name`)
        )" . (('mysql' == $dataBase) ? 'ENGINE=InnoDB CHARSET=utf8' : '');

        /* Contains the relationship between the existing contents and
         * registered widgets.
         */
        $queries[] = "CREATE TABLE IF NOT EXISTS `sc_widgets` (
            `widget`  INT UNSIGNED NOT NULL,
            `content` INT UNSIGNED NOT NULL,
            `enabled` INT(1) NOT NULL DEFAULT '0',
            UNIQUE KEY (`widget`, `content`)
        )" . (('mysql' == $dataBase) ? 'ENGINE=InnoDB CHARSET=utf8' : '');

        /* It contains information about the users of the system.
         * The module ZfcUser provides the basic functionality.
         */
        $queries[] = "CREATE TABLE IF NOT EXISTS `sc_users` (
            `user_id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `username`     VARCHAR(255) DEFAULT NULL,
            `email`        VARCHAR(255) DEFAULT NULL,
            `display_name` VARCHAR(50)  DEFAULT NULL,
            `password`     VARCHAR(128) NOT NULL,
            `registered`   INT UNSIGNED,
            `state`        SMALLINT UNSIGNED,
            `locale`       VARCHAR(64),
            `timezone`     VARCHAR(255),
            PRIMARY KEY (`user_id`),
            UNIQUE KEY  (`username`),
            UNIQUE KEY  (`email`)
        )"  . (('mysql' == $dataBase) ? 'ENGINE=InnoDB CHARSET=utf8' : '');

        $adapter = $this->adapter;
        foreach($queries as $query) {
            try {
                $adapter->query($query, $adapter::QUERY_MODE_EXECUTE);
            } catch(Exception $e) {
                // @todo
            }
        }
    }

    /**
     * Delete all of the specified tables from the database.
     *
     * @return void
     */
    public function down($dataBase = 'mysql')
    {
        $queries[] = "DROP TABLE IF EXISTS `sc_content`";
        $queries[] = "DROP TABLE IF EXISTS `sc_search`";
        $queries[] = "DROP TABLE IF EXISTS `sc_widgets`";
        $queries[] = "DROP TABLE IF EXISTS `sc_layout`";
        $queries[] = "DROP TABLE IF EXISTS `sc_users`";

        $adapter = $this->adapter;
        foreach($queries as $query) {
            try {
                $adapter->query($query, $adapter::QUERY_MODE_EXECUTE);
            } catch(Exception $e) {
                // @todo
            }
        }
    }
}
