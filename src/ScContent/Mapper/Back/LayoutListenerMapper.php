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

use ScContent\Mapper\AbstractLayoutMapper,
    ScContent\Mapper\Exception\UnavailableSourceException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutListenerMapper extends AbstractLayoutMapper
{
    /**
     * @param  integer $contentId Content identifier
     * @param  boolean $trash Content trash flag
     * @param  string $tid Transaction identifier
     * @return void
     */
    public function unregisterContent($contentId, $trash, $tid)
    {
        $this->checkTransaction($tid);

        $select = $this->getSql()->select()
            ->columns([
                'left_key',
                'right_key',
            ])
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                'id' => $contentId,
                'trash' => (int) $trash
            ]);

        $content = $this->execute($select)->current();
        if (empty($content)) {
            throw new UnavailableSourceException(
                'The content was not found.'
            );
        }

        $sql = sprintf(
            'DELETE FROM
                `%s`
            WHERE `content` IN(
                SELECT
                    `content`.`id`
                FROM
                    `%s` AS `content`
                WHERE
                    `content`.`left_key`  >= :leftKey
                AND
                    `content`.`right_key` <= :rightKey
                AND
                    `content`.`trash` = :trash
            )',
            $this->getTable(self::WidgetsTableAlias),
            $this->getTable(self::ContentTableAlias)
        );

        $this->execute($sql, [
            ':leftKey'     => $content['left_key'],
            ':rightKey'    => $content['right_key'],
            ':trash'       => (int) $trash,
        ]);
    }

    /**
     * @param  string $tid Transaction identifier
     * @return void
     */
    public function unregisterCleanedContent($tid)
    {
        $this->checkTransaction($tid);

        $sql = sprintf(
            'DELETE FROM
                `%s`
            WHERE `content` IN(
                SELECT
                    `content`.`id`
                FROM
                    `%s` AS `content`
                WHERE
                    `content`.`trash` = \'1\'
            )',
            $this->getTable(self::WidgetsTableAlias),
            $this->getTable(self::ContentTableAlias)
        );

        $this->execute($sql);
    }
}
