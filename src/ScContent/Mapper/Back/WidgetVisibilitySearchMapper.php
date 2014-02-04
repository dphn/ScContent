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
    Zend\Filter\FilterInterface,
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Predicate\Predicate,
    Zend\Db\Sql\Expression,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilitySearchMapper extends AbstractContentMapper
{
    /**
     * @var Zend\Filter\FilterInterface
     */
    protected $morfologyFilter;

    /**
     * @const string
     */
    const WidgetsTableAlias = 'widgetsalias';

    /**
     * @var array
     */
    protected $_tables = [
        self::ContentTableAlias => 'sc_content',
        self::SearchTableAlias  => 'sc_search',
        self::WidgetsTableAlias => 'sc_widgets',
        self::UsersTableAlias   => 'sc_users',
    ];

    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @param Zend\Filter\FilterInterface $filter
     */
    public function __construct(AdapterInterface $adapter, FilterInterface $filter)
    {
        $this->morfologyFilter = $filter;
        $this->setAdapter($adapter);
    }

    /**
     * @param ScContent\Options\Back\WidgetVisibilityListOptions $options
     * @return ScContent\Entity\Back\WidgetVisibilityList
     */
    public function getContent(Options $options)
    {
        $this->beginTransaction();
        $parent = $this->getVirtualRoot(false);
        $counter = [
            'all'        => $this->getSearchCount($options, 'all'),
            'categories' => $this->getSearchCount($options, 'categories'),
            'articles'   => $this->getSearchCount($options, 'articles'),
            'files'      => $this->getSearchCount($options, 'files'),
        ];
        $total = $counter[$options->getFilter()];
        $totalPages = max(1, ceil($total / $options->getLimit()));
        $currentPage = max(1, min($totalPages, $options->getPage()));

        // Fix the number of the page received from the request.
        if ($currentPage != $options->getPage()) {
            $options->setPage($currentPage);
        }

        $contentList = new WidgetVisibilityList($parent);
        $contentList->setCounter($counter);
        $contentList->setTotalPages($totalPages);

        $offset = ($currentPage - 1) * $options->getLimit();

        $this->getContentItems($contentList, $options, $offset);

        return $contentList;
    }

    /**
     * @param ScContent\Options\Back\WidgetVisibilityListOptions $options
     * @param string $filter
     * @return integer
     */
    protected function getSearchCount(Options $options, $filter)
    {
        if (! $options->getSearch()) {
            return 0;
        }

        $select = $this->getSql()->select()
            ->columns([
                'total' => new Expression('COUNT(`content`.`id`)'),
            ])
            ->from([
                'content' => $this->getTable(self::ContentTableAlias),
            ])
            ->where([
                'content.trash' => 0
            ]);

        switch ($filter) {
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

        $text = $this->morfologyFilter->filter($options->getSearch());
        $text = $this->quoteValue($text);
        $textSource = $options->getSearchSource();

        $select->join(
            ['search' => $this->getTable(self::SearchTableAlias)],
            (new Predicate())->literal('`content`.`id` = `search`.`id`')
                ->literal(
                    "MATCH(`search`.`{$textSource}`) AGAINST({$text} IN BOOLEAN MODE) > '0'"
                ),
            []
        );

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
                'content.trash' => 0,
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

        $text = $this->morfologyFilter->filter($options->getSearch());
        $text = $this->quoteValue($text);
        $textSource = $options->getSearchSource();

        $select->join(
            ['search' => $this->getTable(self::SearchTableAlias)],
            (new Predicate())->literal('`content`.`id` = `search`.`id`')
            ->literal(
                "MATCH(`search`.`{$textSource}`) AGAINST({$text} IN BOOLEAN MODE) > '0'"
            ),
            []
        );

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
