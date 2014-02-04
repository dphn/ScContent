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

use ScContent\Mapper\AbstractContentMapper,
    ScContent\Options\Back\WidgetVisibilityListOptions as Options,
    ScContent\Entity\Back\WidgetVisibilityList,
    ScContent\Entity\Back\WidgetVisibilityItem,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Predicate\Predicate,
    Zend\Db\Sql\Expression,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityListMapper extends AbstractContentMapper
{
    /**
     * @const string
     */
    const WidgetsTableAlias = 'widgetsalias';

    /**
     * @var array
     */
    protected $_tables = [
        self::ContentTableAlias => 'sc_content',
        self::WidgetsTableAlias => 'sc_widgets',
        self::UsersTableAlias   => 'sc_users',
    ];

    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @param ScContent\Options\Back\WidgetVisibilityListOptions $options
     * @return ScContent\Entity\Back\WidgetVisibilityList
     */
    public function getContent(Options $options)
    {
        $parent = $this->findMetaById($options->getContentId());
        if (empty($parent) || $parent['trash']) {
            $parent = $this->getVirtualRoot(false);
        }
        $back = $this->findBack($parent);

        $counter = [
            'all'        => $this->getContentCount($parent, 'all'),
            'categories' => $this->getContentCount($parent, 'categories'),
            'articles'   => $this->getContentCount($parent, 'articles'),
            'files'      => $this->getContentCount($parent, 'files'),
        ];
        $total = $counter[$options->getFilter()];
        $totalPages = max(1, ceil($total / $options->getLimit()));
        $currentPage = max(1, min($totalPages, $options->getPage()));

        // Fix the number of the page received from the request.
        if ($currentPage != $options->getPage()) {
            $options->setPage($currentPage);
        }

        $contentList = new WidgetVisibilityList($parent);
        $contentList->setBack($back);
        $contentList->setCounter($counter);
        $contentList->setTotalPages($totalPages);

        $offset = ($currentPage - 1) * $options->getLimit();

        $this->getContentItems($contentList, $options, $offset);

        return $contentList;
    }

    /**
     * @param array $parent
     * @param string $filter
     * @return integer
     */
    protected function getContentCount($parent, $filter)
    {
        $select = $this->getSql()->select()
            ->columns(['total' => new Expression('COUNT(`id`)')])
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                '`trash`     = ?' => $parent['trash'],
                '`left_key`  > ?' => $parent['left_key'],
                '`right_key` < ?' => $parent['right_key'],
                '`level`     = ?' => $parent['level'] + 1,
            ]);

        switch ($filter) {
        	case 'categories':
        	    $select->where(['type' => 'category']);
        	    break;
        	case 'articles':
        	    $select->where(['type' => 'article']);
        	    break;
        	case 'files':
        	    $select->where(['type' => 'file']);
        	    break;
        }
        $result = $this->execute($select)->current();
        return (int) $result['total'];
    }

    /**
     * @param ScContent\Entity\Back\WidgetVisibilityList $content
     * @param ScContent\Options\Back\WidgetVisibilityListOptions $options
     * @param integer $offset
     * @return void
     */
    protected function getContentItems(WidgetVisibilityList $content, Options $options, $offset)
    {
        $select = $this->getSql()->select()
            ->columns([
                'id', 'type', 'status', 'title', 'name', 'spec',
                'date' => 'created',
            ])
            ->from(['content' => $this->getTable(self::ContentTableAlias)])
            ->join(
                ['subsidiary' => $this->getTable(self::ContentTableAlias)],
                (new Predicate())->equalTo('subsidiary.trash', $content->getParent('trash'))
                    ->equalTo('subsidiary.level', $content->getParent('level') + 2)
                    ->literal('`subsidiary`.`left_key`  > `content`.`left_key`')
                    ->literal('`subsidiary`.`right_key` < `content`.`right_key`'),
                ['childrens' => new Expression('COUNT(`subsidiary`.`id`)')],
                self::JoinLeft
            )
            ->join(
                ['users' => $this->getTable(self::UsersTableAlias)],
                'content.author = users.user_id',
                ['user_id' => 'user_id', 'user_name' => 'username', 'user_email' => 'email'],
                self::JoinLeft
            )
            ->join(
                ['widgets' => $this->getTable(self::WidgetsTableAlias)],
                (new Predicate())->equalTo('widgets.widget', $options->getWidgetId())
                    ->literal('widgets.content = content.id'),
                ['enabled'],
                self::JoinLeft
            )
            ->where([
                '`content`.`trash`     = ?' => $content->getParent('trash'),
                '`content`.`left_key`  > ?' => $content->getParent('left_key'),
                '`content`.`right_key` < ?' => $content->getParent('right_key'),
                '`content`.`level`     = ?' => $content->getParent('level') + 1,
            ])
            ->group('content.id')
            ->limit($options->getLimit())
            ->offset($offset);

        switch ($options->getFilter()) {
            case 'categories':
                $select->where(['content.type' => 'category']);
                break;
            case 'articles':
                $select->where(['content.type' => 'article']);
                break;
            case 'files':
                $select->where(['content.type' => 'file']);
                break;
        }

        switch ($options->getOrderBy()) {
            case 'natural':
                $select->order('content.left_key ' . $options->getOrder());
                break;
            case 'title':
                $select->order('content.title ' . $options->getOrder());
                break;
            case 'user':
                $select->order('users.username ' . $options->getOrder());
                break;
            case 'date':
                $select->order('content.created '. $options->getOrder());
                break;
        }

        $results = $this->execute($select);
        $itemPrototype = new WidgetVisibilityItem();
        $hydrator = $this->getHydrator();
        foreach ($results as $i => $result) {
            $item = clone($itemPrototype);
            $hydrator->hydrate($result, $item);
            $content->addItem($item);
        }
    }
}
