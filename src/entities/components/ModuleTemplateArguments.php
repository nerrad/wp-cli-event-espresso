<?php

namespace Nerrad\WPCLI\EE\entities\components;

use Nerrad\WPCLI\EE\abstracts\ComponentTemplateArgumentsAbstract;
use Nerrad\WPCLI\EE\services\utils\Locations;
use WP_CLI\utils as cliUtils;

class ModuleTemplateArguments extends ComponentTemplateArgumentsAbstract
{

    /**
     * @var string
     */
    private $module_package = 'New_Addon';

    /**
     * @var string
     */
    private $module_underscore_slug = 'new_admin_page';

    /**
     * @var string
     */
    private $addon_author = 'YOUR NAME';


    private $addon_package_camelized = 'NewAddon';

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
        $template_path = Locations::templatesPath() . 'module/';
        $addon_directory .= 'module/';
        return array(
            $addon_directory . 'EED_' . $this->module_package . '.module.php'
            => $template_path . 'main-module-file.mustache',
        );
    }



    public function assignDataToProps($data)
    {
        array_walk(array_keys(get_object_vars($this)), function ($property) use ($data) {
            if ($this->shouldExcludeProperty($property)) {
                return;
            }
            switch (true) {
                case $property === 'module_underscore_slug':
                    $this->{$property} = strtolower($this->component_string->package());
                    break;
                case $property === 'module_package':
                    $this->{$property} = $this->component_string->package();
                    break;
                case $property === 'addon_author':
                    $this->{$property} = $this->addon_base_template_arguments->getAddonAuthor();
                    break;
                case $property === 'addon_package_camelized':
                    $this->{$property} = str_replace(' ', '', $this->addon_base_template_arguments->getAddonPackage());
                    break;
                default:
                    $this->{$property} = cliUtils\get_flag_value($data, $property, $this->{$property});
            }
        });
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
        return array(
            $base_directory => 'module',
        );
    }
}