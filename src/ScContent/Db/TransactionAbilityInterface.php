<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Db;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
interface TransactionAbilityInterface
{
    /**
     * @return boolean
     */
    function beginTransaction();

    /**
     * @return boolean
     */
    function commit();

    /**
     * @return boolean
     */
    function rollBack();

    /**
     * @return string | false
     */
    function getTransactionIdentifier();
}
