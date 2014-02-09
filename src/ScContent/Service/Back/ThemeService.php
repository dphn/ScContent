<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Back;

use ScContent\Service\Installation\LayoutService,
    ScContent\Mapper\Back\SettingsMapper,
    ScContent\Exception\IoCException,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ThemeService extends LayoutService
{
    /**
     * @var ScContent\Mapper\Back\SettingsMapper
     */
    protected $settingsMapper;

    /**#@+
     * @const string
     */
    const UnableDisableActiveTheme = 'Unable to disable active theme';
    const DisableNotEnabledTheme   = 'Unable to disable not enabled theme';
    const FailedDisableTheme       = 'Failed to disable theme';
    const NotEnabledThemeAsDefault = 'Unable to set not enabled theme as default';
    const ThemeNotProvidesBackend  = 'Theme not provides backend';
    /**#@-*/

    /**
     * @var string[string] Messages
     *                     <code>(string) message [(string) message identifier]</code>
     */
    protected $errorMessages = [
        self::UnknownThemeNotEnabled
            => 'Unknown theme %s was not enabled. Missing configuration of theme.',

        self::MissingRegionsSpecification
            => 'Failed to enable theme %s. Missing specification of the regions.',

        self::FailedEnableTheme
            => 'An unexpected error occurred. Failed to enable theme %s.',

        self::UnableDisableActiveTheme
            => 'Unable to disable active theme %s.',

        self::DisableNotEnabledTheme
            => 'Unable to disable theme %s. Theme is not enabled.',

        self::FailedDisableTheme
            => 'An unexpected error occurred. Failed to disable theme %s.',

        self::NotEnabledThemeAsDefault
            => 'Unable to set theme %s as default theme. Theme is not enabled.',

        self::ThemeNotProvidesBackend
            => 'Unable to set theme %s as default backend theme. Theme not provides backend feature.',
    ];

    /**
     * @param ScContent\Mapper\Back\SettingsMapper $mapper
     * @return void
     */
    public function setSettingsMapper(SettingsMapper $mapper)
    {
        $this->settingsMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\SettingsMapper
     */
    public function getSettingsMapper()
    {
        if (! $this->settingsMapper instanceof SettingsMapper) {
            throw new IoCException(
                'The settings mapper was not set.'
            );
        }
        return $this->settingsMapper;
    }

    /**
     * @param string $theme Theme name
     * @return boolean
     */
    public function disableTheme($theme)
    {
        $options = $this->getModuleOptions();
        $mapper = $this->getLayoutMapper();
        if ($theme === $options->getFrontendThemeName()
            || $theme === $options->getBackendThemeName()
        ) {
            $this->setValue($theme)->error(self::UnableDisableActiveTheme);
            return false;
        }
        $registeredThemes = $this->getRegisteredThemes();
        if (! in_array($theme, $registeredThemes)) {
            $this->setValue($theme)->error(self::DisableNotEnabledTheme);
            return false;
        }
        try {
            $mapper->beginTransaction();
            $tid = $mapper->getTransactionIdentifier();
            $mapper->uninstall($theme, $tid);
            $mapper->commit();
        } catch (Exception $e) {
            if ($mapper->inTransaction()) {
                $mapper->rollBack();
            }
            $this->setValue($theme)->error(self::FailedDisableTheme);
            return false;
        }
        return true;
    }

    /**
     * @param string $themeName
     * @param string $side
     * @return boolean
     */
    public function setDefault($themeName, $side)
    {
        $translator = $this->getTranslator();
        $moduleOptions = $this->getModuleOptions();
        $registeredThemes = $this->getRegisteredThemes();
        if (! in_array($themeName, $registeredThemes)) {
            $this->setValue($themeName)->error(self::NotEnabledThemeAsDefault);
            return false;
        }
        if ('backend' !== $side) {
            $side = 'frontend';
        }
        if ('backend' === $side) {
            $theme = $moduleOptions->getThemeByName($themeName);
            if (! isset($theme['provides_backend'])
                || ! $theme['provides_backend']
            ) {
                $this->setValue($themeName)
                    ->error(self::ThemeNotProvidesBackend);

                return false;
            }
        }
        $mapper = $this->getSettingsMapper();
        $config = $mapper->getConfig();
        if (! isset($config['sc'])) {
            $config->sc = [];
        }
        $key = $side . '_theme_name';
        $config->sc->$key = $themeName;
        $mapper->saveConfig($config);
        return true;
    }
}
