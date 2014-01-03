<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Filter;

use Zend\Filter\AbstractFilter;

/**
 * Filtering result useful only for MySql.
 *
 * @author Dolphin <work.dolphin@gmail.com>
 */
class SimpleStemmingFilter extends AbstractFilter
{
    /**
     * Options for this filter
     *
     * @var array
     */
    protected $options = [
        'minWordLength' => 3,
        'cutLength'     => 3,
    ];

    /**
     * @param integer $length
     * @return void
     */
    public function setMinWordLength($length)
    {
        $this->options['minWordLength'] = (int) $length;
    }

    /**
     * @return integer
     */
    public function getMinWordLength()
    {
        return $this->options['minWordLength'];
    }

    /**
     * @param integer $length
     * @return void
     */
    public function setCutLength($length)
    {
        $this->options['cutLength'] = (int) $length;
    }

    /**
     * @return integer
     */
    public function getCutLength()
    {
        return $this->options['cutLength'];
    }

    /**
     * @param string
     * @return string
     */
    public function filter($value)
    {
        $minWordLength = $this->getMinWordLength();
        $cutLength = $this->getCutLength();
        $values = explode(' ', $value);
        $value = '';
        foreach ($values as $item) {
            $item = trim($item);
            if ('' === $item) {
                continue;
            }
            $item = ltrim($item, '+-><~"');
            $item = rtrim($item, '*"');
            $strLen = mb_strlen($item);
            if ($strLen < $minWordLength) {
                continue;
            }
            $pos = max($minWordLength, $strLen - $cutLength);
            $item = mb_substr($item, 0, $pos);
            $value .= $item . '* ';
        }
        return rtrim($value);
    }
}
