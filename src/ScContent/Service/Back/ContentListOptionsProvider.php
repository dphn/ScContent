<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Back;

use ScContent\Mapper\Back\ContentListOptions as OptionsMapper,
    ScContent\Entity\Back\ContentSearchProxy,
    ScContent\Service\Localization,
    ScContent\Options\ContentList,
    //
    ScContent\Exception\DomainException,
    //
    Zend\Mvc\Router\Http\TreeRouteStack as Router,
    Zend\Http\Request;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListOptionsProvider
{
    /**
     * @var Zend\Mvc\Router\Http\TreeRouteStack
     */
    protected $router;

    /**
     * @var Zend\Http\Request
     */
    protected $request;

    /**
     * @var ScContent\Mapper\Back\ContentListOptions
     */
    protected $optionsMapper;

    /**
     * @var array
     */
    protected $query = [];

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
     * Constructor
     *
     * @param Zend\Mvc\Router\Http\TreeRouteStack $router
     * @param Zend\Http\Request $request
     * @param ScContent\Service\Localization $l10n
     */
    public function __construct(
        Router $router,
        Request $request,
        Localization $l10n
    ) {
        $this->l10n = $l10n;
        $this->router = $router;
        $this->request = $request;
        $query = [];
        if ($this->request->isPost()) {
            $query = $this->request->getPost();
        }
        if ($this->request->isGet()) {
            $query = $this->router->match($this->request)->getParams();
        }
        $this->optionsMapper = new OptionsMapper();
        $this->query = $query;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function hasIdentifier($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param null | string $identifier optional default null
     * @throws ScContent\Exception\DomainException
     * @return void
     */
    public function save($identifier = null)
    {
        if (! is_null($identifier)) {
            if (! $this->hasIdentifier($identifier)) {
                throw new DomainException(sprintf(
                    "Unknown options identifier '%s'.",
                    $identifier
                ));
            }
            $this->optionsMapper->saveOptions($this->options[$identifier]);
            return;
        }

        foreach ($this->options as $item) {
            $this->optionsMapper->saveOptions($item);
        }
    }

    /**
     * @param string $name
     * @return ScContent\Options\ContentList
     */
    public function getOptions($name, $type = '')
    {
        if (! $this->options[$name] instanceof ContentList) {
            $this->options[$name] = $this->optionsMapper->getOptons(
                $name, $this->query, $type
            );
        }
        return $this->options[$name];
    }

    /**
     * @param string $name
     * @return ScContent\Options\ContentList
     */
    public function getOtherOptions($name, $type = '')
    {
        $other = ($name == 'first') ? 'second' : 'first';
        return $this->getOptions($other, $type);
    }

    /**
     * @param string $name
     * @return ScContent\Entity\ContentSearchProxy
     */
    public function getSearchProxy($name)
    {
        if (! $this->searchProxies[$name] instanceof ContentSearchProxy) {
            $options = $this->getOptions($name);
            $this->searchProxies[$name] = new ContentSearchProxy(
                $this->l10n,
                $options->getSearchOptions()
            );
        }
        return $this->searchProxies[$name];
    }
}
