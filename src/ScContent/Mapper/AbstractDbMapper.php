<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper;

use ScContent\Db\TransactionAbilityInterface,
    ScContent\Mapper\Exception\InvalidArgumentException,
    ScContent\Mapper\Exception\LogicException,
    ScContent\Mapper\Exception\DomainException,
    ScContent\Exception\IoCException,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Adapter\Driver\ResultInterface,
    //
    Zend\Stdlib\Hydrator\ArraySerializable,
    Zend\StdLib\Hydrator\HydratorInterface,
    //
    Zend\Db\Sql\PreparableSqlInterface,
    Zend\Db\Sql\SqlInterface,
    Zend\Db\Sql\Select,
    Zend\Db\Sql\Sql;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractDbMapper
{
    /**
     * @const string
     */
    const JoinInner = Select::JOIN_INNER;

    /**
     * @const string
     */
    const JoinLeft = Select::JOIN_LEFT;

    /**
     * @const string
     */
    const JoinRight = Select::JOIN_RIGHT;

    /**
     * @const integer
     */
    const ReturnTypeEntity = 1;

    /**
     * @const integer
     */
    const ReturnTypeArray = 2;

    /**
     * @var Zend\Db\Adapter\AdapterInterface
     */
    protected $_adapter;

    /**
     * @var Zend\Db\Sql\SqlInterface
     */
    protected $_sql;

    /**
     * @var Zend\Stdlib\HydratorInterface
     */
    protected $_hydrator;

    /**
     * @var array
     */
    protected $_tables = array();

    /**
     * @var boolean
     */
    protected $_initialized = false;

    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @return AbstractDbMapper
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\Db\Adapter\AdapterInterface
     */
    public function getAdapter()
    {
        if (! $this->_adapter instanceof AdapterInterface) {
            throw new IoCException('The adapter was not set.');
        }
        return $this->_adapter;
    }

    /**
     * @param Zend\Db\Sql\SqlInterface $sql
     * @return AbstractDbMapper
     */
    public function setSql(SqlInterface $sql)
    {
        $this->_sql = $sql;
        return $this;
    }

    /**
     * @return Zend\Db\Sql\SqlInterface
     */
    public function getSql()
    {
        if (! $this->_sql instanceof SqlInterface) {
            $this->_sql = new Sql($this->getAdapter());
        }
        return $this->_sql;
    }

    /**
     * @param Zend\Stdlib\HydratorInterface $hydrator
     * @return AbstractDbMapper
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->_hydrator = $hydrator;
        return $this;
    }

    /**
     * @return Zend\Stdlib\HydratorInterface
     */
    public function getHydrator()
    {
        if (! $this->_hydrator instanceof HydratorInterface) {
            $this->_hydrator = new ArraySerializable();
        }
        return $this->_hydrator;
    }

    /**
     * @param string $alias
     * @param string $name
     * @throws ScContent\Mapper\Exception\InvalidArgumentException
     * @return AbstractDbMapper
     */
    public function setTable($alias, $name)
    {
        if (! array_key_exists($alias, $this->_tables)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown table alias '%s'.", $alias
            ));
        }
        $this->_tables[$alias] = $name;
        return $this;
    }

    /**
     * @param string $alias
     * @throws ScContent\Mapper\Exception\InvalidArgumentException
     * @return string
     */
    public function getTable($alias)
    {
        if (! array_key_exists($alias, $this->_tables)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown table alias '%s'.", $alias
            ));
        }
        return $this->_tables[$alias];
    }

    /**
     * @return boolean
     */
    public function beginTransaction()
    {
        return $this->getAdapter()->getDriver()->getConnection()->beginTransaction();
    }

    /**
     * @return boolean
     */
    public function commit()
    {
        return $this->getAdapter()->getDriver()->getConnection()->commit();
    }

    /**
     * @return boolean
     */
    public function rollBack()
    {
        return $this->getAdapter()->getDriver()->getConnection()->rollBack();
    }

    /**
     * @return boolean
     */
    public function inTransaction()
    {
        return $this->getResource()->inTransaction();
    }

    /**
     * @return string | false
     */
    public function getTransactionIdentifier()
    {
        return $this->getResource()->getTransactionIdentifier();
    }

    /**
     * @param string | false $activeTransactionIdentifier
     * @throws ScContent\Mapper\Exception\LogicException
     * $return AbstractDbMapper
     */
    public function checkTransaction($activeTransactionIdentifier)
    {
        if (! $activeTransactionIdentifier) {
            throw new LogicException(
                'Invalid general transaction identifier.'
            );
        }
        $currentTransactionIdentifier = $this->getTransactionIdentifier();
        if (! $currentTransactionIdentifier) {
            throw new LogicException(
                'The current transaction was not started.'
            );
        }
        if ($activeTransactionIdentifier !== $currentTransactionIdentifier) {
            throw new LogicException(sprintf(
                'Current transaction identifier does not match the general identifier the transaction.'
            ));
        }
        return $this;
    }

    /**
     * @return int | null | false
     */
    protected function lastInsertId()
    {
        return $this->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();
    }

    /**
     * @param Zend\Db\Sql\PreparableSqlInterface | string $sql
     * @param Zend\Db\Adapter\ParameterContainer | array $parameters optional
     * @return Zend\Db\Adapter\ResultInterface
     */
    protected function execute($sql, $parameters = null)
    {
        if (is_null($parameters) && $sql instanceof PreparableSqlInterface) {
            return $this->getSql()->prepareStatementForSqlObject($sql)->execute();
        }
        if ($sql instanceof PreparableSqlInterface) {
            $sql = $this->toString($sql);
        }
        $sth = $this->getAdapter()->createStatement($sql, $parameters);
        return $sth->execute();
    }

    /**
     * @param Zend\Db\Adapter\ResultInterface $source
     * @return array
     */
    protected function toArray(ResultInterface $source)
    {
        $result = array();
        foreach ($source as $item) {
            $result[] = $item;
        }
        return $result;
    }

    /**
     * @param Zend\Db\Adapter\ResultInterface $source
     * @param string $key
     * @throws ScContent\Exception\InvalidArgumentException
     * @return array
     */
    protected function toList(ResultInterface $source, $key)
    {
        $result = array();
        foreach ($source as $item) {
            if (! isset($item[$key])) {
                throw new InvalidArgumentException(
                    sprintf("Unknown key '%s'.", $key)
                );
            }
            $result[] = $item[$key];
        }
        return $result;
    }

    /**
     * @param Zend\Db\Sql\PreparableSqlInterface $sqlObject
     * @return string
     */
    protected function toString(SqlInterface $sqlObject)
    {
        return $this->getSql()->getSqlStringForSqlObject($sqlObject);
    }

    /**
     * @param string $identifier
     * @return string
     */
    protected function quoteIdentifier($identifier)
    {
        return $this->getAdapter()->getPlatform()->quoteIdentifier($identifier);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function quoteValue($value)
    {
        return $this->getAdapter()->getPlatform()->quoteValue($value);
    }

    /**
     * @throws ScContent\Mapper\Exception\DomainException
     * @return ScContent\Db\TransactionAbilityInterface
     */
    protected function getResource()
    {
        $resource = $this->getAdapter()->getDriver()->getConnection()->getResource();
        if (! $resource instanceof TransactionAbilityInterface) {
            throw new DomainException(
                "Resource class must implement 'ScContent\Db\TransactionAbilityInterface' interface."
            );
        }
        return $resource;
    }
}
