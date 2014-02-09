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
    ScContent\Mapper\Back\LayoutMoveMapper,
    ScContent\Options\ModuleOptions,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Exception\DebugException,
    ScContent\Exception\IoCException,
    //
    Zend\EventManager\EventInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutMove extends AbstractControllerListener
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var ScContent\Mapper\Back\LayoutMoveMapper
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $redirectRoute = 'sc-admin/layout/index';

    /**#@+
     * @const string
     */
    const SourceNotFound      = 'Source not found';
    const DestinationNotFound = 'Destination not found';
    const UnexpectedError     = 'Unexpected error';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = [
        self::SourceNotFound
            => 'Unable to move the widget with identifier %s in the new region. The widget was not found.',

        self::DestinationNotFound
            => 'Unable to move the widget %s in the new region %s. The region was not found.',

        self::UnexpectedError
            => 'Unable to move the widget %s in the new region.',
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
     * @throws ScContent\Exception\IoCException
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
     * @param ScContent\Mapper\Back\LayoutMoveMapper $mapper
     * @return void
     */
    public function setMapper(LayoutMoveMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\LayoutMoveMapper
     */
    public function getMapper()
    {
        if (! $this->mapper instanceof LayoutMoveMapper) {
            throw new IoCException(
                'The mapper was not set.'
            );
        }
        return $this->mapper;
    }

    /**
     * @param Zend\EventManager\EventInterface $event
     * @return null | Zend\Stdlib\Response
     */
    public function process(EventInterface $event)
    {
        $mapper = $this->getMapper();
        $translator = $this->getTranslator();
        $moduleOptions = $this->getModuleOptions();
        $regions = $event->getParam('region');
        if (empty($regions) || ! is_array($regions)) {
            return;
        }
        $oldRegions = $event->getParam('old_region');
        if (empty($oldRegions) || ! is_array($oldRegions)) {
            return;
        }
        $regions = array_diff_assoc($regions, $oldRegions);
        if (empty($regions)) {
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
        foreach ($regions as $id => $region) {
            try {
                $mapper->beginTransaction();
                $tid = $mapper->getTransactionIdentifier();
                $mapper->move($id, $region, $tid);
                $mapper->commit();
            } catch (UnavailableSourceException $e) {
                $mapper->rollBack();
                $this->setValue($id)->error(self::SourceNotFound);
            } catch (UnavailableDestinationException $e) {
                $mapper->rollBack();
                $name = $id;
                $meta = $mapper->findMetaById($id);
                if (! empty($meta) && isset($meta['name'])) {
                    $name = $meta['name'];
                    $name = $moduleOptions->getWidgetDisplayName($name);
                }
                $this->setValue($name, $region)
                    ->error(self::DestinationNotFound);
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
