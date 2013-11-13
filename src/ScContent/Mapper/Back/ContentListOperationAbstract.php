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

use ScContent\Mapper\AbstractContentMapper;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class ContentListOperationAbstract extends AbstractContentMapper
{
    /**
     * @param array $meta
     * @return null | array
     */
    protected function findParent($meta)
    {
        if (2 > $meta['level']) {
            return $this->getVirtualRoot($meta['trash']);
        }

        $select = $this->getSql()->select()
            ->columns(array(
                'id', 'left_key', 'right_key', 'level', 'trash', 'type'
            ))
            ->from($this->getTable(self::ContentTableAlias))
            ->where(array(
                '`trash`     = ?' => $meta['trash'],
                '`left_key`  < ?' => $meta['left_key'],
                '`right_key` > ?' => $meta['right_key'],
                '`level`     = ?' => $meta['level'] - 1,
            ));

        return $this->execute($select)->current();
    }
}
