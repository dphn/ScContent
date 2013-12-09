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

use ScContent\Validator\ContentList\SearchDateRange,
    //
    Zend\Form\Form,
    //
    IntlDateFormatter,
    Locale;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentSearchForm extends Form
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('search');
        $this->setFormSpecification()->setInputSpecification();
    }

    /**
     * @return ContentSearchForm
     */
    protected function setFormSpecification()
    {
        $this->add([
            'name' => 'text',
            'type' => 'text',
            'options' => [
                'label' => 'Containing Text',
            ],
            'attributes' => [
                'id' => 'search-text',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'text_source',
            'type' => 'radio',
            'options' => [
                'value_options' => [
                    'title' => 'In the title',
                    'content' => 'In the content',
                    'description' => 'In the description',
                ],
            ],
            'attributes' => [
                'value' => 'title',
            ],
        ]);

        $this->add([
            'name' => 'date_type',
            'type' => 'radio',
            'options' => [
                'value_options' => [
                    'unknown' => 'Date of the latest changes is unknown',
                    'week' => 'Last week',
                    'month' => 'Last month',
                    'range' => 'In this range',
                ],
            ],
        ]);

        $this->add([
            'name' => 'date_start',
            'type' => 'text', // for browser compatibility
            'attributes' => [
                'id' => 'search-date-start',
                'class' => 'form-control input-sm',
            ],
        ]);

        $this->add([
            'name' => 'date_end',
            'type' => 'text', // for browser compatibility
            'attributes' => [
                'id' => 'search-date-end',
                'class' => 'form-control input-sm',
            ],
        ]);

        $this->add([
            'name' => 'modification_type',
            'type' => 'select',
            'options' => [
                'value_options' => [
                    'created' => 'Created',
                    'modified' => 'Modified',
                ],
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'user_name',
            'type' => 'text',
            'options' => [
                'label' => 'Containing Text',
            ],
            'attributes' => [
                'id' => 'search-user',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'user_source',
            'type' => 'radio',
            'options' => [
                'value_options' => [
                    'username' => 'In the name',
                    'email' => 'In the e-mail',
                ],
            ],
            'attributes' => [
                'value' => 'username',
            ],
        ]);

        $this->add([
            'name' => 'user_type',
            'type' => 'select',
            'options' => [
                'value_options' => [
                    'author' => 'Author',
                    'editor' => 'Editor',
                ],
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'search',
            'type' => 'button',
            'options' => [
                'label' => 'Search',
            ],
            'attributes' => [
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'search',
            ],
        ]);

        $this->add([
            'name' => 'clean',
            'type' => 'button',
            'options' => [
                'label' => 'Clean',
            ],
            'attributes' => [
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'clean',
            ],
        ]);

        return $this;
    }

    /**
     * @return ContentSearchForm
     */
    protected function setInputSpecification()
    {
        $spec = $this->getInputFilter();

        $spec->add([
            'name' => 'text',
            'required' => false,
            'validators' => [
                [
                    'name' => 'stringlength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 64,
                    ],
                ],
            ],
        ]);

        $spec->add([
            'name' => 'date_type',
            'required' => false,
            'validators' => [
                [
                    'name' => 'ScContent\Validator\ContentList\SearchDateRange',
                ],
            ],
        ]);

        $spec->add([
            'name' => 'date_start',
            'required' => false,
            'validators' => [
                [
                    'name' => 'datetime',
                    'options' => [
                        // l10n service sets actual Locale as application-wide Locale
                        'locale' => Locale::getDefault(),
                        'dateType' => IntlDateFormatter::SHORT,
                        'timeType' => IntlDateFormatter::NONE
                    ],
                ],
            ],
        ]);

        $spec->add([
            'name' => 'date_end',
            'required' => false,
            'validators' => [
                [
                    'name' => 'datetime',
                    'options' => [
                        // l10n service sets actual Locale as application-wide Locale
                        'locale' => Locale::getDefault(),
                        'dateType' => IntlDateFormatter::SHORT,
                        'timeType' => IntlDateFormatter::NONE
                    ],
                ],
            ],
        ]);

        $spec->add([
            'name' => 'user_name',
            'required' => false,
            'validators' => [
                [
                    'name' => 'stringlength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 64,
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
