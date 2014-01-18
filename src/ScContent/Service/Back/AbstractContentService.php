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

use ScContent\Service\AbstractService,
    ScContent\Options\ModuleOptions,
    ScContent\Mapper\Back\ContentMapper,
    ScContent\Service\ScDateTimeInterface,
    ScContent\Service\Stdlib,
    ScContent\Entity\AbstractContent,
    ScContent\Exception\IoCException,
    //
    Zend\Authentication\AuthenticationService;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractContentService extends AbstractService
{
    /**
     * @var Zend\Authentication\AuthenticationService
     */
    protected $authentication;

    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var ScContent\Mapper\Back\ContentMapper
     */
    protected $contentMapper;

    /**
     * @var ScContent\Service\ScDateTimeInterface
     */
    protected $datetime;

    /**
     * @param Zend\Authentication\AuthenticationService $authentication
     * @return void
     */
    public function setAuthenticationService(AuthenticationService $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\Authentication\AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (! $this->authentication instanceof AuthenticationService) {
            throw new IoCException(
                'The authentication service was not set.'
            );
        }
        return $this->authentication;
    }

    /**
     * @param ScContent\Options\ModuleOptions $options
     * @return void
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions instanceof ModuleOptions) {
            throw new IoCException(
                'The module options were not set.'
            );
        }
        return $this->moduleOptions;
    }

    /**
     * @param ScContent\Mapper\Back\ContentMapper $mapper
     * @return void
     */
    public function setContentMapper(ContentMapper $mapper)
    {
        $this->contentMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\ContentMapper
     */
    public function getContentMapper()
    {
        if (! $this->contentMapper instanceof ContentMapper) {
            throw new IoCException(
                'The content mapper was not set.'
            );
        }
        return $this->contentMapper;
    }

    /**
     * @param ScContent\Service\ScDateTimeInterface $datetime
     * @return void
     */
    public function setDateTime(ScDateTimeInterface $datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Service\ScDateTimeInterface
     */
    public function getDateTime()
    {
        if (! $this->datetime instanceof ScDateTimeInterface) {
            throw new IoCException(
                'The datetime was not set.'
            );
        }
        return $this->datetime;
    }

    /**
     * @param ScContent\Entity\AbstractContent $content
     * @return void
     */
    public function saveContent(AbstractContent $content)
    {
        $mapper = $this->getContentMapper();
        $content = $this->prepareOld($content);
        $mapper->update($content);
    }

    /**
     * @param ScContent\Entity\AbstractContent $content
     * @return ScContent\Entity\AbstractContent
     */
    protected function prepareNew(AbstractContent $content)
    {
        $datetime = $this->getDateTime();
        $content->setName(Stdlib::randomKey(8))
            ->setAuthor($this->getUserId())
            ->setEditor($this->getUserId())
            ->setCreated($datetime->gmStamp())
            ->setModified($datetime->gmStamp());

        return $content;
    }

    /**
     * @param ScContent\Entity\AbstractContent $content
     * @return ScContent\Entity\AbstractContent
     */
    protected function prepareOld(AbstractContent $content)
    {
        $datetime = $this->getDateTime();
        $content->setEditor($this->getUserId())
            ->setModified($datetime->gmStamp());

        return $content;
    }

    /**
     * @return integer
     */
    protected function getUserId()
    {
        $authentication = $this->getAuthenticationService();
        if ($authentication->hasIdentity()) {
            return $authentication->getIdentity()->getId();
        }
        return 0;
    }
}
