<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Back;

use ScContent\Mapper\AbstractDbMapper,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Mapper\Exception\UnavailableSourceException,
    //
    Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityChangeMapper extends AbstractDbMapper
{
    /**
     * @const string
     */
    const ContentTableAlias = 'contentalias';

    /**
     * @const string
     */
    const LayoutTableAlias = 'layoutalias';

    /**
     * @const string
     */
    const WidgetsTableAlias = 'widgetsalias';

    /**
     * @var array
     */
    protected $_tables = [
        self::WidgetsTableAlias => 'sc_widgets',
        self::LayoutTableAlias  => 'sc_layout',
        self::ContentTableAlias => 'sc_content',
    ];

    /**
     * @param \Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @param  integer $widgetId  Layout element identifier
     * @param  integer $contentId Content identifier
     * @param  integer $state Visibility state <code>0 - disabled, 1 - enabled, -1 - inherit</code>
     * @param  string $tid Transaction identifier
     * @throws \ScContent\Mapper\Exception\UnavailableSourceException
     *              If layout element was not found
     * @throws \ScContent\Mapper\Exception\UnavailableDestinationException
     *              If content was not found
     */
    public function changeState($widgetId, $contentId, $state, $tid)
    {
        $this->checkTransaction($tid);

        $select = $this->getSql()->select()
            ->columns(['id'])
            ->from($this->getTable(self::LayoutTableAlias))
            ->where([
                'id' => $widgetId,
            ]);

        $result = $this->execute($select)->current();
        if (empty($result)) {
            throw new UnavailableSourceException(sprintf(
                "The layout element with identifier '%s' was not found.",
                $widgetId
            ));
        }

        $select = $this->getSql()->select()
            ->columns(['id'])
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                'id' => $contentId,
            ]);

        $result = $this->execute($select)->current();
        if (empty($result)) {
            throw new UnavailableDestinationException(sprintf(
                "The contnent with identifier '%s' was not found.",
                $contentId
            ));
        }

        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::WidgetsTableAlias))
            ->where([
                'content' => $contentId,
                'widget' => $widgetId,
            ]);

        $this->execute($delete);

        $state = (int) $state;
        if ($state < 0) {
            return;
        }

        $insert = $this->getSql()->insert()
            ->into($this->getTable(self::WidgetsTableAlias))
            ->values([
                'widget'  => $widgetId,
                'content' => $contentId,
                'enabled' => (bool) $state,
            ]);

        $this->execute($insert);
    }
}
