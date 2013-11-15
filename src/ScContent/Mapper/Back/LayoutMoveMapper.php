<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Back;

use ScContent\Mapper\AbstractLayoutMapper,
    ScContent\Options\ModuleOptions,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutMoveMapper extends AbstractLayoutMapper
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @param ScContent\Options\ModuleOptions $options
     */
    public function __construct(
        AdapterInterface $adapter,
        ModuleOptions $options
    ) {
        $this->setAdapter($adapter);
        $this->moduleOptions = $options;
    }

    /**
     * @param integer $id Widget identifier
     * @param string $region Region name
     * @param string $tid Transaction identifier
     * @throws ScContent\Mapper\Exception\UnavailableSourceException
     * @throws ScContent\Mapper\Exception\UnavailableDestinationException
     * @return void
     */
    public function move($id, $region, $tid)
    {
        $this->checkTransaction($tid);

        $moduleOptions = $this->moduleOptions;
        $source = $this->findMetaById($id);

        if (empty($source)) {
            throw new UnavailableSourceException(sprintf(
                "The widget with identifier '%s'was not found.",
                $id
            ));
        }
        if (! $moduleOptions->widgetExists($source['name'])) {
            throw new UnavailableSourceException(sprintf(
                "The widget with identifier '%s'was not found.",
                $id
            ));
        }
        if (! $moduleOptions->themeExists($source['theme'])) {
            throw new UnavailableSourceException(sprintf(
                "The theme of widget with identifier '%s' does not exist.",
                $id
            ));
        }
        if (! $moduleOptions->regionExists(
                $source['theme'],
                $source['region']
        )) {
            throw new UnavailableSourceException(sprintf(
                "The region of widget with identifier '%s' does not exist.",
                $id
            ));
        }
        if (! $moduleOptions->regionExists(
                $source['theme'],
                $region
        )) {
            throw new UnavailableDestinationException(sprintf(
                "The region '%s' does not exist.",
                $region
            ));
        }
        $newPosition = $this->findMaxPosition($source['theme'], $region);
        $update = $this->getSql()->update()
            ->table($this->getTable(self::LayoutTableAlias))
            ->set(array(
                'position' => new Expression(
                    'IF(`id` = :id,
                        :newPosition,
                        `position` - 1
                    )'
                ),
                'region' => new Expression(
                    'IF(`id` = :id,
                        :newRegion,
                        `region`
                    )'
                ),
            ))
            ->where(array(
                'theme     = ?' => $source['theme'],
                'region    = ?' => $source['region'],
                'position >= ?' => $source['position'],
            ));

        $this->execute($update, array(
            ':id' => $id,
            ':newRegion' => $region,
            ':newPosition' => $newPosition,
        ));
    }

    /**
     * @param string $theme
     * @param string $region
     * @return integer
     */
    protected function findMaxPosition($theme, $region)
    {
        $select = $this->getSql()->select()
            ->columns(array('position'))
            ->from($this->getTable(self::LayoutTableAlias))
            ->where(array(
                'region' => $region,
                'theme' => $theme,
            ))
            ->order('position DESC')
            ->limit(1);

        $result = $this->execute($select)->current();
        if (! $result) {
            return 1;
        }
        return $result['position'] + 1;
    }
}
