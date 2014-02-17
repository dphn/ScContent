<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Db;

use ScContent\Service\Stdlib;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Resource extends \PDO implements TransactionAbilityInterface
{
    /**
     * @var string|false
     */
    protected $transactionIdentifier = false;

    /**
     * @return boolean
     */
    public function beginTransaction()
    {
        $this->transactionIdentifier = Stdlib::randomKey();
        return (bool) parent::beginTransaction();
    }

    /**
     * @return boolean
     */
    public function commit()
    {
        return (bool) parent::commit();
    }

    /**
     * @return boolean
     */
    public function rollBack()
    {
        return (bool) parent::rollBack();
    }

    /**
     * @return string|false
     */
    public function getTransactionIdentifier()
    {
        if (! $this->inTransaction()) {
            $this->transactionIdentifier = false;
        }
        return $this->transactionIdentifier;
    }
}
