<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Back;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
interface ContentListMapperInterface
{
    /**
     * @param string $optionsIdentifier
     * @return ScContent\Entity\Back\ContentList
     */
    function getContent($optionsIdentifier);
}
