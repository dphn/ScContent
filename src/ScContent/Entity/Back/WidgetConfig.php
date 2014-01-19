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

use ScContent\Entity\AbstractEntity,
    ScContent\Entity\WidgetInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetConfig extends AbstractEntity
{
    /**
     * @var ScContent\Entity\WidgetInterface
     */
    protected $widget;

    /**
     * A list of roles that are known to the system.
     *
     * @var array
     */
    protected $availableRoles = [];

    /**
     * @param array $availableRoles
     */
    public function __construct(WidgetInterface $widget, $availableRoles)
    {
        $this->widget = $widget;
        $this->availableRoles = $availableRoles;
    }

    /**
     * @param string $name
     * @return WidgetConfig
     */
    public function setDisplayName($name)
    {
        $this->widget->setDisplayName($name);
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->widget->getDisplayName();
    }

    /**
     * @param string $description
     * @return WidgetConfig
     */
    public function setDescription($description)
    {
        $this->widget->setDescription($description);
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->widget->getDescription();
    }

    /**
     * This data comes from the form. It is the applicable roles list.
     *
     * @param array $roles
     * @return void
     */
    public function setRoles($roles)
    {
        $widget = $this->widget;
        $newRoles = [];
        foreach ($this->availableRoles as $role) {
            $newRoles[$role] = in_array($role, $roles);
        }
        $widget->setOption('roles', $newRoles);
    }

    /**
     * Derive all roles that are known to the system.
     */
    public function getRoles()
    {
        return $this->availableRoles;
    }

    /**
     * Creates a list of applicable roles.
     *
     * @return array
     */
    public function findApplicableRoles()
    {
        $widget = $this->widget;
        $array = [];
        foreach ($this->availableRoles as $role) {
            if ($widget->isApplicable($role)) {
                $array[] = $role;
            }
        }
        return $array;
    }
}
