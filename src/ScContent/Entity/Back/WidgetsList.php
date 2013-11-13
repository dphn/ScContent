<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity\Back;

use ScContent\Entity\AbstractList;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetsList extends AbstractList
{
    /**
     * @param ScContent\Entity\WidgetItem $item
     * @return void
     */
    public function addItem(WidgetItem $item)
    {
        $this->items[] = $item;
    }
}
