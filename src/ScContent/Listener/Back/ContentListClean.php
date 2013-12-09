<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Back;

use ScContent\Mapper\Back\ContentListCleanMapper,
    ScContent\Exception\DebugException,
    //
    Zend\EventManager\EventInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListClean extends ContentListAbstractListener
{
    /**
     * @const string
     */
    const UnexpectedError = 'Unexpected error';

    /**
     * @var array
     */
    protected $errorMessages = [
        self::UnexpectedError => 'Unable to clean trash.',
    ];

    /**
     * @param ScContent\Mapper\Back\ContentListCleanMapper $mapper
     */
    public function setMapper(ContentListCleanMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Emptying the trash
     *
     * @param Zend\EventManager\EventInterface $event
     * @return null | Zend\Http\Response
     */
    public function process(EventInterface $event)
    {
        $events = $this->getEventManager();
        $mapper = $this->getMapper();
        $optionsProvider = $this->getOptionsProvider();
        $translator = $this->getTranslator();
        $pane = $event->getParam('pane');
        if (! $optionsProvider->hasIdentifier($pane)) {
            return;
        }
        $options = $optionsProvider->getOptions($pane);
        if ($options->getType() == 'search' || $options->getRoot() == 'site') {
            return;
        }
        try {
            $mapper->beginTransaction();
            $tid = $mapper->getTransactionIdentifier();
            $events->trigger(
                __FUNCTION__ . '.clean.pre',
                null,
                [
                    'tid' => $tid,
                ]
            );
            $mapper->clean($tid);
            $mapper->commit();
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                throw new DebugException(
                    $translator->translate('Error: ') . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
            $meta = $mapper->findMetaById($id);
            $name = isset($meta['title']) ? $meta['title'] : $id;
            $this->setValue($name)->error(self::UnexpectedError);
        }

        return $this->redirect($event, 'sc-admin/file/delete');
    }
}
