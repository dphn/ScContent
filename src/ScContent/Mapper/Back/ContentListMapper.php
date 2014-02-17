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
    ScContent\Service\Back\ContentListOptionsProvider as OptionsProvider,
    ScContent\Entity\Back\ContentList as ContentListEntity,
    ScContent\Entity\Back\ContentListItem,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Predicate\Predicate,
    Zend\Db\Sql\Expression,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListMapper extends AbstractContentMapper implements
    ContentListMapperInterface
{
    /**
     * @var \ScContent\Service\Back\ContentListOptionsProvider
     */
    protected $optionsProvider;

    /**
     * Constructor
     *
     * @param \Zend\Db\Adapter\AdapterInterface $adapter
     * @param \ScContent\Service\Back\ContentListOptionsProvider $options
     */
    public function __construct(
        AdapterInterface $adapter,
        OptionsProvider $options
    ) {
        $this->optionsProvider = $options;
        $this->setAdapter($adapter);
    }

    /**
     * @param  string $optionsIdentifier
     * @return \ScContent\Entity\Back\ContentList
     */
    public function getContent($optionsIdentifier)
    {
        $options = $this->optionsProvider->getOptions($optionsIdentifier);
        $parent = $this->findMetaById($options->getParent());
        if (empty($parent) || $parent['trash'] != $options->isTrash()) {
            /* reset parent */
            $options->setParent(0);
            $parent = $this->getVirtualRoot($options->isTrash());
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

        $contentList = new ContentListEntity($parent);
        $contentList->setBack($back);
        $contentList->setCounter($counter);
        $contentList->setTotalPages($totalPages);

        $offset = ($currentPage - 1) * $options->getLimit();
        $this->getContentItems($contentList, $offset, $optionsIdentifier);

        return $contentList;
    }

    /**
     * @param  array $parent
     * @param  string $filter
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
     * @param  \ScContent\Entity\Back\ContentList $content
     * @param  integer $offset
     * @param  string $optionsIdentifier
     * @return void
     */
    protected function getContentItems(ContentListEntity $content, $offset, $optionsIdentifier)
    {
        $options = $this->optionsProvider->getOptions($optionsIdentifier);
        $select = $this->getSql()->select()
            ->columns([
                'id', 'type', 'status', 'title', 'name', 'spec',
                'date' => $options->getModificationType()
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
                'author' == $options->getUserType() ? 'content.author = users.user_id'
                                                    : 'content.editor = users.user_id',
                ['user_id' => 'user_id', 'user_name' => 'username', 'user_email' => 'email'],
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
            case 'status':
                $select->order('content.status ' . $options->getOrder());
                break;
            case 'user':
                $select->order('users.username ' . $options->getOrder());
                break;
            case 'date':
                $modificationType = $options->getModificationType();
                $select->order("content.{$modificationType} ". $options->getOrder());
                break;
        }

        $results = $this->execute($select);
        $total = $content->getCounter($options->getFilter());
        $itemPrototype = new ContentListItem();
        $hydrator = $this->getHydrator();
        foreach ($results as $i => $result) {
            if ('asc' == $options->getOrder()) {
                $result['order'] = $offset + $i +1;
            } else {
                $result['order'] = $total - $offset - $i;
            }
            $item = clone($itemPrototype);
            $hydrator->hydrate($result, $item);
            $content->addItem($item);
        }
    }
}
