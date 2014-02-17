<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Front;

use ScContent\Entity\Front\Content,
    ScContent\Mapper\Front\ContentMapper,
    ScContent\Exception\IoCException,
    ScContent\Exception\RuntimeException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentService
{
    /**
     * @var \ScContent\Entity\Front\Content
     */
    protected $content;

    /**
     * @var \ScContent\Mapper\Front\ContentMapper
     */
    protected $mapper;

    /**
     * @param  \ScContent\Mapper\Front\ContentMapper $mapper
     * @return void
     */
    public function setMapper(ContentMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\Front\ContentMapper
     */
    public function getMapper()
    {
        if (! $this->mapper instanceof ContentMapper) {
            throw new IoCException(
                'The mapper was not set.'
            );
        }
        return $this->mapper;
    }

    /**
     * @param  string $name
     * @return \ScContent\Entity\Front\Content
     */
    public function getContent($name = '')
    {
        if ($this->content instanceof Content) {
            return $this->content;
        }

        $mapper = $this->getMapper();

        $this->content = new Content();
        if (! empty($name)) {
            $this->content->setName($name);
            // @todo get "Allow preview" from ACL
            $mapper->findByName($this->content, true);
            return $this->content;
        }
        $mapper->findHomePage($this->content);

        return $this->content;
    }
}
