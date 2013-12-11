<?php

namespace ScContent\Service\Front;

use ScContent\Entity\Front\Content,
    ScContent\Mapper\Front\ContentMapper,
    ScContent\Exception\IoCException,
    ScContent\Exception\RuntimeException;

class ContentService
{
    protected $mapper;

    public function setMapper(ContentMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper()
    {
        if (! $this->mapper instanceof ContentMapper) {
            throw new IoCException(
	           'The mapper was not set.'
            );
        }
        return $this->mapper;
    }

    public function getContent($name = '')
    {
        $mapper = $this->getMapper();

        $content = new Content();
        if (! empty($name)) {
            $content->setName($name);
            $mapper->findByName($content);
            return $content;
        }
        $mapper->findFirstElement($content);
        return $content;
    }
}
