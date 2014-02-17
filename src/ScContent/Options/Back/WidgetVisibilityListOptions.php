<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Options\Back;

use ScContent\Entity\AbstractEntity;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityListOptions extends AbstractEntity
{
    /**
     * @var integer
     */
    const StateUndefined = 0;

    /**
     * @var integer
     */
    const StateConstructed = 1;

    /**
     * @var integer
     */
    protected $state = self::StateUndefined;

    /**
     * @var null|integer
     */
    protected $widgetId;

    /**
     * @var integer
     */
    protected $contentId = 0;

    /**
     * @var string
     */
    protected $search = '';

    /**
     * @var string
     */
    protected $searchSource = 'title';

    /**
     * @var string
     */
    protected $filter = 'all';

    /**
     * @var string
     */
    protected $order = 'asc';

    /**
     * @var string
     */
    protected $orderBy = 'natural';

    /**
     * @var integer
     */
    protected $limit = 20;

    /**
     * @var integer
     */
    protected $page = 1;

    /**
     * @var boolean
     */
    protected $pageIsReset = false;

    /**
     * @var array
     */
    protected static $searchSources = ['title', 'name', 'content', 'description'];

    /**
     * @var array
     */
    protected static $filters = ['all', 'categories', 'articles', 'files'];

    /**
     * @var array
     */
    protected static $orders = ['asc', 'desc'];

    /**
     * @var array
     */
    protected static $ordersBy = ['natural', 'title', 'user', 'date'];

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->exchangeArray($options);
        $this->state = self::StateConstructed;
    }

    /**
     * @param  integer $id
     * @return void
     */
    public function setWidgetId($id)
    {
        $this->widgetId = (int) $id;
    }

    /**
     * @return null|integer
     */
    public function getWidgetId()
    {
        return $this->widgetId;
    }

    /**
     * @param  integer $id
     * @return void
     */
    public function setContentId($id)
    {
        $id = (int) $id;

        if ($this->state == self::StateConstructed
            && $this->contentId != $id
        ) {
            $this->resetPage();
        }
        $this->contentId = $id;
    }

    /**
     * @return integer
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param  string $needle
     * @return void
     */
    public function setSearch($needle)
    {
        $needle = mb_substr(trim($needle), 0, 64);
        if ($this->state == self::StateConstructed
            && $needle !== $this->search
        ) {
            $this->resetPage();
        }
        $this->search = $needle;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param  string $source
     * @return void
     */
    public function setSearchSource($source)
    {
        if (! in_array($source, self::$searchSources, true)) {
            return;
        }
        if ($this->state == self::StateConstructed
            && $source != $this->searchSource
        ) {
            $this->resetPage();
        }
        $this->searchSource = $source;
    }

    /**
     * @return string
     */
    public function getSearchSource()
    {
        return $this->searchSource;
    }

    /**
     * @param  string $filter
     * @return void
     */
    public function setFilter($filter)
    {
        if (in_array($filter, self::$filters, true)) {
            if ($this->state == self::StateConstructed
                 && $filter != $this->filter
            ) {
                $this->resetPage();
            }
            $this->filter = $filter;
        }
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param  string $order
     * @return void
     */
    public function setOrder($order)
    {
        if ($this->state == self::StateConstructed) {
            return;
        }
        if (in_array($order, self::$orders, true)) {
            $this->order = $order;
        }
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param  string $orderBy
     * @return void
     */
    public function setOrderBy($orderBy)
    {
        if (in_array($orderBy, self::$ordersBy, true)) {
            if ($this->state == self::StateConstructed) {
                if ($this->orderBy == $orderBy && $this->order == 'asc') {
                    $this->order = 'desc';
                } else {
                    $this->order = 'asc';
                }
            }
            $this->orderBy = $orderBy;
        }
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param  integer $limit
     * @return void
     */
    public function setLimit($limit)
    {
        // not uses
    }

    /**
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param  integer $page
     * @return void
     */
    public function setPage($page)
    {
        if ($this->pageIsReset) {
            return;
        }
        $page = (int) $page;
        if ($page >= 0) {
            $this->page = $page;
        }
    }

    /**
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return void
     */
    public function resetPage()
    {
        $this->pageIsReset = true;
        $this->page = 1;
    }
}
