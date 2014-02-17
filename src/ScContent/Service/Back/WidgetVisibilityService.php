<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Back;

use ScContent\Mapper\Back\WidgetVisibilityListMapper    as ListMapper,
    ScContent\Mapper\Back\WidgetVisibilitySearchMapper  as SearchMapper,
    ScContent\Mapper\Back\WidgetVisibilityOptionsMapper as OptionsMapper,
    ScContent\Options\Back\WidgetVisibilityListOptions  as Options,
    //
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Zend\Mvc\Router\Http\TreeRouteStack as Router,
    Zend\Http\Request,
    Zend\Stdlib\ArrayUtils;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityService
{
    /**
     * @var \Zend\Mvc\Router\Http\TreeRouteStack
     */
    protected $router;

    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * @var \ScContent\Mapper\Back\WidgetVisibilityListMapper
     */
    protected $listMapper;

    /**
     * @var \ScContent\Mapper\Back\WidgetVisibilitySearchMapper
     */
    protected $searchMapper;

    /**
     * @var \ScContent\Mapper\Back\WidgetVisibilityOptionsMapper
     */
    protected $optionsMapper;

    /**
     * @var \ScContent\Options\Back\WidgetVisibilityOptions
     */
    protected $options;

    /**
     * @var null|array
     */
    protected $query;

    /**
     * @param  \Zend\Mvc\Router\Http\TreeRouteStack $router
     * @return void
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \Zend\Mvc\Router\Http\TreeRouteStack
     */
    public function getRouter()
    {
        if (! $this->router instanceof Router) {
            throw new IoCException(
                'The router was not set.'
            );
        }
        return $this->router;
    }

    /**
     * @param  \Zend\Http\Request $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \Zend\Http\Request
     */
    public function getRequest()
    {
        if (! $this->request instanceof Request) {
            throw new IoCException(
                'The request was not set.'
            );
        }
        return $this->request;
    }

    /**
     * @param  \ScContent\Mapper\Back\WidgetVisibilityListMapper $mapper
     * @return void
     */
    public function setListMapper(ListMapper $mapper)
    {
        $this->listMapper = $mapper;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\Back\WidgetVisibilityListMapper
     */
    public function getListMapper()
    {
        if (! $this->listMapper instanceof ListMapper) {
            throw new IoCException(
                'The visibility list mapper was not set.'
            );
        }
        return $this->listMapper;
    }

    /**
     * @param  \ScContent\Mapper\Back\WidgetVisibilitySearchMapper $mapper
     * @return void
     */
    public function setSearchMapper(SearchMapper $mapper)
    {
        $this->searchMapper = $mapper;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\Back\WidgetVisibilitySearchMapper
     */
    public function getSearchMapper()
    {
        if (! $this->searchMapper instanceof SearchMapper) {
            throw new IoCException(
                'The search mapper was not set.'
            );
        }
        return $this->searchMapper;
    }

    /**
     * @param  \ScContent\Mapper\Back\WidgetVisibilityOptionsMapper $mapper
     * @return void
     */
    public function setOptionsMapper(OptionsMapper $mapper)
    {
        $this->optionsMapper = $mapper;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\Back\WidgetVisibilityOptionsMapper
     */
    public function getOptionsMapper()
    {
        if (! $this->optionsMapper instanceof OptionsMapper) {
            throw new IoCException(
                'The options mapper was not set.'
            );
        }
        return $this->optionsMapper;
    }

    /**
     * @param  array $query
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function setQuery($query)
    {
        if (! is_array($query)) {
            throw new InvalidArgumentException(
                'The query must be an array.'
            );
        }
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        if (is_null($this->query)) {
            $request = $this->getRequest();
            $router = $this->getRouter();
            $query = [];
            if ($request->isPost()) {
                $query = (array) $request->getPost();
            } elseif ($request->isGet()) {
                $query = (array) $request->getQuery();
            }

            $query = ArrayUtils::merge(
                $query, $router->match($request)->getParams()
            );
            $this->query = $query;
        }
        return $this->query;
    }

    /**
     * @return \ScContent\Options\Back\WidgetVisibilityOptions
     */
    public function getOptions()
    {
        if (! $this->options instanceof Options) {
            $mapper = $this->getOptionsMapper();
            $query = $this->getQuery();
            $this->options = $mapper->getOptions($query);
        }
        return $this->options;
    }

    /**
     * @return void
     */
    public function saveOptions()
    {
        $mapper = $this->getOptionsMapper();
        $options = $this->getOptions();
        $mapper->saveOptions($options);
    }

    /**
     * @return \ScContent\Entity\Back\WidgetVisibilityList
     */
    public function getContentList()
    {
        $options = $this->getOptions();

        if ($options->getSearch()) {
            $mapper = $this->getSearchMapper();
        } else {
            $mapper = $this->getListMapper();
        }

        $list = $mapper->getContent($options);
        return $list;
    }
}
