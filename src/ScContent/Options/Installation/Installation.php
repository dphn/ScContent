<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Options\Installation;

use ScContent\Entity\AbstractList,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DomainException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Installation extends AbstractList
{
    /**
     * @var string
     */
    protected $moduleName = '';

    /**
     * @var string
     */
    protected $layout = 'sc-default/layout/installation/index';

    /**
     * @var string
     */
    protected $template = 'sc-default/template/installation/index';

    /**
     * @var string
     */
    protected $brand = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var mixed
     */
    protected $redirectOnSuccess = 'sc-admin/content-manager';

    /**
     * @var string
     */
    protected $currentStepName = '';

    /**
     * Steps
     *
     * @var Step[string]
     */
    protected $items = [];

    /**
     * @param string $moduleName
     * @param array  $options optional
     */
    public function __construct($moduleName, $options = [])
    {
        $this->moduleName = $moduleName;

        if (array_key_exists('layout', $options)) {
            $this->layout = $options['layout'];
        }
        if (array_key_exists('template', $options)) {
            $this->template = $options['template'];
        }
        if (array_key_exists('brand', $options)) {
            $this->brand = $options['brand'];
        }
        if (array_key_exists('title', $options)) {
            $this->title = $options['title'];
        }
        if (array_key_exists('redirect_on_success', $options)) {
            $this->redirectOnSuccess = $options['redirect_on_success'];
        }
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @param  string $layout
     * @return void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param  string $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param  string $brand
     * @return void
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param  string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param  mixed $redirect
     * @return void
     */
    public function setRedirectOnSuccess($redirect)
    {
        $this->redirectOnSuccess = $redirect;
    }

    /**
     * @return mixed
     */
    public function getRedirectOnSuccess()
    {
        return $this->redirectOnSuccess;
    }

    /**
     * @param  Step $step
     * @return void
     */
    public function addStep(Step $step)
    {
        $this->items[$step->getName()] = $step;
    }

    /**
     * @param  string $name
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return Step
     */
    public function getStep($name)
    {
        if (! array_key_exists($name, $this->items)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown step '%s'.",
                $name
            ));
        }
        return $this->items[$name];
    }

    /**
     * @param  string $name
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function setCurrentStepName($name)
    {
        if (! array_key_exists($name, $this->items)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown step '%s',",
                $name
            ));
        }
        $this->currentStepName = $name;
    }

    /**
     * @throws \ScContent\Exception\DomainException
     * @return string
     */
    public function getCurrentStepName()
    {
        if ('' === $this->currentStepName) {
            throw new DomainException(
                "The current step name was not set."
            );
        }
        return $this->currentStepName;
    }

    /**
     * @return Step
     */
    public function getCurrentStep()
    {
        return $this->items[$this->getCurrentStepName()];
    }
}
