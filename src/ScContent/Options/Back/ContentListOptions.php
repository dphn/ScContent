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
class ContentListOptions extends AbstractEntity
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
     * @var string
     */
    protected $name = 'first';

    /**
     * @var string
     */
    protected $type = 'list';

    /**
     * @var string
     */
    protected $root = 'site';

    /**
     * @var integer
     */
    protected $parent = 0;

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
     * @var sting
     */
    protected $userType = 'author';

    /**
     * @var string
     */
    protected $modificationType = 'created';

    /**
     * @var array
     */
    protected $list = [];

    /**
     * @var array
     */
    protected $search = [];

    /**
     * @var array
     */
    protected $searchOptions = [];

    /**
     * @var array
     */
    protected static $types = ['list', 'search'];

    /**
     * @var array
     */
    protected static $names = ['first', 'second'];

    /**
     * @var array
     */
    protected static $roots = ['site', 'trash', 'search'];

    /**
     * @var array
     */
    protected static $filters = ['all', 'categories', 'articles', 'files'];

    /**
     * @var array
     */
    protected static $userTypes = ['author', 'editor'];

    /**
     * @var array
     */
    protected static $modificationTypes = ['created', 'modified'];

    /**
     * @var array
     */
    protected static $orders = ['asc', 'desc'];

    /**
     * @var array
     */
    protected static $ordersBy = ['natural', 'title', 'status', 'user', 'date'];

    /**
     * @param string $name
     * @return boolean
     */
    public static function hasName($name)
    {
        return in_array($name, self::$names, true);
    }

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options)
    {
        if (! is_array($options)) {
            return;
        }
        if (isset($options['list'])) {
            $this->setList($options['list']);
            unset($options['list']);
        }
        if (isset($options['search'])) {
            $this->setSearch($options['search']);
            unset($options['search']);
        }
        if (isset($options['search_options'])) {
            $this->setSearchOptions($options['search_options']);
            unset($options['search_options']);
        }

        $options = $this->filterOptions($options);

        $this->exchangeArray($options);
        $this->state = self::StateConstructed;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        if (in_array($name, self::$names, true)) {
            $this->name = $name;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        if (! in_array($type, self::$types, true)
            || ($this->type == $type && $this->state == self::StateConstructed)
        ) {
            return;
        }
        $this->type = $type;
        $this->pageIsReset = false;

        $storage = $this->$type;
        if (isset($storage['root'])) {
            $this->root = $storage['root'];
        }
        if (isset($storage['user_type'])) {
            $this->setUserType($storage['user_type']);
        }
        if (isset($storage['modification_type'])) {
            $this->setModificationType($storage['modification_type']);
        }
        if (! isset($storage[$this->root])) {
            return;
        }
        $container = $storage[$this->root];
        if (isset($container['parent'])) {
            $this->setParent($container['parent']);
        }
        if (isset($container['page'])) {
            $this->setPage($container['page']);
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $root
     * @return void
     */
    public function setRoot($root)
    {
        if (! in_array($root, self::$roots, true) || $this->root == $root) {
            return;
        }
        $this->root = $root;
        $type = $this->type;
        $storage = &$this->$type;
        $this->pageIsReset = false;

        if (! isset($storage[$root])) {
            return;
        }
        $container = &$storage[$root];
        if (isset($container['parent'])) {
            $this->setParent($container['parent']);
        }
        if (isset($container['page'])) {
            $this->setPage($container['page']);
        }
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return integer
     */
    public function isTrash()
    {
        return (int) ($this->root == 'trash');
    }

    /**
     * @param integer $parent
     * @return void
     */
    public function setParent($parent)
    {
        $parent = (int) $parent;
        if ($parent >= 0) {
            if ($this->state == self::StateConstructed
                 && $parent != $this->parent) {
                $this->resetPage();
            }
            $this->parent = $parent;
        }
    }

    /**
     * @return integer
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param string $filter
     * @return void
     */
    public function setFilter($filter)
    {
        if (in_array($filter, self::$filters, true)) {
            if ($this->state == self::StateConstructed
                 && $filter != $this->filter) {
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
     * @param integer $page
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

    /**
     * @param string $type
     * @return void
     */
    public function setUserType($type)
    {
        if (in_array($type, self::$userTypes, true)) {
            $this->userType = $type;
        }
    }

    /**
     * @return sting
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setModificationType($type)
    {
        if (in_array($type, self::$modificationTypes, true)) {
            $this->modificationType = $type;
        }
    }

    /**
     * @return string
     */
    public function getModificationType()
    {
        return $this->modificationType;
    }

    /**
     * @param string $order
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
     * @param string $orderBy
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
     * @param integer $limit
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
     * @param array $list
     * @return void
     */
    public function setList($list)
    {
        if (is_array($list)) {
            $this->list = $list;
        }
    }

    /**
     * @return array
     */
    public function getList()
    {
        $list = &$this->list;
        if ($this->type != 'list') {
            return $list;
        }
        if (! isset($list[$this->root])) {
            $list[$this->root] = [];
        }
        $list['root'] = $this->root;
        $list['user_type'] = $this->userType;
        $list['modification_type'] = $this->modificationType;
        $container = &$list[$this->root];
        $container['parent'] = $this->parent;
        $container['page'] = $this->page;
        return $list;
    }

    /**
     * @param array $search
     * @return void
     */
    public function setSearch($search)
    {
        if (is_array($search)) {
            $this->search = $search;
        }
    }

    /**
     * @return array
     */
    public function getSearch()
    {
        $search = &$this->search;
        if ($this->type != 'search') {
            return $search;
        }
        if (! isset($search[$this->root])) {
            $search[$this->root] = [];
        }
        $search['root'] = $this->root;
        $search['user_type'] = $this->userType;
        $search['modification_type'] = $this->modificationType;
        $container = &$search[$this->root];
        $container['parent'] = $this->parent;
        $container['page'] = $this->page;
        return $search;
    }

    /**
     * @param array $options
     * @return void
     */
    public function setSearchOptions($options)
    {
        if (isset($options['user_type'])) {
            $userType = $options['user_type'];
            $this->search['user_type'] = $userType;
            if ($this->type == 'search') {
                $this->userType = $userType;
            }
        }
        if (isset($options['modification_type'])) {
            $modificationType = $options['modification_type'];
            $this->search['modification_type'] = $modificationType;
            if ($this->type == 'search') {
                $this->modificationType = $modificationType;
            }
        }
        $this->searchOptions = $options;
    }

    /**
     * @return array
     */
    public function getSearchOptions()
    {
        return $this->searchOptions;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $options = parent::getArrayCopy();
        return $this->filterOptions($options);
    }

    /**
     * @param array $options
     * @return array
     */
    protected function filterOptions($options)
    {
        $keys = [
            'root',      'state',
            'user_type', 'modification_type',
            'page',      'parent',
        ];
        foreach ($keys as $key) {
            if (isset($options[$key])) {
                unset($options[$key]);
            }
        }
        return $options;
    }
}
