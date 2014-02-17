<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity\Back;

use ScContent\Entity\AbstractEntity;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Theme extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $displayName = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $screenshot = '';

    /**
     * @param  string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return void
     */
    public function setDisplayName($name)
    {
        $this->displayName = $name;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if (! $this->displayName) {
            return $this->name;
        }
        return $this->displayName;
    }

    /**
     * @param  string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param  string $screenshot
     * @return void
     */
    public function setScrrenshot($screenshot)
    {
        $this->screenshot = $screenshot;
    }

    /**
     * @return string
     */
    public function getScreenshot()
    {
        return $this->screenshot;
    }
}
