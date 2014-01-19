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
interface WidgetInterface extends EntityInterface
{
    /**
     * @param null | integer | string $id
     * @return WidgetInterface
     */
    function setId($id);

    /**
     * @return null | integer | string
     */
    function getId();

    /**
     * @param string $name
     * @return WidgetInterface
     */
    function setTheme($name);

    /**
     * @return string
     */
    function getTheme();

    /**
     * @param string $region
     * @return WidgetInterface
     */
    function setRegion($region);

    /**
     * @return string
     */
    function getRegion();

    /**
     * @param string $name
     * @return WidgetInterface
     */
    function setName($name);

    /**
     * @return string
     */
    function getName();

    /**
     * @param string $name
     * @return WidgetInterface
     */
    function setDisplayName($name);

    /**
     * @return string
     */
    function getDisplayName();

    /**
     * @return boolean
     */
    function hasDescription();

    /**
     * @param string $description
     * @return WidgetInterface
     */
    function setDescription($description);

    /**
     * @return string
     */
    function getDescription();

    /**
     * @param string | array $options Array of options or a serialized options as string
     * @return WidgetInterface
     */
    function setOptions($options);

    /**
     * @param boolean $serialized optional default true
     * @return array | string
     */
    function getOptions($serialized = true);

    /**
     * @param string $name
     * @param mixed $value
     * @return WidgetInterface
     */
    function setOption($name, $value);

    /**
     * @param string $name
     * @param mixed $default optional default null
     * @return mixed
     */
    function findOption($name, $default = null);

   /**
    * @param string $role
    * @return boolean
    */
    public function isApplicable($role);

    /**
     * @param integer $position
     * @return WidgetInterface
     */
    function setPosition($position);

    /**
     * @return integer
     */
    function getPosition();
}
