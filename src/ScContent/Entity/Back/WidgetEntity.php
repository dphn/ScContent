<?php

namespace ScContent\Entity\Back;

use ScContent\Entity\WidgetItem;

class WidgetEntity extends WidgetItem
{
    /**
     * A list of roles that are known to the system.
     *
     * @var array
     */
    protected $availableRoles = [];

    /**
     * @param array $availableRoles
     */
    public function __construct($availableRoles)
    {
        $this->availableRoles = $availableRoles;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $array = parent::getArrayCopy();
        if (isset($array['options'])) {
            $array['options'] = serialize($array['options']);
        }
        unset($array['roles']);
        return $array;
    }

    /**
     * This data comes from the form. It is the applicable roles list.
     *
     * @param array $roles
     * @return void
     */
    public function setRoles($roles)
    {
        $this->options['roles'] = [];
        foreach ($this->availableRoles as $role) {
            $this->options['roles'][$role] = in_array($role, $roles);
        }
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
        $array = [];
        foreach ($this->availableRoles as $role) {
            if ($this->isApplicable($role)) {
                $array[] = $role;
            }
        }
        return $array;
    }
}
