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
    ScContent\Form\Back\FileEditForm,
    ScContent\Form\Back\FileAddForm,
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
     * @var ScContent\Form\Back\FileAddForm
     */
    protected $fileAddForm;

    /**
     * @var ScContent\Form\Back\FileEditForm
     */
    protected $fileEditForm;

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
                $this->scTranslate('The file location was not specified.')
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
                ['parent' => $parent]
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
                        ['id' => implode(',', $fileIds)]
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
        return new ViewModel([
            'form' => $form,
        ]);
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
                $this->scTranslate('The file ID(s) was not specified.')
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
                $this->scTranslate('No files, available for editing.')
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
                ['id' => $id]
            )
        );
        $form->bind($filesList);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $this->getFileService()->saveFiles($filesList);
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
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
            $this->fileService = $serviceLocator->get('ScService.Back.File');
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
                'ScService.FileTransfer'
            );
        }
        return $this->fileTransfer;
    }

    /**
     * @param ScContent\Form\Back\FileAddForm $form
     * @return void
     */
    public function setFileAddForm(FileAddForm $form)
    {
        $this->fileAddForm = $form;
    }

    /**
     * @return ScContent\Form\Back\FileAddForm
     */
    public function getFileAddForm()
    {
        if (! $this->fileAddForm instanceof FileAddForm) {
            $formElementManager = $this->getServiceLocator()->get(
                'FormElementManager'
            );
            $this->fileAddForm = $formElementManager->get(
                'ScForm.Back.FileAdd'
            );
        }
        return $this->fileAddForm;
    }

    /**
     * @param ScContent\Form\Back\FileEditForm $form
     * @return void
     */
    public function setFileEditForm(FileEditForm $form)
    {
        $this->fileEditForm = $form;
    }

    /**
     * @return ScContent\Form\Back\FileEditForm
     */
    public function getFileEditForm()
    {
        if (! $this->fileEditForm instanceof FileEditForm) {
            $formElementManager = $this->getServiceLocator()->get(
                'FormElementManager'
            );
            $this->fileEditForm = $formElementManager->get(
                'ScForm.Back.FileEdit'
            );
        }
        return $this->fileEditForm;
    }
}
