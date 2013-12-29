<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\View\Helper;

use ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DomainException,
    //
    Zend\View\Helper\Placeholder\Container\AbstractStandalone;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class BodyTag extends AbstractStandalone
{
    /**#@+
     * @const string
     */
    const StateClosed = 'closed';
    const StateOpen   = 'open';
    /**#@-*/

    /**
     * @var string
     */
    protected $state = self::StateClosed;

    /**
     * @var string
     */
    protected $regKey = 'ScContent_View_Helper_BodyTag';

    /**
     * @var array
     */
    protected $generalAttributes = [
        'class', 'contenteditable', 'contextmenu', 'dir', 'id', 'lang',
        'spellcheck', 'style', 'title', 'translate',
    ];

    /**
     * @var array
     */
    protected $eventHandlerAttributes = [
        'onclick', 'oncontextmenu', 'ondblclick', 'oninvalid', 'onkeydown',
        'onkeypress', 'onkeyup', 'onload', 'onmousedown', 'onmousemove',
        'onmouseout', 'onmouseover', 'onmouseup', 'onscroll',
    ];

    /**
     * @param void
     */
    public function __construct()
    {
        parent::__construct();

        $this->setSeparator(' ');
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return BodyTag
     */
    public function addAttribute($attribute, $value)
    {
        return call_user_func_array(array($this, '__invoke'), func_get_args());
    }

    /**
     * @param string $attribute
     * @param string $value
     * @throw ScContent\Exception\InvalidArgumentException
     * @return BodyTag
     */
    public function __invoke($attribute = null, $value = null)
    {
        if (!is_null($attribute)) {
            if (! $this->isValid($attribute)) {
                throw new InvalidArgumentException(sprintf(
                    "Unknown attribute '%s'",
                    $attribute
                ));
            }
            $container = $this->getContainer();
            if (is_null($value) && isset($container[$attribute])) {
                unset($container[$attribute]);
                return $this;
            }
            $value = $this->prepareValue($attribute, $value);

            if (! isset($container[$attribute])) {
                $container[$attribute] = [$value];
            } else {
                $container[$attribute][] = $value;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if ($this->state == self::StateClosed) {
            $container = $this->getContainer();

            $tag = '';
            foreach ($container as $attribute => $values) {
                $value = implode($this->getSeparator(), array_unique($values));
                $tag .= sprintf(' %s="%s"', $attribute, $value);
            }
            $tag = sprintf('<body%s>', $tag);
            $this->state = self::StateOpen;
            return $tag;
        }
        $this->resetContainer();
        $this->state = self::StateClosed;
        return '</body>';
    }

    /**
     * @param string $attribute
     * @return boolean
     */
    protected function isValid($attribute)
    {
        $attribute = strtolower($attribute);

        if (in_array($attribute, $this->generalAttributes)
            || in_array($attribute, $this->eventHandlerAttributes)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return string
     */
    protected function prepareValue($attribute, $value)
    {
        $attribute = strtolower($attribute);

        if (in_array($attribute, $this->eventHandlerAttributes)) {
            $value = rtrim($value, ';');
            $value .= ';';
        }
        return $value;
    }

    /**
     * @return void
     */
    protected function resetContainer()
    {
        $this->deleteContainer();
        $this->setContainer($this->getContainer());
    }
}
