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

use Zend\Form\Element\Collection,
    Zend\Form\FormInterface,
    Zend\Form\Form;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileEdit extends Form
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('files');
        $this->setFormSpecification();
    }

    /**
     * @param object $object
     * @param integer $flags
     * @return FileEdit
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        // remove old (dummy) elements
        $collection = $this->get('files');
        $count = $collection->getCount();
        for ($i = 0; $i < $count; $i ++) {
            $collection->remove($i);
        }
        // create actual elements
        $collection->setCount(count($object));
        $collection->prepareFieldset();

        // bind list
        parent::bind($object);

        // bind items
        $hydrator = $collection->getHydrator();
        foreach ($object as $i => $item) {
            $collection->get($i)->setObject($item)->populateValues(
                $hydrator->extract($item)
            );
        }
        return $this;
    }

    /**
     * @return FileEdit
     */
    protected function setFormSpecification()
    {
        $this->add(array(
            'type' => 'collection',
            'name' => 'files',
            'options' => array(
                'allow_add' => false,
                'allow_remove' => false,
                'target_element' => new FileEditFieldset(),
                'should_create_template' => false,
            ),
        ));

        $collection = $this->get('files');
        $collection->setHydrator($collection->getHydrator());

        $this->add(array(
            'name' => 'save',
            'type' => 'button',
            'options' => array(
                'label' => 'Save',
            ),
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'save',
            ),
        ));

        $this->setValidationGroup(array(
            'files' => array(
                'title',
                'description',
                'content',
            )
        ));

        return $this;
    }
}
