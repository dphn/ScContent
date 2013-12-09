<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\Installation;

use Zend\Validator\Exception\InvalidArgumentException,
    Zend\Validator\AbstractValidator,
    Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Migration extends AbstractValidator
{
    /**
     * @var array
     */
    protected $tables = [];

    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @return void
     */
    public function __construct(AdapterInterface $adapter)
    {
        $result = $adapter->query('SHOW TABLES', $adapter::QUERY_MODE_EXECUTE);
        foreach ($result as $set) {
            $this->tables[] = current($set);
        }
    }

    /**
     * @param array $options
     * @throws Zend\Validator\Exception\InvalidArgumentException
     * @return boolean
     */
    public function isValid($options)
    {
        if (! isset($options['tables'])) {
            throw new InvalidArgumentException("Missing option 'tables'.");
        }
        foreach ($options['tables'] as $table) {
            if (! in_array($table, $this->tables)) {
                return false;
            }
        }
        return true;
    }
}
