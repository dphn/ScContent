<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\InputFilter;

use Zend\Validator\File\UploadFile as UploadValidator,
    Zend\InputFilter\InputInterface,
    Zend\InputFilter\Input;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 *
 * The code for this class is inspired from the standard ZF2
 * class "Zend\InputFilter\FileInput".
 *
 * The main difference is that the validators are run **AFTER** the filters.
 */
class FileInput extends Input
{
    /**
     * @var bool
     */
    protected $isValid = false;

    /**
     * @var bool
     */
    protected $autoPrependUploadValidator = true;

    /**
     * @param  bool $value Enable/Disable automatically prepending an Upload validator
     * @return FileInput
     */
    public function setAutoPrependUploadValidator($value)
    {
        $this->autoPrependUploadValidator = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoPrependUploadValidator()
    {
        return $this->autoPrependUploadValidator;
    }

    /**
     * @param  mixed $context Extra "context" to provide the validator and filter
     * @return mixed
     */
    public function getValue($context = null)
    {
        if (empty($this->value) && is_array($context)) {
            $this->value = $context;
        }
        return $this->value;
    }

    /**
     * @param  mixed $context Extra "context" to provide the validator and filter
     * @return bool
     */
    public function isValid($context = null)
    {
        if (! $this->continueIfEmpty()) {
            $this->injectNotEmptyValidator();
        }

        $validator = $this->getValidatorChain();

        $value = $this->getValue($context);

        if (! $this->injectUploadValidator($context)) {
            return false;
        }

        $value = $this->filterValue();

        if (is_array($value) && isset($value['tmp_name'])) {
            // Single file input
            $this->isValid = $validator->isValid($value, $context);
        } elseif (is_array($value) && ! empty($value)
                    && isset($value[0]['tmp_name'])
        ) {
            // Multi file input (multiple attribute set)
            $this->isValid = true;
            foreach ($value as $item) {
                if(!$validator->isValid($item, $context)) {
                    $this->isValid = false;
                    break; // Do not continue processing files if validation fails
                }
            }
        }
        return $this->isValid;
    }

    /**
     * @param  mixed $context Extra "context" to provide the validator and filter
     * @return bool
     */
    protected function injectUploadValidator($context = null)
    {
        if (! $this->autoPrependUploadValidator) {
            return true;
        }

        $value = $this->getValue();
        if (is_array($value) && isset($value['file'])) {
            $value = $value['file'];
        }

        $validator = new UploadValidator();

        if (is_array($value) && isset($value['tmp_name'])) {
            // Single file input
            $this->isValid = $validator->isValid($value, $context);
        } elseif (is_array($value) && ! empty($value)
                    && isset($value[0]['tmp_name'])
        ) {
            // Multi file input (multiple attribute set)
            $this->isValid = true;
            foreach ($value as $item) {
                if (! $validator->isValid($item, $context)) {
                    $this->isValid = false;
                    break; // Do not continue processing files if validation fails
                }
            }
        }
        $this->autoPrependUploadValidator = false;

        return $this->isValid;
    }

    /**
     * @return null | array
     */
    protected function filterValue()
    {
        $value = $this->getValue();
        if (is_array($value) && isset($value['file'])) {
            $value = $value['file'];
        }

        if (is_array($value)) {
            $filter = $this->getFilterChain();
            if (isset($value['tmp_name'])) {
                // Single file input
                $value = $filter->filter($value);
            } else {
                // Multi file input (multiple attribute set)
                $newValue = [];
                foreach ($value as $fileData) {
                    if (is_array($fileData) && isset($fileData['tmp_name'])) {
                        $newValue[] = $filter->filter($fileData);
                    }
                }
                $value = $newValue;
            }
        }
        return $value;
    }

    /**
     * No-op, NotEmpty validator does not apply for FileInputs.
     * See also: BaseInputFilter::isValid()
     *
     * @return void
     */
    protected function injectNotEmptyValidator()
    {
        $this->notEmptyValidator = true;
    }

    /**
     * @param  InputInterface $input
     * @return FileInput
     */
    public function merge(InputInterface $input)
    {
        parent::merge($input);
        if ($input instanceof FileInput) {
            $this->setAutoPrependUploadValidator(
                $input->getAutoPrependUploadValidator()
            );
        }
        return $this;
    }
}
