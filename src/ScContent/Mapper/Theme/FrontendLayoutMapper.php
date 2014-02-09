<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Theme;

use ScContent\Mapper\AbstractLayoutMapper,
    ScContent\Service\Theme\FrontendRegionsProxy,
    ScContent\Entity\Widget,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Predicate\Predicate;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FrontendLayoutMapper extends AbstractLayoutMapper
{
    /**
     * @var ScContent\Service\Theme\FrontendRegionsProxy
     */
    protected $proxy;

    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @param ScContent\Service\Theme\FrontendRegionsProxy $proxy
     */
    public function __construct(
        AdapterInterface $adapter,
        FrontendRegionsProxy $proxy
    ) {
        $this->setAdapter($adapter);
        $this->proxy = $proxy;
    }

    /**
     * @param string $themeName
     * @param null | integer $contentId Content identifier
     * @return ScContent\Entity\Front\Regions
     */
    public function findRegions($themeName, $contentId)
    {
        $proxy = $this->proxy;

        $select = $this->getSql()->select()
            ->columns(['left_key', 'right_key'])
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                'id'    => $contentId,
                'trash' => 0,
            ]);

        $result = $this->execute($select)->current();

        $leftKey = $result['left_key'];
        $rightKey = $result['right_key'];

        $select = $this->getSql()->select()
            ->from(['layout' => $this->getTable(self::LayoutTableAlias)])
            ->where([
                '`layout`.`theme`   = ?' => $themeName,
                '`layout`.`region` <> ?' => 'none',
                (new Predicate())->expression('? = COALESCE((%s), ?)', [1, 1]),

            ])
            ->order(['region ASC', 'position ASC']);

        $sql = $this->toString($select);

        $select = $this->getSql()->select()
            ->columns(['enabled'])
            ->from(['widgets' => $this->getTable(self::WidgetsTableAlias)])
            ->join(
                ['content' => $this->getTable(self::ContentTableAlias)],
                'content.id = widgets.content',
                [],
                self::JoinLeft
            )
            ->where([
                '`content`.`left_key`  <= ?' => $leftKey,
                '`content`.`right_key` >= ?' => $rightKey,
                '`content`.`trash`      = ?' => 0,
                '`widgets`.`widget` = `layout`.`id`',
            ])
            ->order('content.left_key DESC')
            ->limit(1);

        $correlatedSql = $this->toString($select);
        $sql = sprintf($sql, $correlatedSql);

        $results = $this->execute($sql);

        $hydrator = $this->getHydrator();
        $itemPrototype = new Widget();
        foreach ($results as $result) {
            $item = clone ($itemPrototype);
            $hydrator->hydrate($result, $item);
            $proxy->addItem($item);
        }

        return $proxy->getRegions();
    }
}
