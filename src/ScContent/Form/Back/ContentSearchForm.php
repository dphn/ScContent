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
    Zend\InputFilter\InputFilter,
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
        $this->add(array(
            'name' => 'text',
            'type' => 'text',
            'options' => array(
                'label' => 'Containing Text',
            ),
            'attributes' => array(
                'id' => 'search-text',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'text_source',
            'type' => 'radio',
            'options' => array(
                'value_options' => array(
                    'title' => 'In the title',
                    'content' => 'In the content',
                    'description' => 'In the description',
                ),
            ),
            'attributes' => array(
                'value' => 'title',
            ),
        ));

        $this->add(array(
            'name' => 'date_type',
            'type' => 'radio',
            'options' => array(
                'value_options' => array(
                    'unknown' => 'Date of the latest changes is unknown',
                    'week' => 'Last week',
                    'month' => 'Last month',
                    'range' => 'In this range',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'date_start',
            'type' => 'text', // for browser compatibility
            'attributes' => array(
                'id' => 'search-date-start',
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'date_end',
            'type' => 'text', // for browser compatibility
            'attributes' => array(
                'id' => 'search-date-end',
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'modification_type',
            'type' => 'select',
            'options' => array(
                'value_options' => array(
                    'created' => 'Created',
                    'modified' => 'Modified',
                ),
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'user_name',
            'type' => 'text',
            'options' => array(
                'label' => 'Containing Text',
            ),
            'attributes' => array(
                'id' => 'search-user',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'user_source',
            'type' => 'radio',
            'options' => array(
                'value_options' => array(
                    'username' => 'In the name',
                    'email' => 'In the e-mail',
                ),
            ),
            'attributes' => array(
                'value' => 'username',
            ),
        ));

        $this->add(array(
            'name' => 'user_type',
            'type' => 'select',
            'options' => array(
                'value_options' => array(
                    'author' => 'Author',
                    'editor' => 'Editor',
                ),
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'search',
            'type' => 'button',
            'options' => array(
                'label' => 'Search',
            ),
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'search',
            ),
        ));

        $this->add(array(
            'name' => 'clean',
            'type' => 'button',
            'options' => array(
                'label' => 'Clean',
            ),
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'clean',
            ),
        ));

        return $this;
    }

    /**
     * @return ContentSearchForm
     */
    protected function setInputSpecification()
    {
        $spec = new InputFilter();

        $spec->add(array(
            'name' => 'text',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'stringlength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 64,
                    ),
                ),
            ),
        ));

        $spec->add(array(
            'name' => 'date_type',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'ScContent\Validator\ContentList\SearchDateRange',
                ),
            ),
        ));

        $spec->add(array(
            'name' => 'date_start',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'datetime',
                    'options' => array(
                        // l10n service sets actual Locale as application-wide Locale
                        'locale' => Locale::getDefault(),
                        'dateType' => IntlDateFormatter::SHORT,
                        'timeType' => IntlDateFormatter::NONE
                    ),
                ),
            ),
        ));

        $spec->add(array(
            'name' => 'date_end',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'datetime',
                    'options' => array(
                        // l10n service sets actual Locale as application-wide Locale
                        'locale' => Locale::getDefault(),
                        'dateType' => IntlDateFormatter::SHORT,
                        'timeType' => IntlDateFormatter::NONE
                    ),
                ),
            ),
        ));

        $spec->add(array(
            'name' => 'user_name',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'stringlength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 64,
                    ),
                ),
            ),
        ));

        $this->setInputFilter($spec);

        return $this;
    }
}
