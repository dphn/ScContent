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

use ScContent\Exception\DomainException,
    //
    Traversable;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AbstractEntity implements EntityInterface
{
    /**
     * @param array | Traversable $data
     * @throws ScContent\Exception\DomainException
     * @return AbstractEntity
     */
    public function exchangeArray($data)
    {
        if (! is_array($data) && ! $data instanceof Traversable) {
            throw new DomainException(sprintf(
                'Data provided to %s must be an %s or %s',
                __METHOD__, 'array', 'Traversable'
            ));
        }
        foreach ($data as $key => $value) {
            $setter = 'set' . str_replace(
                ' ', '', ucwords(str_replace('_', ' ', $key))
            );
            if (! method_exists($this, $setter)) {
                continue;
            }
            $this->{$setter}($value);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $array = [];
        $transform = function ($letters) {
            $letter = array_shift($letters);
            return '_' . strtolower($letter);
        };
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if ('getarraycopy' == strtolower($method)) {
                continue;
            }
            if (substr($method, 0, 3) != 'get') {
                continue;
            }
            $key = lcfirst(substr($method, 3));
            $normalizedKey = preg_replace_callback(
                '/([A-Z])/', $transform, $key
            );
            $value = $this->{$method}();
            $array[$normalizedKey] = $value;
        }
        return $array;
    }
}
