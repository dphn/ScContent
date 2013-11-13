<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller\Back;

use ScContent\Controller\AbstractBack,
    ScContent\Service\Back\FileService,
    ScContent\Service\FileTransferInterface,
    ScContent\Service\FileTransfer,
    ScContent\Form\Back\FileEdit,
    ScContent\Form\Back\FileAdd,
    ScContent\Exception\RuntimeException,
    ScContent\Exception\DebugException,
    //
    Zend\View\Model\ViewModel,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileController extends AbstractBack
{
    /**
     * @var ScContent\Service\Back\FileService
     */
    protected $fileService;

    /**
     * @var ScContent\Service\FileTransfer
     */
    protected $fileTransfer;

    /**
     * @var ScContent\Form\Back\FileAdd
     */
    protected $addFileForm;

    /**
     * @var ScContent\Form\Back\FileEdit
     */
    protected $editFileForm;

    /**
     * Add File.
     *
     * @return Zend\Stdlib\ResponseInterface | Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $parent = $this->params()->fromRoute('parent');
        if (! is_numeric($parent)) {
            $this->flashMessenger()->addMessage(
                'The file location is not specified.'
            );
            return $this->redirect()
                ->toRoute('sc-admin/content-manager')
                ->setStatusCode(303);
        }

        $form = $this->getAddFileForm();
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'sc-admin/file/add',
                array('parent' => $parent)
            )
        );
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()) {
                $transfer = $this->getFileTransfer();
                $data = $transfer->receive($form->getData());
                try {
                    $fileIds = $this->getFileService()->makeFiles(
                        $parent, $data
                    );
                    return $this->redirect()->toRoute(
                        'sc-admin/file/edit',
                        array('id' => implode(',', $fileIds))
                    );
                } catch (Exception $e) {
                    $transfer->rollBack($data);
                    if ($e instanceof RuntimeException) {
                        $this->flashMessenger()->addMessage($e->getMessage());
                        return $this->redirect()
                            ->toRoute('sc-admin/content-manager')
                            ->setStatusCode(303);
                    } elseif (DEBUG_MODE) {
                        throw new DebugException(
                            $e->getMessage(),
                            $e->getCode(),
                            $e
                        );
                    }
                }
            }
        }
        return new ViewModel(array(
            'form' => $form
        ));
    }

    /**
     * Edit File(s).
     *
     * @return Zend\Stdlib\ResponseInterface | Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $ids = array();
        if (! empty($id) && ! is_array($id)) {
            $ids = explode(',', $id);
        }
        if (empty($ids)) {
            $this->flashMessenger()->addMessage(
                $this->translate('The file ID(s) was not specified.')
            );
            return $this->redirect()
                ->toRoute('sc-admin/content-manager')
                ->setStatusCode(303);
        }

        try {
            $filesList = $this->getFileService()->getFilesList($ids);
        } catch (RuntimeException $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
            return $this->redirect()
                ->toRoute('sc-admin/content-manager')
                ->setStatusCode(303);
        }
        if ($filesList->isEmpty()) {
            $this->flashMessenger()->addMessage(
                $this->translate('No files, available for editing.')
            );
            return $this->redirect()
                ->toRoute('sc-admin/content-manager')
                ->setStatusCode(303);
        }

        $form = $this->getEditFileForm();
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'sc-admin/file/edit',
                array('id' => $id)
            )
        );
        $form->bind($filesList);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $this->getFileService()->saveFiles($filesList);
            }
        }
        return new ViewModel(array(
            'form' => $form
        ));
    }

    /**
     * @param ScContent\Service\Back\FileService $service
     * @return void
     */
    public function setFileService(FileService $service)
    {
        $this->fileService = $service;
    }

    /**
     * @return ScContent\Service\Back\FileService
     */
    public function getFileService()
    {
        if (! $this->fileService instanceof FileService) {
            $serviceLocator = $this->getServiceLocator();
            $this->fileService = $serviceLocator->get('sc-service.back.file');
        }
        return $this->fileService;
    }

    /**
     * @param ScContent\Service\FileTransferInterface $fileTransfer
     * @return void
     */
    public function setFileTransfer(FileTransferInterface $fileTransfer)
    {
        $this->fileTransfer = $fileTransfer;
    }

    /**
     * @return ScContent\Service\FileTransferInterface
     */
    public function getFileTransfer()
    {
        if (! $this->fileTransfer instanceof FileTransfer) {
            $serviceLocator = $this->getServiceLocator();
            $this->fileTransfer = $serviceLocator->get(
                'sc-service.file.transfer'
            );
        }
        return $this->fileTransfer;
    }

    /**
     * @param ScContent\Form\Back\FileAdd $form
     * @return void
     */
    public function setAddFileForm(FileAdd $form)
    {
        $this->addFileForm = $form;
    }

    /**
     * @return ScContent\Form\Back\FileAdd
     */
    public function getAddFileForm()
    {
        if (! $this->addFileForm instanceof FileAdd) {
            $formElementManager = $this->getServiceLocator()->get(
                'FormElementManager'
            );
            $this->addFileForm = $formElementManager->get(
                'sc-form.back.file.add'
            );
        }
        return $this->addFileForm;
    }

    /**
     * @param ScContent\Form\Back\FileEdit $form
     * @return void
     */
    public function setEditFileForm(FileEdit $form)
    {
        $this->editFileForm = $form;
    }

    /**
     * @return ScContent\Form\Back\FileEdit
     */
    public function getEditFileForm()
    {
        if (! $this->editFileForm instanceof FileEdit) {
            $formElementManager = $this->getServiceLocator()->get(
                'FormElementManager'
            );
            $this->editFileForm = $formElementManager->get(
                'sc-form.back.file.edit'
            );
        }
        return $this->editFileForm;
    }
}
