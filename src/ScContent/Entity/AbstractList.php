<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractList implements \Iterator, \Countable
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @return array
     */
    public function rewind()
    {
        return reset($this->items);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * @return mixed
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return ! is_null(key($this->items));
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->items);
    }
}
