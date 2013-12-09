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
class ContentSearchMapper extends AbstractContentMapper implements
    ContentListMapperInterface
{
    /**
     * @var ScContent\Service\Back\ContentListOptionsProvider
     */
    protected $optionsProvider;

    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @param ScContent\Service\Back\ContentListOptionsProvider $options
     */
    public function __construct(
        AdapterInterface $adapter,
        OptionsProvider $options
    ) {
        $this->optionsProvider = $options;
        $this->setAdapter($adapter);
    }

    /**
     * @param string $optionsIdentifier
     * @return ScContent\Entity\Back\ContentList
     */
    public function getContent($optionsIdentifier)
    {
        $options = $this->optionsProvider->getOptions($optionsIdentifier);
        try {
            $this->beginTransaction();
            $parent = $this->getVirtualRoot($options->isTrash());

            $search = $this->optionsProvider->getSearchProxy(
                $optionsIdentifier
            );
            $counter = [
                'all' => $this->getSearchCount(
                    $optionsIdentifier,
                    'all'
                ),
                'categories' => $this->getSearchCount(
                    $optionsIdentifier,
                    'categories'
                ),
                'articles' => $this->getSearchCount(
                    $optionsIdentifier,
                    'articles'
                ),
                'files' => $this->getSearchCount(
                    $optionsIdentifier,
                    'files'
                ),
            ];
            $total = $counter[$options->getFilter()];
            $totalPages = max(1, ceil($total / $options->getLimit()));
            $currentPage = max(1, min($totalPages, $options->getPage()));

            // Fix the number of the page received from the request.
            if ($currentPage != $options->getPage()) {
                $options->setPage($currentPage);
            }

            $contentList = new ContentListEntity($parent);
            $contentList->setCounter($counter);
            $contentList->setTotalPages($totalPages);

            $offset = ($currentPage - 1) * $options->getLimit();
            $this->getContentItems($contentList, $offset, $optionsIdentifier);

            $this->commit();
            return $contentList;
        } catch (Exception $e) {
            $this->rollback();
        }
    }

    /**
     * @param string $optionsIdentifier
     * @param string $filter
     * @return integer
     */
    protected function getSearchCount($optionsIdentifier, $filter)
    {
        $options = $this->optionsProvider->getOptions($optionsIdentifier);
        $search = $this->optionsProvider->getSearchProxy($optionsIdentifier);
        if ($search->isEmpty()) {
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
                'content.trash' => $options->isTrash()
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

        if ($search->hasUserName()) {
            $userName = $search->getUserName();
            $userType = $search->getUserType();
            $userSource = $search->getUserSource();

            $on = new Predicate();
            $on->literal("`content`.`{$userType}` = `users`.`user_id`");
            $on->like("users.{$userSource}", "{$userName}%");
            $select->join(
                ['users' => $this->getTable(self::UsersTableAlias)],
                $on,
                []
            );
        }

        if ($search->hasText()) {
            $text = $this->quoteValue($search->convertText());
            $textSource = $search->getTextSource();

            $on = new Predicate();
            $on->literal('`content`.`id` = `search`.`id`');
            $on->literal(
                "MATCH(`search`.`{$textSource}`) AGAINST({$text} IN BOOLEAN MODE) > '0'"
            );
            $select->join(
                ['search' => $this->getTable(self::SearchTableAlias)],
                $on,
                []
            );
        }

        if ($search->hasDate()) {
            $modificationType = $search->getModificationType();
            $select->where([
                "`content`.`{$modificationType}` >= ?" => $search->calculateDateStart(),
                "`content`.`{$modificationType}` <= ?" => $search->calculateDateEnd(),
            ]);
        }

        $result = $this->execute($select)->current();
        return (int) $result['total'];
    }

    /**
     * @param ScContent\Entity\Back\ContentList $content
     * @param integer $offset
     * @param string $optionsIdentifier
     */
    protected function getContentItems(
        ContentListEntity $content,
        $offset,
        $optionsIdentifier
    ) {
        $options = $this->optionsProvider->getOptions($optionsIdentifier);
        $search = $this->optionsProvider->getSearchProxy($optionsIdentifier);
        if ($search->isEmpty()) {
            return;
        }
        $on = new Predicate();
        $select = $this->getSql()->select()
            ->columns([
                'id', 'type', 'status', 'title', 'name', 'spec',
                'date' => $search->getModificationType()
            ])
            ->from([
                'content' => $this->getTable(self::ContentTableAlias)
            ])
            ->join(
                ['subsidiary' => $this->getTable(self::ContentTableAlias)],
                $on->equalTo('subsidiary.trash', $options->isTrash())
                    ->literal('`subsidiary`.`level`     = `content`.`level` + \'1\'')
                    ->literal('`subsidiary`.`left_key`  > `content`.`left_key`')
                    ->literal('`subsidiary`.`right_key` < `content`.`right_key`'),
                ['childrens' => new Expression('COUNT(`subsidiary`.`id`)')],
                self::JoinLeft
            )
            ->join(
                ['users' => $this->getTable(self::UsersTableAlias)],
                'author' == $options->getUserType() ? 'content.author = users.user_id'
                                                    : 'content.editor = users.user_id',
                [
                    'user_id' => 'user_id',
                    'user_name' => 'username',
                    'user_email' => 'email',
                ],
                self::JoinLeft
            )
            ->where([
                'content.trash' => $options->isTrash(),
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
                $select->order("content.{$modificationType} " . $options->getOrder());
                break;
        }

        if ($search->hasUserName()) {
            $userName = $search->getUserName();
            $userType = $search->getUserType();
            $userSource = $search->getUserSource();

            $on = new Predicate();
            $on->literal("`content`.`{$userType}` = `search_users`.`user_id`");
            $on->like("search_users.{$userSource}", "{$userName}%");
            $select->join(
                ['search_users' => $this->getTable(self::UsersTableAlias)],
                $on,
                []
            );
        }

        if ($search->hasText()) {
            $text = $this->quoteValue($search->convertText());
            $textSource = $search->getTextSource();

            $on = new Predicate();
            $on->literal('`content`.`id` = `search`.`id`');
            $on->literal(
                "MATCH(`search`.`{$textSource}`) AGAINST({$text} IN BOOLEAN MODE) > '0'"
            );
            $select->join(
                ['search' => $this->getTable(self::SearchTableAlias)],
                $on,
                []
            );
        }

        if ($search->hasDate()) {
            $modificationType = $search->getModificationType();
            $select->where([
                "`content`.`{$modificationType}` >= ?" => $search->calculateDateStart(),
                "`content`.`{$modificationType}` <= ?" => $search->calculateDateEnd(),
            ]);
        }

        $results = $this->execute($select);
        $total = $content->getCounter($options->getFilter());
        $itemPrototype = new ContentListItem();
        $hydrator = $this->getHydrator();
        foreach ($results as $i => $result) {
            if ('asc' == $options->getOrder()) {
                $result['order'] = $offset + $i + 1;
            } else {
                $result['order'] = $total - $offset - $i;
            }
            $item = clone($itemPrototype);
            $hydrator->hydrate($result, $item);
            $content->addItem($item);
        }
    }

}
