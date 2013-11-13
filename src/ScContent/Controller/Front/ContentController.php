<?php
namespace ScContent\Controller\Front;

use ScContent\Controller\AbstractWidgetFront,
    Zend\View\Model\ViewModel;

class ContentController extends AbstractWidgetFront
{

    public function indexAction()
    {
        return new ViewModel();
    }
}