<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Back;

use ScContent\Listener\AbstractControllerListener,
    ScContent\Mapper\Back\LayoutReorderMapper,
    ScContent\Options\ModuleOptions,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DomainException,
    ScContent\Exception\DebugException,
    ScContent\Exception\IoCException,
    //
    Zend\EventManager\EventInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutReorder extends AbstractControllerListener
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var ScContent\Mapper\Back\LayoutReorderMapper
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $redirectRoute = 'sc-admin/layout/index';

    /**#@+
     * @const string
     */
    const SourceNotFound  = 'Source not found';
    const UnexpectedError = 'Unexpected error';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = [
        self::SourceNotFound
            => 'Unable to change the position of the widget with identifier %s. The widget was not found.',

        self::UnexpectedError
            => 'Unable to change the position of %s.',
    ];

    /**
     * @param ScContent\Options\ModuleOptions $options
     * @return void
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
    }

    /**
     * @return ScContent\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions instanceof ModuleOptions) {
            throw new IoCException(
                'The module options were not set.'
            );
        }
        return $this->moduleOptions;
    }

    /**
     * @param ScContent\Mapper\Back\LayoutReorderMapper $mapper
     * @return void
     */
    public function setMapper(LayoutReorderMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\LayoutReorderMapper
     */
    public function getMapper()
    {
        if (! $this->mapper instanceof LayoutReorderMapper) {
            throw new IoCException(
                'The mapper was not set.'
            );
        }
        return $this->mapper;
    }

    /**
     * @param Zend\EventManager\EventInterface $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @throws ScContent\Exception\DomainException
     * @throws ScContent\Exception\DebugException
     * @return null | Zend\Stdlib\Response
     */
    public function process(EventInterface $event)
    {
        $mapper = $this->getMapper();
        $translator = $this->getTranslator();
        $moduleOptions = $this->getModuleOptions();
        $positions = $event->getParam('position');
        if (empty($positions) || ! is_array($positions)) {
            return;
        }
        $oldPositions = $event->getParam('old_position');
        if (empty($oldPositions) || ! is_array($oldPositions)) {
            return;
        }
        $positions = array_diff_assoc($positions, $oldPositions);
        if(empty($positions)) {
            return;
        }
        $theme = $event->getParam('theme');
        if (empty($theme)) {
            throw new DomainException(
                "Missing 'theme' event paramether."
            );
        }

        if (! $moduleOptions->themeExists($theme)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown theme '%s'.",
                $theme
            ));
        }

        $this->setRedirectRouteParams(['theme' => $theme]);
        foreach ($positions as $id => $position) {
            try {
                $mapper->beginTransaction();
                $tid = $mapper->getTransactionIdentifier();
                $mapper->reorder($id, $position, $tid);
                $mapper->commit();
            } catch (UnavailableSourceException $e) {
                $mapper->rollBack();
                $this->setValue($id)->error(self::SourceNotFound);
            } catch (Exception $e) {
                $mapper->rollBack();
                if (DEBUG_MODE) {
                    throw new DebugException(
                        $translator->translate('Error: ') . $e->getMessage(),
                        $e->getCode(),
                        $e
                    );
                }
                $name = $id;
                $meta = $mapper->findMetaById($id);
                if (! empty($meta) && isset($meta['name'])) {
                    $name = $meta['name'];
                    $name = $moduleOptions->getWidgetDisplayName($name);
                }
                $this->setValue($name)->error(self::UnexpectedError);
            }
        }
        return $this->redirect($event);
    }
}
