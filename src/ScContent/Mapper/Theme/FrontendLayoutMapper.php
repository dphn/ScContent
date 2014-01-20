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
    ScContent\Options\ModuleOptions,
    ScContent\Entity\Front\Regions,
    ScContent\Entity\Widget,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FrontendLayoutMapper extends AbstractLayoutMapper
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var ScContent\Entity\Front\Regions
     */
    protected $regions;

    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @param ScContent\Options\ModuleOptions  $options
     */
    public function __construct(
        AdapterInterface $adapter,
        ModuleOptions $options,
        Regions $regions
    ) {
        $this->setAdapter($adapter);
        $this->moduleOptions = $options;
        $this->regions = $regions;
    }

    /**
     * @return ScContent\Entity\Front\Regions
     */
    public function findRegions($contentId)
    {
        $list = $this->regions;
        $moduleOptions = $this->moduleOptions;
        $theme = $moduleOptions->getFrontendTheme();
        $themeName = $moduleOptions->getFrontendThemeName();

        $widgets = $moduleOptions->getWidgets();
        if (! is_array($widgets)) {
            return $list;
        }

        if (! isset($theme['frontend']['regions'])
            || ! is_array($theme['frontend']['regions'])
        ) {
            return $list;
        }
        $regions = $theme['frontend']['regions'];
        $availableRegions = array_keys($regions);
        $availableWidgets = array_keys($widgets);

        //$this->execute('set profiling=1');

        $select = $this->getSql()->select()
            ->columns(['left_key', 'right_key'])
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                'id' => $contentId,
            ]);

        $result = $this->execute($select)->current();

        $leftKey = $result['left_key'];
        $rightKey = $result['right_key'];

        $select = $this->getSql()->select()
            ->from(['layout' => $this->getTable(self::LayoutTableAlias)])
            ->where([
                'layout.theme'  => $themeName,
                'layout.region' => $availableRegions,
                'layout.name'   => $availableWidgets,
                '(NOT EXISTS (%s) OR 1 = (%s))',

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
                '`widgets`.`widget` = `layout`.`id`'
            ])
            ->order('content.left_key DESC')
            ->limit(1);

        $sql = sprintf($sql, $this->toString($select), $this->toString($select));

        $results = $this->execute($sql);

        /*$r = $this->execute('show profiles');
        var_dump($this->toArray($r)); exit();*/

        $hydrator = $this->getHydrator();
        $itemPrototype = new Widget();
        foreach ($results as $result) {
            $item = clone ($itemPrototype);
            $hydrator->hydrate($result, $item);
            $list->addItem($item);
        }
        return $list;
    }
}
