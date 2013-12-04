<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetsList extends AbstractList
{
    /**
     * @param WidgetItem $item
     * @return void
     */
    public function addItem(WidgetItem $item)
    {
        $this->items[] = $item;
    }
}
