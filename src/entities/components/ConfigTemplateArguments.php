<?php

namespace Nerrad\WPCLI\EE\entities\components;

use Nerrad\WPCLI\EE\abstracts\ComponentTemplateArgumentsAbstract;
use Nerrad\WPCLI\EE\services\utils\Locations;
use WP_CLI\utils as cliUtils;

class ConfigTemplateArguments extends ComponentTemplateArgumentsAbstract
{

    private $addon_package = 'New_Addon';


    private $addon_name = 'New Addon';


    private $addon_author = 'YOUR NAME HERE';


    /**
     * Converts all the properties to an array ready for the templates.
     */
    public function toArray()
    {
        $template_arguments = array();
        foreach(array_keys(get_object_vars($this)) as $property) {
            if ($this->shouldExcludeProperty($property)) {
                continue;
            }
            $template_arguments[$property] = $this->{$property};
        }
        return $template_arguments;
    }

    /**
     * Returns a mapped array of generated file names to file source mustache template.
     *
     * @param string $addon_directory The base addon directory where files will be installed.
     * @return array
     */
    public function templates($addon_directory)
    {
        $template_path = Locations::templatesPath() . 'config/';
        return array(
            $addon_directory . 'EE_' . $this->addon_base_template_arguments->getAddonPackage() . '_Config.php'
            => $template_path . 'main-config-class.mustache',
        );
    }



    public function assignDataToProps($data)
    {
        array_walk(array_keys(get_object_vars($this)), function ($property) use ($data) {
            if ($this->shouldExcludeProperty($property)) {
                return;
            }
            switch (true) {
                case $property === 'addon_package':
                    $this->{$property} = $this->addon_base_template_arguments->getAddonPackage();
                    break;
                case $property === 'addon_name':
                    $this->{$property} = $this->addon_base_template_arguments->getAddonName();
                    break;
                case $property === 'addon_author':
                    $this->{$property} = $this->addon_base_template_arguments->getAddonAuthor();
                    break;
                default:
                    $this->{$property} = cliUtils\get_flag_value($data, $property, $this->{$property});
            }
        });
        return;
    }


    private function shouldExcludeProperty($property)
    {
        return $property === 'force'
               || $property === 'addon_string'
               || $property === 'component_string'
               || ! property_exists($this, $property);
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
     *      'some-other-directory' exists within that path and create it if it doesn't.  A 2nd so on.
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
        //nothing to do for this template
        return array();
    }
}