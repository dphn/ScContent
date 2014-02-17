<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\View\Helper;

use ScContent\Service\FileTypesCatalogInterface as CatalogInterface,
    ScContent\View\Helper\FormatProviderInterface,
    ScContent\Service\Dir,
    //
    Zend\I18n\View\Helper\AbstractTranslatorHelper,
    Zend\View\Helper\BasePath;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentFormat extends AbstractTranslatorHelper
{
    /**
     * @var \ScContent\Service\Dir
     */
    protected $dir;

    /**
     * @var \ScContent\Service\FileTypesCatalog
     */
    protected $catalog;

    /**
     * @var \Zend\View\Helper\BasePath
     */
    protected $basePath;

    /**
     * @const string
     */
    const FormatString = '<img src="%s" class="%s" title="%s" alt="%s" />';

    /**
     * @var array
     */
    protected $options = [
        'theme'   => 'sc-default',
        'uploads' => 'uploads',
        'class'   => 'content-list-icon',
    ];

    /**
     * @var array
     */
    protected $icons = [
        'presentation' => 'presentation.png',
        'undefined'    => 'undefined.png',
        'document'     => 'document.png',
        'drawing'      => 'drawing.png',
        'category'     => 'category.png',
        'article'      => 'article.png',
        'archive'      => 'archive.png',
        'image'        => 'image.png',
        'audio'        => 'audio.png',
        'video'        => 'video.png',
        'flash'        => 'flash.png',
    ];

    /**
     * Constructor
     *
     * @param \Zend\View\Helper\BasePath $basePath
     * @param \ScContent\Service\Dir $dir
     * @param \ScContent\Service\FileTypesCatalog $catalog
     * @param array $options
     */
    public function __construct(
        BasePath $basePath,
        Dir $dir,
        CatalogInterface $catalog,
        $options = null
    ) {
        $this->dir = $dir;
        $this->catalog = $catalog;
        $this->basePath = $basePath;
        if (! empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * @param  \Zend\View\Helper\BasePath $basePath
     * @return void
     */
    public function setBasePath(BasePath $basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return \Zend\View\Helper\BasePath
     */
    public function getBasePath()
    {
        if ($this->basePath) {
            return $this->basePath;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->basePath = $this->view->plugin('basepath');
        }

        return $this->basePath;
    }

    /**
     * @param  \ScContent\View\Helper\FormatProviderInterface $provider
     * @return string
     */
    public function getImagePath(FormatProviderInterface $provider)
    {
        if (! $this->isWebImage($provider)) {
            return $this->getIconPath($provider);
        }
        $fileName = $provider->getName();
        list ($extension, $mime) = explode(':', $provider->getSpec());
        $uploads = $this->options['uploads'];
        $file = $fileName . '.' . $extension;
        $uploads = $this->options['uploads'];
        $src = $this->basePath->__invoke($uploads . '/' . $file);
        return $src;
    }

    /**
     * @param  \ScContent\View\Helper\FormatProviderInterface $provider
     * @return string
     */
    public function getIconPath(FormatProviderInterface $provider)
    {
        $type = $provider->getType();
        if ('file' === $type) {
            $spec = $provider->getSpec();
            $feature = $this->catalog->getFeature($spec);
            $type = $this->getFileType($feature);
        }

        $src = null;
        $dir = $this->dir;

        if ($this->isWebImage($provider)) {
            $fileName = $provider->getName();
            list ($extension, $mime) = explode(':', $spec);
            $uploads = $this->options['uploads'];
            $file = $fileName . '.thumbnail.' . $extension;
            if ($this->dir->appUploads($file, true)) {
                $src = $this->basePath->__invoke($uploads . '/' . $file);
            } else {
                $file = $fileName . '.' . $extension;
                $src = $this->basePath->__invoke($uploads . '/' . $file);
            }
        }

        if (is_null($src)) {
            $images = $this->options['theme'] . '/img';
            $icon = $this->icons[$type];
            $src = $this->getBasePath()->__invoke($images . '/' . $icon);
        }
        if (null !== ($translator = $this->getTranslator())) {
            $type = $translator->translate(
                $type, $this->getTranslatorTextDomain()
            );
        }
        $class = $this->options['class'];
        return sprintf(self::FormatString, $src, $class, $type, $type);
    }

    /**
     * @param  \ScContent\View\Helper\FormatProviderInterface $provider
     * @return string
     */
    public function getExtension(FormatProviderInterface $provider)
    {
        $spec = $provider->getSpec();
        if (empty($spec)) {
            return $provider->getType();
        }
        list ($extension, $mime) = explode(':', $spec);
        return $extension;
    }

    /**
     * @param  \ScContent\View\Helper\FormatProviderInterface $provider
     * @return string
     */
    public function getFileName(FormatProviderInterface $provider)
    {
        $spec = $provider->getSpec();
        if (empty($spec)) {
            return $provider->getType();
        }
        list ($extension, $mime) = explode(':', $spec);
        $fileName = $provider->getName() . '.' . $extension;
        return $fileName;
    }

    /**
     * @param  \ScContent\View\Helper\FormatProviderInterface $provider
     * @return boolean
     */
    public function canPreview(FormatProviderInterface $provider)
    {
        if ('file' !== $provider->getType()) {
            return true;
        }
        $spec = $provider->getSpec();
        $feature = $this->catalog->getFeature($spec);
        $needle = CatalogInterface::Image | CatalogInterface::Web;
        return (bool) ($feature & $needle);
    }

    /**
     * @param  \ScContent\View\Helper\FormatProviderInterface $provider
     * @return boolean
     */
    public function isWebImage(FormatProviderInterface $provider)
    {
        if ('file' !== $provider->getType()) {
            return false;
        }
        $spec = $provider->getSpec();
        $feature = $this->catalog->getFeature($spec);
        $needle = CatalogInterface::Image | CatalogInterface::Web;
        return (bool) ($feature & $needle);
    }

    /**
     * @param  integer $feature
     * @return string
     */
    protected function getFileType($feature)
    {
        $type = 'undefined';
        switch (true) {
            case (bool) ($feature & CatalogInterface::Document):
                $type = 'document';
                break;
            case (bool) ($feature & CatalogInterface::Archive):
                $type = 'archive';
                break;
            case (bool) ($feature & CatalogInterface::Image):
                $type = 'image';
                break;
            case (bool) ($feature & CatalogInterface::Audio):
                $type = 'audio';
                break;
            case (bool) ($feature & CatalogInterface::Video):
                $type = 'video';
                break;
            case (bool) ($feature & CatalogInterface::Flash):
                $type = 'flash';
                break;
            case (bool) ($feature & CatalogInterface::Presentation):
                $type = 'presentation';
                break;
            case (bool) ($feature & CatalogInterface::Drawing):
                $type = 'drawing';
                break;
        }
        return $type;
    }
}
