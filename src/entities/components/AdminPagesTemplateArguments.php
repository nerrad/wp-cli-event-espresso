<?php

namespace Nerrad\WPCLI\EE\entities\components;

use Nerrad\WPCLI\EE\abstracts\TemplateArgumentsAbstract;
use Nerrad\WPCLI\EE\entities\AddonBaseTemplateArguments;
use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\services\utils\Locations;
use WP_CLI\utils as cliUtils;

class AdminPagesTemplateArguments extends TemplateArgumentsAbstract
{

    /**
     * @var string
     */
    private $admin_page_package = 'New_Admin_Page';

    /**
     * @var string
     */
    private $admin_page_package_capitalized = 'NEW_ADMIN_PAGE';

    /**
     * @var string
     */
    private $admin_page_underscore_slug = 'new_admin_page';

    /**
     * @var string
     */
    private $admin_page_name = 'New Admin Page';


    /**
     * @var string
     */
    private $addon_author = 'YOUR NAME';


    /**
     * @var string
     */
    private $addon_version = '1.0.0';


    /**
     * @var string
     */
    private $addon_path_constant = 'ADDON_SLUG_PATH';


    /**
     * @var string
     */
    private $addon_url_constant = 'ADDON_SLUG_URL';


    /**
     * @var ComponentString;
     */
    private $component_string;


    /**
     * @var AddonBaseTemplateArguments;
     */
    private $addon_base_template_arguments;


    /**
     * AdminPagesTemplateArguments constructor.
     *
     * @param \Nerrad\WPCLI\EE\entities\components\ComponentString $component_string
     * @param \Nerrad\WPCLI\EE\entities\AddonBaseTemplateArguments $addon_base_template_arguments
     * @param \Nerrad\WPCLI\EE\entities\AddonString                $addon_string
     * @param                                                      $data
     * @param bool                                                 $force
     */
    public function __construct(
        ComponentString $component_string,
        AddonBaseTemplateArguments $addon_base_template_arguments,
        AddonString $addon_string,
        $data,
        $force = false
    ) {
        $this->addon_string = $addon_string;
        $this->component_string = $component_string;
        $this->addon_base_template_arguments = $addon_base_template_arguments;
        parent::__construct($addon_string, $data, $force);
    }

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
        $template_path = Locations::templatesPath() . 'admin/';
        $addon_directory .= 'admin/' . $this->admin_page_underscore_slug . '/';
        return array(
            $addon_directory . $this->admin_page_package . '_Admin_Page.core.php'
                => $template_path . 'main_admin_page_class.mustache',
            $addon_directory . $this->admin_page_package . '_Admin_Page_Init.core.php'
                => $template_path . 'main_admin_page_init_class.mustache',
            $addon_directory . 'templates/' . $this->admin_page_underscore_slug . '_basic_info_page.template.php'
                => $template_path. 'basic_info_page.mustache'
        );
    }



    public function assignDataToProps($data)
    {
        array_walk(array_keys(get_object_vars($this)), function ($property) use ($data) {
            if ($this->shouldExcludeProperty($property)) {
                return;
            }
            switch (true) {
                case $property === 'admin_page_name':
                    $this->{$property} = $this->component_string->name();
                    break;
                case $property === 'admin_page_underscore_slug':
                    $this->{$property} = strtolower($this->component_string->package());
                    break;
                case $property === 'admin_page_package':
                    $this->{$property} = $this->component_string->package();
                    break;
                case $property === 'addon_author':
                    $this->{$property} = $this->addon_base_template_arguments->getAddonAuthor();
                    break;
                case $property === 'admin_page_package_capitalized':
                    $this->{$property} = strtoupper($this->component_string->package());
                    break;
                case $property === 'addon_version':
                    $this->{$property} = $this->addon_base_template_arguments->getAddonVersion();
                    break;
                case $property === 'addon_path_constant':
                    $this->{$property} = $this->addon_string->constants()->path();
                    break;
                case $property === 'addon_url_constant':
                    $this->{$property} = $this->addon_string->constants()->url();
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
            $base_directory => 'admin',
            $base_directory . 'admin/' => $this->admin_page_underscore_slug,
            $base_directory . 'admin/' . $this->admin_page_underscore_slug . '/' => 'templates'
        );
    }
}