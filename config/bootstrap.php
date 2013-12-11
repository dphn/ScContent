<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
use Zend\Mvc\MvcEvent;

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
$app = $event->getApplication();
$serviceLocator = $app->getServiceManager();
$eventManager   = $app->getEventManager();

$eventManager->attachAggregate(
    $serviceLocator->get('ScListener.Installation.Inspector')
);

$eventManager->attachAggregate(new \ScContent\Listener\Theme\ThemeContext);

/* ScContent installation.
 *
 * Using installation feature for installation of module ScContent.
 */
$file = $this->getDir() . DS . 'settings' . DS . 'installation.locked';
if (! file_exists($file)) {
    $installation = $serviceLocator->get('ScOptions.InstallationOptions')
        ->getInstallation();

    $serviceLocator->get('ScListener.Installation.Inspector')->setup($installation);
}

/* After the user logs in, sets the locale and time zone according to the
 * user specified data from database
 */
$zfcServiceEvents = $serviceLocator->get(
        'ZfcUser\Authentication\Adapter\AdapterChain'
    )
    ->getEventManager();

$l10n = $serviceLocator->get('ScService.Localization');

$zfcServiceEvents->attach('authenticate', function ($event) use($l10n, $serviceLocator) {
    $userId = $event->getIdentity();
    if ($userId) {
        $mapper = $serviceLocator->get('zfcuser_user_mapper');
        $user = $mapper->findById($userId);
        $l10n->save($user);
    }
});
