<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Migration;

use ScContent\Exception\MigrationException,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Schema extends AbstractMigrationSchema
{
    /**
     * Migrate.
     *
     * @throw ScContent\Exception\MigrationException
     * @return void
     */
    public function up()
    {
        $this->down();

        try {
            $this->buildMapper(
               'ScContent.Migration.MapperBase.Content'
            )
            ->up();

            $this->buildMapper(
                'ScContent.Migration.MapperBase.Search'
            )
            ->up();

            $this->buildMapper(
                'ScContent.Migration.MapperBase.Garbage'
            )
            ->up();

            $this->buildMapper(
                'ScContent.Migration.MapperBase.Layout'
            )
            ->up();

            $this->buildMapper(
                'ScContent.Migration.MapperBase.Widgets'
            )
            ->up();

            $this->buildMapper(
                'ScContent.Migration.MapperBase.Users'
            )
            ->up();

            $this->buildMapper(
                'ScContent.Migration.MapperBase.Roles'
            )
            ->up();

            $this->buildMapper(
                'ScContent.Migration.MapperBase.RolesLinker'
            )
            ->up();

        } catch (Exception $e) {
            $this->down();
            throw new MigrationException(
               'Migration Failed: ' . $e->getMessage(),
               $e->getCode(),
               $e
            );
        }
    }

    /**
     * Remove migration from the database.
     *
     * @return void
     */
    public function down()
    {
        try {
            $this->buildMapper(
               'ScContent.Migration.MapperBase.Content'
            )
            ->down();
        } catch (Exception $e) {
            //
        }

        try {
            $this->buildMapper(
                'ScContent.Migration.MapperBase.Search'
            )
            ->down();
        } catch (Exception $e) {
            //
        }

        try {
            $this->buildMapper(
                'ScContent.Migration.MapperBase.Garbage'
            )
            ->down();
        } catch (Exception $e) {
            //
        }

        try {
            $this->buildMapper(
                'ScContent.Migration.MapperBase.Layout'
            )
            ->down();
        } catch (Exception $e) {
            //
        }

        try {
            $this->buildMapper(
                'ScContent.Migration.MapperBase.Widgets'
            )
            ->down();
        } catch (Exception $e) {
            //
        }

        try {
            $this->buildMapper(
                'ScContent.Migration.MapperBase.Users'
            )
            ->down();
        } catch (Exception $e) {
            //
        }

        try {
            $this->buildMapper(
                'ScContent.Migration.MapperBase.Roles'
            )
            ->down();
        } catch (Exception $e) {
            //
        }

        try {
            $this->buildMapper(
                'ScContent.Migration.MapperBase.RolesLinker'
            )
            ->down();
        } catch (Exception $e) {
            //
        }
    }
}
