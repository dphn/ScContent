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
    ScContent\Mapper\Back\WidgetVisibilityChangeMapper as Mapper,
    //
    ScContent\Exception\DomainException,
    ScContent\Exception\DebugException,
    ScContent\Exception\IoCException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Mapper\Exception\UnavailableSourceException,
    //
    Zend\EventManager\EventInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityChange extends AbstractControllerListener
{
    /**
     * @var ScContent\Mapper\Back\WidgetVisibilityChangeMapper
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $redirectRoute = 'sc-admin/widget/visibility';

    /**
     * @const string
     */
    const UnexpectedError = 'Unexpected error';

    /**
     * @var array
     */
    protected $errorMessages = [
        self::UnexpectedError
            => 'An unexpected error occurred. Failed to change the visibility of the widgets.',
    ];

    /**
     * @param ScContent\Mapper\Back\WidgetVisibilityChangeMapper $mapper
     * @return void
     */
    public function setMapper(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\WidgetVisibilityChangeMapper
     */
    public function getMapper()
    {
        if (! $this->mapper instanceof Mapper) {
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
        $states = $event->getParam('state');
        if (empty($states) || ! is_array($states)) {
            return;
        }
        $oldStates = $event->getParam('old_state');
        if (empty($oldStates) || ! is_array($oldStates)) {
            return;
        }
        $states = array_diff_assoc($states, $oldStates);
        if (empty($states)) {
            return;
        }

        $widgetId = $event->getParam('widget_id');
        if (empty($widgetId)) {
            throw new DomainException(sprintf(
                $translator->translate("Missing event param '%s'."),
                'widget_id'
            ));
        }

        $this->setRedirectRouteParams(['widget_id' => $widgetId]);
        $mapper->beginTransaction();
        $tid = $mapper->getTransactionIdentifier();
        foreach ($states as $contentId => $state) {
            try {
                $mapper->changeState($widgetId, $contentId, $state, $tid);
            } catch (UnavailableSourceException $e) {
                // layout element with id '$widgetId' was not found

                $mapper->rollBack();
                // Widget ID is common for any content
                break;
            } catch (UnavailableDestinationException $e) {
                // content with id '$contentId' was not found
                continue;
            } catch (Exception $e) {
                // unexpected error
                $mapper->rollBack();
                if (DEBUG_MODE) {
                    throw new DebugException(
                        $translator->translate('Error: ') . $e->getMessage(),
                        $e->getCode(),
                        $e
                    );
                }
                break;
            }
        }
        if ($mapper->inTransaction()) {
            $mapper->commit();
        }

        return $this->redirect($event);
    }
}
