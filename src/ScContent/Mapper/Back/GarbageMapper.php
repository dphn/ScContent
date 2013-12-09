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

use ScContent\Mapper\AbstractDbMapper,
    ScContent\Mapper\Exception\UnavailableSourceException,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class GarbageMapper extends AbstractDbMapper
{
    /**
     * @const string
     */
    const GarbageTableAlias = 'garbagealias';

    /**
     * @const string
     */
    const ContentTableAlias = 'contentalias';

    /**
     * @var array
     */
    protected $_tables = [
        self::GarbageTableAlias => 'sc_garbage',
        self::ContentTableAlias => 'sc_content',
    ];

    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @param integer $limit
     * @param string $tid Transaction identifier
     * @return array
     */
    public function findGarbage($limit, $tid)
    {
        $this->checkTransaction($tid);

        $select = $this->getSql()->select()
            ->from($this->getTable(self::GarbageTableAlias))
            ->where(['failures' => 0])
            ->limit($limit);

        $result = $this->execute($select);
        return $this->toArray($result);
    }

    /**
     * @param array $list
     * @param string $tid Transaction identifier
     * @return void
     */
    public function registerFailures($list, $tid)
    {
        $this->checkTransaction($tid);

        $update = $this->getSql()->update()
            ->table($this->getTable(self::GarbageTableAlias))
            ->set(['failures' => 1])
            ->where(['name' => $list]);

        $this->execute($update);
    }

    /**
     * @param array $list
     * $param string $tid Transaction identifier
     * @return void
     */
    public function delete($list, $tid)
    {
        $this->checkTransaction($tid);

        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::GarbageTableAlias))
            ->where(['name' => $list]);

        $this->execute($delete);
    }

    /**
     * @param string $tid Transaction identifier
     * @return integer
     */
    public function getGarbageAmount($tid)
    {
        $this->checkTransaction($tid);

        $select = $this->getSql()->select()
            ->columns(['total' => new Expression('COUNT(`name`)')])
            ->from($this->getTable(self::GarbageTableAlias))
            ->where([
                'failures' => 0,
            ]);

        $result = $this->execute($select)->current();
        return (int) $result['total'];
    }

    /**
     * @param integer $contentId Content identifier
     * @param string $tid Transaction identifier
     * @throws ScContent\Mapper\Exception\UnavailableSourceException
     * @return void
     */
    public function registerRemovedGarbage($contentId, $tid)
    {
        $select = $this->getSql()->select()
            ->columns(['left_key', 'right_key'])
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                'id' => $contentId,
                'trash' => 1
            ]);

        $content = $this->execute($select)->current();
        if(empty($content)) {
            throw new UnavailableSourceException(
                'The content was not found.'
            );
        }

        $sql = sprintf(
            'INSERT INTO
                `%s`
            (`name`, `spec`)
            SELECT
                `name`, `spec`
            FROM
                `%s` AS `content`
            WHERE
                `content`.`left_key`  >= :leftKey
            AND
                `content`.`right_key` <= :rightKey
            AND
                `content`.`type` = \'file\'
            AND
                `content`.`trash` = \'1\'',
            $this->getTable(self::GarbageTableAlias),
            $this->getTable(self::ContentTableAlias)
        );

        $this->execute($sql, [
            ':leftKey'  => $content['left_key'],
            ':rightKey' => $content['right_key'],
        ]);
    }

    /**
     * @param string $tid Transaction identifier
     * @return void
     */
    public function registerCleanedGarbage($tid)
    {
        $sql = sprintf(
            'INSERT INTO
                `%s`
            (`name`, `spec`)
            SELECT
                `name`, `spec`
            FROM
                `%s` AS `content`
            WHERE
                `content`.`type` = \'file\'
            AND
                `content`.`trash` = \'1\'',
            $this->getTable(self::GarbageTableAlias),
            $this->getTable(self::ContentTableAlias)
        );

        $this->execute($sql);
    }
}
