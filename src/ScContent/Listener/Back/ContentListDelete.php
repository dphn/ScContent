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

use ScContent\Mapper\Back\ContentListDeleteMapper,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Exception\DebugException,
    //
    Zend\EventManager\EventInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListDelete extends ContentListAbstractListener
{
    /**#@+
     * @const string
     */
    const DeleteFromSite  = 'Delete from site';
    const SourceNotFound  = 'Source not found';
    const UnexpectedError = 'Unexpected error';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = [
        self::DeleteFromSite
            => 'Permanently delete content that is not in the trash, is impossible.',

        self::SourceNotFound
            => 'Unable to permanently delete the element with identifier %s. The element was not found.',

        self::UnexpectedError
            => 'Failed to permanently delete %s.',
    ];

    /**
     * @param ScContent\Mapper\Back\ContentListDeleteMapper $mapper
     */
    public function setMapper(ContentListDeleteMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * To permanently delete elements from the trash
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
        $ids = $event->getParam('id');
        if (empty($ids)) {
            return;
        }
        $options = $optionsProvider->getOptions($pane);
        if ($options->getRoot() == 'site') {
            $this->error(self::DeleteFromSite);
            return $this->redirect($event);
        }
        foreach ($ids as $id) {
            try {
                $mapper->beginTransaction();
                $tid = $mapper->getTransactionIdentifier();
                $events->trigger(
                    __FUNCTION__ . '.delete.pre',
                    null,
                    [
                        'content' => $id,
                        'tid' => $tid,
                    ]
                );
                $mapper->delete($id, $tid);
                $mapper->commit();
            } catch (UnavailableSourceException $e) {
                $mapper->rollBack();
                $this->setValue($id)->error(self::SourceNotFound);
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
        }

        return $this->redirect($event, 'sc-admin/file/delete');
    }
}
