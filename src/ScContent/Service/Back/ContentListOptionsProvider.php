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

use ScContent\Mapper\Back\ContentListOptions as OptionsMapper,
    ScContent\Options\Back\ContentListOptions as Options,
    ScContent\Entity\Back\ContentSearchProxy,
    ScContent\Service\Localization,
    //
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DomainException,
    ScContent\Exception\IoCException,
    //
    Zend\Mvc\Router\Http\TreeRouteStack as Router,
    Zend\Http\Request,
    Zend\Stdlib\ArrayUtils;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListOptionsProvider
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
     * @var \ScContent\Service\Localization
     */
    protected $l10n;

    /**
     * @var \ScContent\Mapper\Back\ContentListOptions
     */
    protected $optionsMapper;

    /**
     * @var null|array
     */
    protected $query;

    /**
     * @var array
     */
    protected $options = [
        'first'  => null,
        'second' => null,
    ];

    /**
     * @var array
     */
    protected $searchProxies = [
        'first'  => null,
        'second' => null,
    ];

    /**
     * @param  \Zend\Mvc\Router\Http\TreeRouteStack $router
     * @return void
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     *
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
     * @param \ScContent\Service\Localization $l10n
     */
    public function setLocalization(Localization $l10n)
    {
        $this->l10n = $l10n;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Service\Localization
     */
    public function getLocalization()
    {
        if (! $this->l10n instanceof Localization) {
            throw new IoCException(
                'The localization service was not set.'
            );
        }
        return $this->l10n;
    }

    /**
     * @param  \ScContent\Mapper\Back\ContentListOptions $mapper
     * @return void
     */
    public function setOptionsMapper(OptionsMapper $mapper)
    {
        $this->optionsMapper = $mapper;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\Back\ContentListOptions
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
            }
            $query = ArrayUtils::merge(
                $query, $router->match($request)->getParams()
            );
            $this->query = $query;
        }
        return $this->query;
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasIdentifier($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param  null|string $identifier optional default null
     * @throws \ScContent\Exception\DomainException
     * @return void
     */
    public function save($identifier = null)
    {
        $optionsMapper = $this->getOptionsMapper();
        if (! is_null($identifier)) {
            if (! $this->hasIdentifier($identifier)) {
                throw new DomainException(sprintf(
                    "Unknown options identifier '%s'.",
                    $identifier
                ));
            }
            $optionsMapper->saveOptions($this->options[$identifier]);
            return;
        }

        foreach ($this->options as $item) {
            $optionsMapper->saveOptions($item);
        }
    }

    /**
     * @param  string $name
     * @return \ScContent\Options\Back\ContentListOptions
     */
    public function getOptions($name, $type = '')
    {
        $query = $this->getQuery();
        $optionsMapper = $this->getOptionsMapper();
        if (! $this->options[$name] instanceof Options) {
            $this->options[$name] = $optionsMapper->getOptons(
                $name, $query, $type
            );
        }
        return $this->options[$name];
    }

    /**
     * @param  string $name
     * @return \ScContent\Options\Back\ContentListOptions
     */
    public function getOtherOptions($name, $type = '')
    {
        $other = ($name == 'first') ? 'second' : 'first';
        return $this->getOptions($other, $type);
    }

    /**
     * @param  string $name
     * @return \ScContent\Entity\ContentSearchProxy
     */
    public function getSearchProxy($name)
    {
        if (! $this->searchProxies[$name] instanceof ContentSearchProxy) {
            $l10n = $this->getLocalization();
            $options = $this->getOptions($name);
            $this->searchProxies[$name] = new ContentSearchProxy(
                $l10n,
                $options->getSearchOptions()
            );
        }
        return $this->searchProxies[$name];
    }
}
