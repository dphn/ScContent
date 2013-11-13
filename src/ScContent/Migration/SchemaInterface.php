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
interface SchemaInterface
{
    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     */
    function __construct(AdapterInterface $adapter);

    /**
     * Migrate to the database.
     *
     * @param string $dataBase
     * @return void
     */
    function up($dataBase = 'mysql');

    /**
     * Delete all of the specified tables from the database.
     *
     * @param string $dataBase
     * @return void
     */
    function down($dataBase = 'mysql');

    // @todo version control
}
