<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetItem extends AbstractEntity
{
    /**
     * @var integer | null
     */
    protected $id;

    /**
     * @var string
     */
    protected $theme = '';

    /**
     * @var string
     */
    protected $region = '';

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
     * @var array
     */
    protected $options = [];

    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * @param integer $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setTheme($name)
    {
        $this->theme = $name;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $region
     * @return void
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $name
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
     * @param string $name
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
        return $this->displayName;
    }

    /**
     * @return boolean
     */
    public function hasDescription()
    {
        return ! empty($this->description);
    }

    /**
     * @param string $description
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
     * @param string | array $options
     * @return void
     */
    public function setOptions($options)
    {
        if (is_string($options)) {
            $options = unserialize($options);
        }
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function findOption($name, $default = null)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }
        return $default;
    }

    /**
     * @param string $role
     * @return boolean
     */
    public function isApplicable($role)
    {
        if (isset($this->options['roles'][$role])) {
            return (bool) (int) $this->options['roles'][$role];
        }
        return true;
    }

    /**
     * @param integer $position
     * @return void
     */
    public function setPosition($position)
    {
        $this->position = (int) $position;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}
