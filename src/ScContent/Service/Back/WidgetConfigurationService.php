<?php

namespace ScContent\Service\Back;

use ScContent\Service\AbstractService,
    ScContent\Mapper\Back\WidgetMapper,
    ScContent\Entity\Back\WidgetEntity,
    ScContent\Mapper\RolesMapper,
    //
    ScContent\Exception\RuntimeException,
    ScContent\Exception\DebugException,
    ScContent\Exception\IoCException,
    //
    ScContent\Mapper\Exception\UnavailableSourceException,
    //
    Exception;


class WidgetConfigurationService extends AbstractService
{
    /**
     * @var ScContent\Mapper\Back\WidgetMapper
     */
    protected $widgetMapper;

    /**
     * @var ScContent\Mapper\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @param ScContent\Mapper\Back\WidgetMapper $mapper
     * @return void
     */
    public function setWidgetMapper(WidgetMapper $mapper)
    {
        $this->widgetMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\WidgetMapper
     */
    public function getWidgetMapper()
    {
        if (! $this->widgetMapper instanceof WidgetMapper) {
            throw new IoCException(
                'The widget mapper was not set.'
            );
        }
        return $this->widgetMapper;
    }

    /**
     * @param ScContent\Mapper\RolesMapper $mapper
     * @return void
     */
    public function setRolesMapper(RolesMapper $mapper)
    {
        $this->rolesMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\RolesMapper
     */
    public function getRolesMapper()
    {
        if (! $this->rolesMapper instanceof RolesMapper) {
            throw new IoCException(
                'The roles mapper was not set.'
            );
        }
        return $this->rolesMapper;
    }

    public function findWidget($id)
    {
        $translator = $this->getTranslator();
        $rolesMapper = $this->getRolesMapper();
        $widgetMapper = $this->getWidgetMapper();

        $availableRoles = $rolesMapper->findRegisteredRoles();
        $widget = new WidgetEntity($availableRoles);
        $widget->setId($id);
        try {
            $widgetMapper->find($widget);
            return $widget;
        } catch (UnavailableSourceException $e) {
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Widget with identifier '%s' was not found."
                ),
                $id
            ));
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                throw new DebugException(
                    $translator->translate('Error: ') . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "An unexpected error occurred during the search widget with id '%s'."
                ),
                $id
            ));
        }
    }

    /**
     * @param WidgetEntity $widget
     */
    public function saveWidget(WidgetEntity $widget)
    {
        $translator = $this->getTranslator();
        $mapper = $this->getWidgetMapper();
        try {
            $mapper->save($widget);
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                throw new DebugException(
                    $translator->translate('Error: ') . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
            throw new RuntimeException($translator->translate(
                'Failed to save widget. An unexpected error occurred.'
            ));
        }
    }
}
