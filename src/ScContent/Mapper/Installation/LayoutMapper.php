<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Installation;

use ScContent\Mapper\AbstractLayoutMapper,
    ScContent\Entity\WidgetInterface,
    //
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutMapper extends AbstractLayoutMapper
{
    /**
     * Returns the names of the widgets from a predefined list,
     * that are registered in the database.
     *
     * @param string $theme
     * @param string $names
     * @return array
     */
    public function findExistingWidgets($theme, $names)
    {
        if (! is_array($names)) {
            $names = [$names];
        }
        $select = $this->getSql()->select()
            ->from($this->getTable(self::LayoutTableAlias))
            ->columns([
                'name',
            ])
            ->where([
                'theme' => $theme,
                'name'  => $names, // = new In('name', $names)
            ]);

        $result = $this->execute($select);
        return $this->toList($result, 'name');
    }

    /**
     * @param ScContent\Entity\WidgetInterface $entity
     * @return void
     */
    public function install(WidgetInterface $entity)
    {
        $sql = sprintf(
            'INSERT INTO
                `%s`
            (`theme`, `region`, `name`, `display_name`, `description`, `options`, `position`)
            SELECT
            :theme, :region, :name, :displayName, :description, :options,
                IF(ISNULL(MAX(`position`)),
                   1,
                   MAX(`position`) + 1
                )
            FROM
                `%s`
            WHERE
                `theme` =  :theme
            AND
                `region` = :region',
            $this->getTable(self::LayoutTableAlias),
            $this->getTable(self::LayoutTableAlias)
        );
        $this->execute($sql, [
            ':displayName' => $entity->getDisplayName(),
            ':description' => $entity->getDescription(),
            ':options'     => $entity->getOptions(),
            ':theme'       => $entity->getTheme(),
            ':region'      => $entity->getRegion(),
            ':name'        => $entity->getName(),
        ]);
        $entity->setId($this->lastInsertId());
    }
}
