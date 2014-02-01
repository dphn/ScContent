<?php

namespace ScContent\Service\Back;

use ScContent\Mapper\Back\WidgetVisibilityMapper as VisibilityMapper,
    ScContent\Mapper\Back\WidgetVisibilityOptionsMapper as OptionsMapper,
    ScContent\Options\Back\WidgetVisibilityListOptions as Options,
    //
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Zend\Mvc\Router\Http\TreeRouteStack as Router,
    Zend\Http\Request,
    Zend\Stdlib\ArrayUtils;

class WidgetVisibilityService
{
    protected $router;

    protected $request;

    protected $visibilityMapper;

    protected $optionsMapper;

    /**
     * @var ScContent\Options\Back\WidgetVisibilityOptions
     */
    protected $options;

    protected $query;

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function getRouter()
    {
        if (! $this->router instanceof Router) {
            throw new IoCException(
                'The router was not set.'
            );
        }
        return $this->router;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        if (! $this->request instanceof Request) {
            throw new IoCException(
                'The request was not set.'
            );
        }
        return $this->request;
    }

    public function setVisibilityMapper(VisibilityMapper $mapper)
    {
        $this->visibilityMapper = $mapper;
    }

    public function getVisibilityMapper()
    {
        if (! $this->visibilityMapper instanceof VisibilityMapper) {
            throw new IoCException(
                'The visibility mapper was not set.'
            );
        }
        return $this->visibilityMapper;
    }

    public function setOptionsMapper(OptionsMapper $mapper)
    {
        $this->optionsMapper = $mapper;
    }

    public function getOptionsMapper()
    {
        if (! $this->optionsMapper instanceof OptionsMapper) {
            throw new IoCException(
                'The options mapper was not set.'
            );
        }
        return $this->optionsMapper;
    }

    public function setQuery($query)
    {
        if (! is_array($query)) {
            throw new InvalidArgumentException(
                'The query must be an array.'
            );
        }
        $this->query = $query;
    }

    public function getQuery()
    {
        if (is_null($this->query)) {
            $request = $this->getRequest();
            $router = $this->getRouter();
            $query = [];
            if ($request->isPost()) {
                $query = (array) $request->getPost();
            }
            $query = ArrayUtils::merge(
                $query, $router->match($request)->getParams()
            );
            $this->query = $query;
        }
        return $this->query;
    }

    public function getOptions()
    {
        if (! $this->options instanceof Options) {
            $mapper = $this->getOptionsMapper();
            $query = $this->getQuery();
            $this->options = $mapper->getOptions($query);
        }
        return $this->options;
    }

    public function saveOptions()
    {
        $mapper = $this->getOptionsMapper();
        $options = $this->getOptions();
        $mapper->saveOptions($options);
    }

    public function getContentList()
    {
        $options = $this->getOptions();
        $mapper = $this->getVisibilityMapper();
        $list = $mapper->getContent($options);
        return $list;
    }
}
