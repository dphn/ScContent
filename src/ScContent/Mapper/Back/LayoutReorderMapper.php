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

use ScContent\Mapper\AbstractLayoutMapper,
    ScContent\Options\ModuleOptions,
    ScContent\Mapper\Exception\UnavailableSourceException,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutReorderMapper extends AbstractLayoutMapper
{
    /**
     * @var \ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * Constructor
     *
     * @param \Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(
        AdapterInterface $adapter,
        ModuleOptions $options
    ) {
        $this->setAdapter($adapter);
        $this->moduleOptions = $options;
    }

    /**
     * @param  integer $id Widget identifier
     * @param  integer $position New widget position
     * @param  string $tid Transaction identifier
     * @throws \ScContent\Mapper\Exception\UnavailableSourceException
     * @return void
     */
    public function reorder($id, $position, $tid)
    {
        $this->checkTransaction($tid);

        $moduleOptions = $this->moduleOptions;

        $position = max(1, $position);
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

        $destination = $this->findMetaByPosition(
            $source['theme'],
            $source['region'],
            $position
        );

        if (empty($destination)) {
            $destination = $this->findMetaByMaxPosition(
                $source['theme'],
                $source['region']
            );
        }

        if ($source['position'] === $destination['position']) {
            return;
        }

        $skew = $destination['position'] < $source['position'] ? 1 : -1;
        $minPosition = min($source['position'], $destination['position']);
        $maxPosition = max($source['position'], $destination['position']);

        $update = $this->getSql()->update()
            ->table($this->getTable(self::LayoutTableAlias))
            ->set([
                'position' => new Expression(
                    'IF(`id` = :sourceId,
                        :newPosition,
                        `position` + :skew
                    )'
                ),
            ])
            ->where([
                'theme     = ?' => $source['theme'],
                'region    = ?' => $source['region'],
                'position <= ?' => $maxPosition,
                'position >= ?' => $minPosition,
            ]);

        $this->execute($update, [
            ':skew' => $skew,
            ':sourceId' => $source['id'],
            ':newPosition' => $destination['position'],
        ]);
    }

    /**
     * @param  string $theme
     * @param  string $region
     * @param  integer $position
     * @return null|array
     */
    protected function findMetaByPosition($theme, $region, $position)
    {
        $select = $this->getSql()->select()
            ->from($this->getTable(self::LayoutTableAlias))
            ->columns([
                'id', 'theme', 'region', 'name', 'position',
            ])
            ->where([
                'theme' => $theme,
                'region' => $region,
                'position' => $position,
            ]);

        return $this->execute($select)->current();
    }

    /**
     * @param string $theme
     * @param string $region
     */
    protected function findMetaByMaxPosition($theme, $region)
    {
        $select = $this->getSql()->select()
            ->from($this->getTable(self::LayoutTableAlias))
            ->columns([
                'id', 'theme', 'region', 'name', 'position',
            ])
            ->where([
                'theme'  => $theme,
                'region' => $region,
            ])
            ->order('position DESC')
            ->limit(1);

        return $this->execute($select)->current();
    }
}
