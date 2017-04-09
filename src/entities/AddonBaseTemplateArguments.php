<?php

namespace Nerrad\WPCLI\EE\entities;

use Nerrad\WPCLI\EE\abstracts\BaseTemplateArgumentsAbstract;
use WP_CLI\Utils as cliUtils;
use Nerrad\WPCLI\EE\services\utils\Locations;

/**
 * All the base arguments for the addon
 *
 * @package    Nerrad\WP_CLI\EE
 * @subpackage entities
 * @author     Darren Ethier
 * @since      1.0.0
 */
class AddonBaseTemplateArguments extends BaseTemplateArgumentsAbstract
{
    /**
     * This is what will be used for the title of the addon in the readme.txt and README.md files.
     * Something like "EE Addon Slug"
     *
     * @var string
     */
    private $addon_name = 'EE Addon Slug';


    /**
     * Ends up with something like "eea-addon-slug"
     *
     * @var string
     */
    private $addon_slug = 'eea-addon-slug';


    /**
     * Ends up with something like "eea_addon_slug"
     *
     * @var string
     */
    private $addon_underscore_slug = 'eea_addon_slug';


    /**
     * Represents the addon version in templates.
     *
     * @var string
     */
    private $addon_version = '1.0.0';


    /**
     * Ends up with something like "Addon_Slug"
     *
     * @var string
     */
    private $addon_package = 'Addon_Slug';


    /**
     * This gets compiled later but will end up being the content in the main addon file that represents the array
     * used for the addon registration options.
     *
     * @var string
     */
    private $addon_registration_array = '';


    /**
     * What version of EE the addon requires.  Will default to the current version of EE installed.
     *
     * @var
     */
    private $addon_core_version_required = '4.6.0';


    /**
     * Description for this addon that will be used wherever it is described.
     *
     * @var string
     */
    private $addon_description = 'ADDON DESCRIPTION HERE';


    /**
     * What is used to represent the add-on author.
     *
     * @var string
     */
    private $addon_author = 'YOUR NAME HERE';


    /**
     * Author URL
     *
     * @var string
     */
    private $addon_author_url = 'https://YOUR_SITE_HERE.com';


    /**
     * url for the site.
     *
     * @var string
     */
    private $addon_uri = 'https://ADD-ON_SITE_HERE.com';


    /**
     * Ends up with something like `EE_ADDON_SLUG_CORE_VERSION_REQUIRED
     *
     * @var string
     */
    private $addon_core_version_required_constant = 'EE_ADDON_SLUG_CORE_VERSION_REQUIRED';


    /**
     * End up with something like `EE_ADDON_SLUG_VERSION`
     *
     * @var string
     */
    private $addon_version_constant = 'EE_ADDON_SLUG_VERSION_CONSTANT';


    /**
     * End up with something like `EE_ADDON_SLUG_PLUGIN_FILE`
     *
     * @var string
     */
    private $addon_plugin_file_constant = 'EE_ADDON_SLUG_PLUGIN_FILE';


    /**
     * End up with something like `EE_ADDON_SLUG_BASENAME`
     *
     * @var string
     */
    private $addon_basename_constant = 'EE_ADDON_SLUG_BASENAME';


    /**
     * End up with something like `EE_ADDON_SLUG_PATH`
     *
     * @var string
     */
    private $addon_path_constant = 'EE_ADDON_SLUG_PATH';


    /**
     * End up with something like `EE_ADDON_SLUG_URL`
     *
     * @var string
     */
    private $addon_url_constant = 'EE_ADDON_SLUG_URL';


    /**
     * Indicates the namespace to register for the add-on.
     *
     * @var string
     */
    private $addon_namespace = '';


    /**
     * Takes care of parsing the incoming data and assigning to the correct props with appropriate defaults.
     *
     * @param                                       $data
     */
    protected function assignDataToProps($data)
    {
        array_walk(array_keys(get_object_vars($this)), function ($property) use ($data) {
            if ($property === 'force' || $property === 'addon_string' || ! property_exists($this, $property)) {
                return;
            }
            switch (true) {
                case $property === 'addon_name':
                    $this->{$property} = cliUtils\get_flag_value($data, 'addon_name', $this->addon_string->name());
                    break;
                case $property === 'addon_slug':
                    $this->{$property} = 'eea-' . $this->addon_string->slug();
                    break;
                case $property === 'addon_underscore_slug':
                    $this->{$property} = 'eea_' . str_replace('-', '_', $this->addon_string->slug());
                    break;
                case $property === 'addon_package':
                    $this->{$property} = $this->addon_string->package();
                    break;
                case $property === 'addon_core_version_required':
                    $this->{$property} = cliUtils\get_flag_value($data, 'core_version_required',
                        EVENT_ESPRESSO_VERSION);
                    break;
                case $property === 'addon_core_version_required_constant':
                    $this->{$property} = $this->addon_string->constants()->coreVersionRequired();
                    break;
                case $property === 'addon_version_constant':
                    $this->{$property} = $this->addon_string->constants()->version();
                    break;
                case $property === 'addon_plugin_file_constant':
                    $this->{$property} = $this->addon_string->constants()->pluginFile();
                    break;
                case $property === 'addon_basename_constant':
                    $this->{$property} = $this->addon_string->constants()->baseName();
                    break;
                case $property === 'addon_path_constant':
                    $this->{$property} = $this->addon_string->constants()->path();
                    break;
                case $property === 'addon_url_constant':
                    $this->{$property} = $this->addon_string->constants()->url();
                    break;
                case $property === 'namespace':
                    $this->{$property} = cliUtils\get_flag_value($data, 'namespace', $this->getDefaultNamespace());
                    break;
                default:
                    $this->{$property} = cliUtils\get_flag_value($data, $property, $this->{$property});
            }
        });
    }


    /**
     * Used to return the default namespace that will be used for any place the namespace argument is used.
     *
     * @return string
     */
    private function getDefaultNamespace()
    {
        return 'EventEspresso\\'
               . str_replace(
                   ' ',
                   '',
                   ucwords($this->addon_string->package())
               );
    }


    /**
     * Returns a mapped array of generated file names to file source mustache template.
     * @param string $addon_directory  The base addon directory where files will be installed.
     * @return array
     */
    public function templates($addon_directory)
    {
        $template_path = Locations::templatesPath() . 'base/';
        return array(
            $addon_directory . 'circle.yml' => $template_path . 'circle.yml.mustache',
            $addon_directory . '.gitignore' => $template_path . 'gitignore.mustache',
            $addon_directory . 'info.json'  => $template_path . 'info.json.mustache',
            $addon_directory . 'LICENSE'    => $template_path . 'LICENSE.mustache',
            $addon_directory . $this->getAddonSlug() . '.php'
                                            => $template_path . 'main-file.mustache',
            $addon_directory . 'EE_' . $this->getAddonPackage() . '.class.php'
                                            => $template_path . 'main-class.mustache',
            $addon_directory . 'README.md'  => $template_path . 'README.md.mustache',
        );
    }


    /**
     * Converts all the properties to an array ready for the templates.
     */
    public function toArray()
    {
        $template_arguments = array();
        foreach (array_keys(get_object_vars($this)) as $property) {
            if ($property == 'force' || $property === 'addon_string') {
                continue;
            }
            $template_arguments[$property] = $this->{$property};
        }
        return $template_arguments;
    }

    /**
     * When templates have subdirectories that need created for holding generated templates then this method should
     * return instructions for what needs created. The expected format is an array where each "row"
     * represents the directory created.  Example:
     *
     * array(
     *  '/root/path' => 'admin',
     *  '/root/path/admin' => 'some-other-directory'
     * )
     *
     * So arrays are parsed in order indicating what directories should be checked for first and then created if
     * missing. In this example the file generator receiving this will:
     *
     * 1. Check if `/root/path/` exists. If it does, then it will check if `admin` exists in that path and create it if
     *      it doesn't.
     * 2. Check if `/root/path/admin` exists.  If it doesn't then it will abort.  If it does, then it will check if
     *      'some-other-directory' exists within that path and create it if it doesn't.  And so on.
     *
     * So the order of the elements in the array matters because if any path provided in the key doesn't exist
     * then the directory doesn't get created.
     *
     * @param string $base_directory  This serves as the base for all the paths and should be prepended to the first
     *                                element in each row.
     * @return array
     */
    public function subdirectories($base_directory)
    {
        return array();
    }

    /**
     * @return string
     */
    public function getAddonName()
    {
        return $this->addon_name;
    }

    /**
     * @return string
     */
    public function getAddonSlug()
    {
        return $this->addon_slug;
    }

    /**
     * @return string
     */
    public function getAddonUnderscoreSlug()
    {
        return $this->addon_underscore_slug;
    }

    /**
     * @return string
     */
    public function getAddonVersion()
    {
        return $this->addon_version;
    }

    /**
     * @return string
     */
    public function getAddonPackage()
    {
        return $this->addon_package;
    }

    /**
     * @return string
     */
    public function getAddonRegistrationArray()
    {
        return $this->addon_registration_array;
    }

    /**
     * @return mixed
     */
    public function getAddonCoreVersionRequired()
    {
        return $this->addon_core_version_required;
    }

    /**
     * @return string
     */
    public function getAddonDescription()
    {
        return $this->addon_description;
    }

    /**
     * @return string
     */
    public function getAddonAuthor()
    {
        return $this->addon_author;
    }

    /**
     * @return string
     */
    public function getAddonAuthorUrl()
    {
        return $this->addon_author_url;
    }

    /**
     * @return string
     */
    public function getAddonUri()
    {
        return $this->addon_uri;
    }

    /**
     * @return string
     */
    public function getAddonCoreVersionRequiredConstant()
    {
        return $this->addon_core_version_required_constant;
    }

    /**
     * @return string
     */
    public function getAddonVersionConstant()
    {
        return $this->addon_version_constant;
    }

    /**
     * @return string
     */
    public function getAddonPluginFileConstant()
    {
        return $this->addon_plugin_file_constant;
    }

    /**
     * @return string
     */
    public function getAddonBasenameConstant()
    {
        return $this->addon_basename_constant;
    }

    /**
     * @return string
     */
    public function getAddonPathConstant()
    {
        return $this->addon_path_constant;
    }

    /**
     * @return string
     */
    public function getAddonUrlConstant()
    {
        return $this->addon_url_constant;
    }

    /**
     * @return string
     */
    public function getAddonNamespace()
    {
        return $this->addon_namespace;
    }

    /**
     * @return bool
     */
    public function isForce()
    {
        return $this->force;
    }


}