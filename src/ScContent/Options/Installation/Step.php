<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Options\Installation;

use ScContent\Entity\AbstractList,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DomainException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Step extends AbstractList
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $layout = '';

    /**
     * @var string
     */
    protected $template = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $header = '';

    /**
     * @var string
     */
    protected $info = '';

    /**
     * @var string
     */
    protected $currentMemberName = '';

    /**
     * Chain members
     *
     * @var Member[string]
     */
    protected $items = [];

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options optional
     */
    public function __construct($name, $options = [])
    {
        $this->name = $name;

        if (isset($options['layout'])) {
            $this->layout = $options['layout'];
        }
        if (isset($options['template'])) {
            $this->template = $options['template'];
        }
        if (isset($options['title'])) {
            $this->title = $options['title'];
        }
        if (isset($options['header'])) {
            $this->header = $options['header'];
        }
        if (isset($options['info'])) {
            $this->info = $options['info'];
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
     * @param  string $layout
     * @return void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param  string $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param  string $header
     * @return void
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param  string $info
     * @return void
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param  Member $member
     * @return void
     */
    public function addMember(Member $member)
    {
        $this->items[$member->getName()] = $member;
    }

    /**
     * @param  string $name
     * @throws \ScConent\Exception\InvalidArgumentException
     * @return Member
     */
    public function getMember($name)
    {
        if (! isset($this->items[$name])) {
            throw new InvalidArgumentException(sprintf(
                "Unknown member of installation chain '%s'.",
                $name
            ));
        }
        return $this->items[$name];
    }

    /**
     * @param  string $name
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function setCurrentMemberName($name)
    {
        if (! array_key_exists($name, $this->items)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown member of chain '%s'.",
                $name
            ));
        }
        $this->currentMemberName = $name;
    }

    /**
     * @throws \ScContent\Exception\DomainException
     * @return string
     */
    public function getCurrentMemberName()
    {
        if ('' === $this->currentMemberName) {
            throw new DomainException(
                'The current member of chain was not set.'
            );
        }
        return $this->currentMemberName;
    }

    /**
     * @return Member
     */
    public function getCurrentMember()
    {
        return $this->items[$this->getCurrentMemberName()];
    }
}
