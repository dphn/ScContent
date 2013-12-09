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

use ScContent\Mapper\Exception\UnavailableSourceException,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListDeleteMapper extends ContentListOperationAbstract
{
    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @param integer $id Content identifier
     * @param string $tid Transaction identifier
     * @throws ScContent\Mapper\Exception\UnavailableSourceException
     * @return void
     */
    public function delete($id, $tid)
    {
        $this->checkTransaction($tid);

        $meta = $this->findMetaById($id);
        if (empty($meta)) {
            throw new UnavailableSourceException(
                'Content was not found.'
            );
        }
        if (! $meta['trash']) {
            throw new UnavailableSourceException(
                 'Content is not in the trash.'
            );
        }
        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                'left_key  >= ?' => $meta['left_key'],
                'right_key <= ?' => $meta['right_key'],
                'trash      = ?' => 1,
            ]);

        $this->execute($delete);

        $update = $this->getSql()->update()
            ->table($this->getTable(self::ContentTableAlias))
            ->set([
                'left_key' => new Expression(
                    'IF(`left_key` > :leftKey,
                        `left_key` - :skewTree,
                         `left_key`
                    )'
                ),
                'right_key' => new Expression(
                    '`right_key` - :skewTree'
                ),
            ])
            ->where([
                '`right_key` > ?' => $meta['right_key'],
                '`trash`     = ?' => 1
            ]);

        $this->execute($update, [
            ':leftKey'  => $meta['left_key'],
            ':skewTree' => $meta['right_key'] - $meta['left_key'] + 1
        ]);
    }
}
