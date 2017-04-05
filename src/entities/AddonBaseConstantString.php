<?php

namespace Nerrad\WPCLI\EE\entities;

/**
 * AddonBaseConstantString
 * Used by the scaffold command (and implemented in AddonString) to represent all the BASE addon constant strings that
 * get included in the scaffold files.  Scaffold components require this in some cases to build their own strings as
 * well.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage entities
 * @author     Darren Ethier
 * @since      1.0.0
 */
class AddonBaseConstantString
{
    /**
     * SOME_ADDON_CORE_VERSION_REQUIRED
     *
     * @var string
     */
    private $core_version_required;

    /**
     * EE_SOME_ADDON_VERSION
     *
     * @var string
     */
    private $version;


    /**
     * EE_SOME_ADDON_PLUGIN_FILE
     *
     * @var string
     */
    private $plugin_file;


    /**
     * EE_SOME_ADDON_BASENAME
     *
     * @var string
     */
    private $basename;


    /**
     * EE_SOME_ADDON_PATH
     *
     * @var string
     */
    private $path;


    /**
     * EE_SOME_ADDON_URL
     *
     * @var string
     */
    private $url;


    /**
     * AddonBaseConstantString constructor.
     *
     * @param \Nerrad\WPCLI\EE\entities\AddonString $addon_string
     */
    public function __construct(AddonString $addon_string)
    {
        $this->build($addon_string);
    }


    /**
     * Takes care of building the constant strings.
     *
     * @param \Nerrad\WPCLI\EE\entities\AddonString $addon_string
     */
    private function build(AddonString $addon_string)
    {
        $package_with_prefix = 'EE_' . strtoupper($addon_string->package()) . '_';
        $constants_map       = array(
            'core_version_required' => $package_with_prefix . 'CORE_VERSION_REQUIRED',
            'version'               => $package_with_prefix . 'VERSION',
            'plugin_file'           => $package_with_prefix . 'PLUGIN_FILE',
            'basename'              => $package_with_prefix . 'BASENAME',
            'path'                  => $package_with_prefix . 'PATH',
            'url'                   => $package_with_prefix . 'URL',
        );
        foreach ($constants_map as $property => $constant_string) {
            if (property_exists($this, $property)) {
                $this->{$property} = $constant_string;
            }
        }
    }


    /**
     * Returns the core version required constant string.
     *
     * @return string
     */
    public function coreVersionRequired()
    {
        return $this->core_version_required;
    }


    /**
     * Returns the version constant string
     *
     * @return string
     */
    public function version()
    {
        return $this->version;
    }


    /**
     * Returns the plugin file constant string
     *
     * @return string
     */
    public function pluginFile()
    {
        return $this->plugin_file;
    }


    /**
     * Returns the base name constant string
     *
     * @return string
     */
    public function baseName()
    {
        return $this->basename;
    }


    /**
     * Returns the path constant string.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }


    /**
     * Returns the url constant string.
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }
}