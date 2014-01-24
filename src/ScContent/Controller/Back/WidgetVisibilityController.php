<?php

namespace ScContent\Controller\Back;

use ScContent\Controller\AbstractBack,
    //
    Zend\View\Model\ViewModel;

class WidgetVisibilityController extends AbstractBack
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
