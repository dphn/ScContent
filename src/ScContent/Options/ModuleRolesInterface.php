<?php

interface ModuleRolesInterface
{
    function setRoles($roles);

    function getRoles();

    function setAdministratorRole($name);

    function getAdministratorRole();

    function setDefaultRegistrationRole($name);

    function getDefaultRegistrationRole();
}
