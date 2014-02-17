<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Back;

use ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListReorderMapper extends ContentListOperationAbstract
{
    /**
     * @param \Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @param  integer $id
     * @param  integer $position
     * @param  string Transaction identifier
     * @throws \ScContent\Mapper\Exception\UnavailableSourceException
     * @throws \ScContent\Mapper\Exception\UnavailableDestinationException
     * @return void
     */
    public function reorder($id, $position, $tid)
    {
        $this->checkTransaction($tid);

        $position = max(0, $position - 1);
        $source = $this->findMetaById($id);
        if (empty($source)) {
            throw new UnavailableSourceException(
                'Displacement of an element can not be done.'
            );
        }
        if ($source['trash']) {
            throw new UnavailableSourceException(
                'Displacement of an element in the specified location can not be done.'
            );
        }
        $parent = $this->findParent($source);
        $destination = $this->findMetaByPosition($parent, $position);
        if (empty($destination)) {
            $destination = $this->findMetaByMaxBottomPosition($parent);
        }
        if (empty($destination)) {
            throw new UnavailableDestinationException(
                'Displacement of an element can not be done.'
            );
        }

        // If the source and destination are one and the same element.
        if ($source['left_key'] == $destination['left_key']) {
            return;
        }

        if ($source['right_key'] < $destination['left_key']) {
            $topLeft = $source['left_key'];
            $topRight = $source['right_key'];
            $bottomLeft = $destination['left_key'];
            $bottomRight = $destination['right_key'];
        } else {
            $topLeft = $destination['left_key'];
            $topRight = $destination['right_key'];
            $bottomLeft = $source['left_key'];
            $bottomRight = $source['right_key'];
        }
        $skewTree = $bottomRight - $bottomLeft + 1;
        if ($source['right_key'] > $destination['left_key']) {
            $skewEditTop = $skewTree;
            $skewEditBottom = $topLeft - $bottomLeft;
        } else {
            $skewTree = - ($topRight - $topLeft + 1);
            $skewEditTop = $bottomRight - $topRight;
            $skewEditBottom = $skewTree;
        }

        $update = $this->getSql()->update()
            ->table($this->getTable(self::ContentTableAlias))
            ->set([
                'left_key' => new Expression(
                    '`left_key` + @offset :=
                    IF(`left_key` > :topRight AND `right_key` < :bottomLeft,
                        :skewTree,
                        IF(`left_key` < :bottomLeft,
                            :skewEditTop,
                            :skewEditBottom
                        )
                    )'
                ),
                'right_key' => new Expression('`right_key` + @offset'),
            ])
            ->where([
                '`left_key` >= ?' => $topLeft,
                '`left_key` <= ?' => $bottomRight,
                '`trash`     = ?' => 0,
            ]);

        $this->execute($update, [
            ':skewTree'       => $skewTree,
            ':topRight'       => $topRight,
            ':bottomLeft'     => $bottomLeft,
            ':skewEditTop'    => $skewEditTop,
            ':skewEditBottom' => $skewEditBottom,
        ]);
    }

    /**
     * @param  array $parent
     * @param  integer $position
     * @return null|array
     */
    protected function findMetaByPosition($parent, $position)
    {
        $select = $this->getSql()->select()
            ->columns([
                'id', 'left_key', 'right_key', 'level', 'trash'
            ])
            ->from(['content' => $this->getTable(self::ContentTableAlias)])
            ->where([
                '`content`.`left_key`  > ?' => $parent['left_key'],
                '`content`.`right_key` < ?' => $parent['right_key'],
                '`content`.`level`     = ?' => $parent['level'] + 1,
                '`content`.`trash`     = ?' => $parent['trash'],
            ])
            ->order('content.left_key ASC')
            ->limit(1)
            ->offset($position);

        return $this->execute($select)->current();
    }

    /**
     * @param  array $parent
     * @return null|array
     */
    protected function findMetaByMaxBottomPosition($parent)
    {
        $select = $this->getSql()->select()
            ->columns([
                'id', 'left_key', 'right_key', 'level', 'trash'
            ])
            ->from(['content' => $this->getTable(self::ContentTableAlias)])
            ->where([
                'content.right_key' => $parent['right_key'] - 1,
                'content.level'     => $parent['level'] + 1,
                'content.trash'     => $parent['trash'],
            ]);

        return $this->execute($select)->current();
    }
}
