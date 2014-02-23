<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Theme;

use ScContent\Options\Installation\Installation,
    ScContent\Controller\AbstractInstallation,
    ScContent\Exception\InvalidArgumentException,
    //
    Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationStrategy extends AbstractThemeStrategy
{
    /**
     * @param  \Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function update(MvcEvent $event)
    {
        $model = $event->getResult();
        if (! $model instanceof ViewModel) {
            return;
        }

        $installation = $event->getParam('installation');
        if (is_null($installation)) {
            throw new DomainException(
                "Missing event parameter 'installation'."
            );
        }
        if (! $installation instanceof Installation) {
            throw new DomainException(
                "The event parameter 'installation' must be a instance of 'ScContent\Options\Installation\Installation'."
            );
        }

        $step       = $installation->getCurrentStep();
        $controller = $event->getTarget();

        if (! $controller instanceof AbstractInstallation) {
            throw new InvalidArgumentException(sprintf(
                "The operation is not applicable to the type of target '%s'.",
                get_class($controller)
            ));
        }

        $layoutModel = $event->getViewModel();
        if (! $model->terminate()) {
            if ('layout/layout' == $layoutModel->getTemplate()) {
                $layout = $step->getLayout();
                if (empty($layout)) {
                    $layout = $installation->getLayout();
                }
                $layoutModel->setTemplate($layout);
            }
            if (! isset($layoutModel->title)) {
                $layoutModel->title = $installation->getTitle();
            }
        }

        if (! $model->getTemplate()) {
            $template = $step->getTemplate();
            if (empty($template)) {
                $template = $installation->getTemplate();
            }
            $model->setTemplate($template);
        }

        if (! isset($model->brand)) {
            $model->brand = $installation->getBrand();
        }
        if (! isset($model->step)) {
            $model->step = $step->getName();
        }
        if (! isset($model->header)) {
            $model->header = $step->getHeader();
        }
        if (! isset($model->title)) {
            $model->title = $step->getTitle();
        }
        if (! isset($model->header)) {
            $model->header = $step->getHeader();
        }
        if (! isset($model->info)) {
            $model->info = $step->getInfo();
        }
    }
}
