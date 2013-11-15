<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
use ScContent\Controller\AbstractBack,
    ScContent\Controller\AbstractFront,
    ScContent\Controller\AbstractInstallation,
    ScContent\Controller\Installation\InstallationController,
    //
    Zend\Mvc\MvcEvent;

/**
 * Instead, each time you need to check the parameters of the
 * environment, use the constant.
 *
 * @const string
 */
defined('DEBUG_MODE')
     || define(
            'DEBUG_MODE',
            strtolower(getenv('APPLICATION_ENV')) === 'development' ? true : false
        );

/**
 * Used as the name for events that report an error.
 *
 * @const string
 */
defined('ERROR')
    || define('ERROR', 'ERROR');

/**
 * Used when working with the file system.
 *
 * @const string
 */
defined('CHMOD_FILE')
    || define('CHMOD_FILE', 0644);

/**
 * Used when working with the file system.
 *
 * @const string
 */
defined('CHMOD_DIR')
    || define('CHMOD_DIR', 0755);

/**
 * Default multibite string function encoding.
 *
 * @const string
 */
defined('MB_INTERNAL_ENCODING')
    || define('MB_INTERNAL_ENCODING', 'UTF-8');

mb_internal_encoding(MB_INTERNAL_ENCODING);

/* Activates the inspector of installation
 *
 * If the 'installation.locked' file was not found, the
 * installation starts.
 *
 * The target - Application.
 * Application instance, currently exists.
 */
$app = $e->getApplication();
$sm = $app->getServiceManager();

$file = SCCONTENT_BASE_DIR
    . DS . 'data'
    . DS . 'settings'
    . DS . 'installation.locked';

if (! file_exists($file)) {
    $app->getEventManager()->attach(
        MvcEvent::EVENT_DISPATCH,
        array(
            $sm->get('sc-service.installation.inspector'),
            'inspect',
        ),
        PHP_INT_MAX
    );
}

/* Activates listeners of themes
 *
 * The target - Controller.
 * Controller instance, currently does not exist.
 */
$sharedEvents = $app->getEventManager()->getSharedManager();

$sharedEvents->attach('*', 'dispatch', function($e) use($sm) {
    $target = $e->getTarget();
    switch (true) {
        case $target instanceof AbstractBack:
            $theme = $sm->get('sc-listener.theme.backend');
            $theme->update($e);
            break;
        case $target instanceof AbstractFront:
            $theme = $sm->get('sc-listener.theme.frontend');
            $theme->update($e);
            break;
        case $target instanceof AbstractInstallation:
            $theme = $sm->get('sc-listener.theme.installation');
            $theme->update($e);
            break;
    }
}, - PHP_INT_MAX);

/* After the user logs in, sets the locale and time zone according to the
 * user specified data from database
 */
$l10n = $sm->get('sc-service.l10n');

$zfcServiceEvents = $sm->get('ZfcUser\Authentication\Adapter\AdapterChain')->getEventManager();
$zfcServiceEvents->attach('authenticate', function ($e) use($l10n, $sm) {
    $userId = $e->getIdentity();
    if ($userId) {
        $mapper = $sm->get('zfcuser_user_mapper');
        $user = $mapper->findById($userId);
        $l10n->save($user);
    }
});
