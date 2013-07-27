<?php

return array(
    'factories' => array(
        'sc-content' => function($sm) {
            $config = $sm->getServiceLocator()->get('Config');
            $ctr =  new ScContent\Controller\FrontController();
            $ctr->setConfig($config['sc']);
            return $ctr;
        }
    ),
);