<?php

namespace ScContent\Factory\Navigation;

use Zend\Navigation\Service\AbstractNavigationFactory;

class BackendNavigationFactory extends AbstractNavigationFactory
{
    public function getName()
    {
        return 'sc-backend';
    }
}