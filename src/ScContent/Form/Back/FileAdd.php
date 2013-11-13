<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Form\Back;

use ScContent\InputFilter\FileInput,
    ScContent\Filter\File\MimeType,
    ScContent\Validator\File\FileName,
    ScContent\Validator\File\FileType,
    //
    Zend\InputFilter\InputFilterInterface,
    Zend\InputFilter\InputFilter,
    Zend\Form\Exception\DomainException,
    Zend\Form\Form;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileAdd extends Form
{
    /**
     * @var ScContent\Filter\File\MimeType
     */
    protected $mimeTypeFilter;

    /**
     * @var ScContent\Validator\File\FileName
     */
    protected $fileNameValidator;

    /**
     * @var ScContent\Validator\File\FileType
     */
    protected $fileTypeValidator;

    /**
     * Constructor
     *
     * @param MimeType $mimeTypeFilter
     * @param FileName $fileNameValidator
     * @param FileType $fileTypeValidator
     */
    public function __construct(
        MimeType $mimeTypeFilter,
        FileName $fileNameValidator,
        FileType $fileTypeValidator
    ) {
        $this->mimeTypeFilter = $mimeTypeFilter;
        $this->fileNameValidator = $fileNameValidator;
        $this->fileTypeValidator = $fileTypeValidator;

        parent::__construct('upload');
        $this->setFormSpecification()->setInputSpecification();
    }

    /**
     * @throw Zend\Form\Exception\DomainException
     * @return boolean
     */
    public function isValid()
    {
        if ($this->hasValidated) {
            return $this->isValid;
        }

        $this->isValid = false;
        if (! is_array($this->data) && ! is_object($this->object)) {
            throw new DomainException(sprintf(
                '%s is unable to validate as there is no data currently set',
                __METHOD__
            ));
        }
        if (! is_array($this->data)) {
            $data = $this->extract();
            if (! is_array($data)) {
                throw new DomainException(sprintf(
                    '%s is unable to validate as there is no data currently set',
                    __METHOD__
                ));
            }
            $this->data = $data;
        }

        $filter = $this->filter;

        $filter->setData($this->data);
        $filter->setValidationGroup(InputFilterInterface::VALIDATE_ALL);

        $validationGroup = $this->getValidationGroup();
        if ($validationGroup !== null) {
            $this->prepareValidationGroup($this, $this->data, $validationGroup);
            $filter->setValidationGroup($validationGroup);
        }

        $this->isValid = $result = $filter->isValid();
        $this->hasValidated = true;
        if ($result && $this->bindOnValidate()) {
            $this->bindValues();
        }
        if (! $result) {
            $this->setMessages($filter->getMessages());
        }

        return $result;
    }

    /**
     * @return FileAdd
     */
    protected function setFormSpecification()
    {
        $this->add(array(
            'name' => 'file',
            'type' => 'file',
            'options' => array(
                'label' => 'Upload file'
            ),
            'attributes' => array(
                'multiple' => true,
                'id' => 'file',
            )
        ));

        $this->add(array(
            'name' => 'upload',
            'type' => 'button',
            'options' => array(
                'label' => 'Upload',
            ),
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'upload',
            ),
        ));

        return $this;
    }

    /**
     * @return FileAdd
     */
    protected function setInputSpecification()
    {
        $spec = new InputFilter();

        $fileInput = new FileInput('file');
        $fileInput->setRequired(true);

        $fileInput->getFilterChain()
            ->attach($this->mimeTypeFilter);

        $fileInput->getValidatorChain()
            ->addValidator($this->fileNameValidator)
            ->addValidator($this->fileTypeValidator)
            ->attachByName('filesize', array('max' => 2097152));
            // @todo  ->attachByName('filecount', array('max' => 10));

        $spec->add($fileInput);
        $this->setInputFilter($spec);
        return $this;
    }
}
