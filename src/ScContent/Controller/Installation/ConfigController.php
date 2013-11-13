<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller\Installation;

use ScContent\Controller\AbstractInstallation,
    ScContent\Entity\Installation\DatabaseConfig,
    ScContent\Mapper\Installation\ConfigMapper,
    ScContent\Form\Installation\DatabaseForm,
    //
    Zend\View\Model\ViewModel,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ConfigController extends AbstractInstallation
{
    /**
     * @var ScContent\Entity\Installation\DatabaseConfig
     */
    protected $entity;

    /**
     * @var ScContent\Mapper\Installation\ConfigMapper
     */
    protected $mapper;

    /**
     * @var ScContent\Form\Installation\DatabaseForm
     */
    protected $form;

    /**
     * Creates the internal configuration of the module.
     *
     * @return Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $redirect = $this->getRedirect();
        $routeMatch = $this->getEvent()->getRouteMatch();
        if (! $routeMatch->getParam('step')
            || ! $routeMatch->getParam('member')
        ) {
            return $this->redirect()->toUrl($redirect)->setStatusCode(303);
        }

        $step = $routeMatch->getParam('step');
        $member = $routeMatch->getParam('member');
        $options = $this->getModuleOptions()->getInstallation();
        $batch = $options['steps'][$step]['chain'][$member]['batch'];
        $form = $this->getForm();
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('sc-install', array('process' => 'process'))
        );
        $form->bind($this->getEntity());
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $mapper = $this->getConfigMapper();
                $databaseConfig = $form->getData();
                try {
                    $mapper->save($databaseConfig, $batch['source_file']);
                    return $this->redirect()->toUrl($redirect);
                } catch (Exception $e) {
                    return array(
                        'errors' => array($e->getMessage()),
                        'form' => $form,
                    );
                }
            }
        }
        return new ViewModel(array(
            'form' => $form
        ));
    }

    /**
     * @param ScContent\Entity\Installation\DatabaseConfig $entity
     * @return void
     */
    public function setEntity(DatabaseConfig $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return ScContent\Entity\Installation\DatabaseConfig
     */
    public function getEntity()
    {
        if (! $this->entity instanceof DatabaseConfig) {
            $this->entity = new DatabaseConfig();
        }
        return $this->entity;
    }

    /**
     * @param ScContent\Mapper\Installation\ConfigMapper $mapper
     * @return void
     */
    public function setMapper(ConfigMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return ScContent\Mapper\Installation\ConfigMapper
     */
    public function getConfigMapper()
    {
        if (! $this->mapper instanceof ConfigMapper) {
            $serviceLocator = $this->getServiceLocator();
            $this->mapper = $serviceLocator->get(
                'sc-mapper.installation.config'
            );
        }
        return $this->mapper;
    }

    /**
     * @param ScContent\Form\Installation\DatabaseForm $form
     * @return void
     */
    public function setForm(DatabaseForm $form)
    {
        $this->form = $form;
    }

    /**
     * @return ScContent\Form\Installation\DatabaseForm
     */
    public function getForm()
    {
        if (! $this->form instanceof DatabaseForm) {
            $formElementManager = $this->getServiceLocator()->get(
                'FormElementManager'
            );
            $this->form = $formElementManager->get(
                'sc-form.installation.database'
            );
        }
        return $this->form;
    }
}
