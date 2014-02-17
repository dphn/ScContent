<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Stdlib
{
    /**
     * @param  integer $length
     * @return string
     */
    public static function randomKey($length = 32)
    {
        $key = '';
        $iterations = ceil($length / 32);
        for ($i = 0; $i < $iterations; $i ++) {
            $rand = '';
            while (strlen($rand < 32)) {
                $rand .= mt_rand(0, mt_getrandmax());
            }
            $key .= md5(uniqid($rand, true));
        }
        return substr($key, 0, $length);
    }
}
