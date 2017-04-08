<?php

namespace Nerrad\WPCLI\EE\services\components;

use Nerrad\WPCLI\EE\entities\components\ComponentString;
use Nerrad\WPCLI\EE\interfaces\ComponentHasScaffoldInterface;
use Nerrad\WPCLI\EE\interfaces\ComponentInterface;
use Nerrad\WPCLI\EE\interfaces\ScaffoldCommandInterface;
use Nerrad\WPCLI\EE\traits\ComponentScaffoldTrait;
use Nerrad\WPCLI\EE\traits\ArgumentParserTrait;
use Nerrad\WPCLI\EE\traits\ScaffoldFiles;
use WP_CLI\utils as cliUtils;
use WP_CLI;

class AdminPages implements
    ComponentInterface,
    ComponentHasScaffoldInterface,
    ScaffoldCommandInterface
{
    use ComponentScaffoldTrait;
    use ArgumentParserTrait;
    use ScaffoldFiles;

    /**
     * Return an array of parts output to the registration array for this component when the addon is registered.
     *
     * @return array
     */
    public function registrationParts()
    {
        return array(
            "'admin_path' => {$this->addon_string->constants()->path()} . 'admin'",
            "'admin_callback' => ''",
        );
    }


    /**
     * If this component has any autoloader paths needing registered with the 'autoloader_paths' array in addon
     * registration it should get returned via this method.
     *
     * @return array
     */
    public function autoloaderPaths()
    {
        $paths = array();
        array_walk($this->component_strings, function (ComponentString $component_string) use (&$paths) {
            $paths[] = "'{$this->getAdminClassName($component_string)}' => '{$this->getAdminPath($component_string)}'";
            $paths[] = "'{$this->getAdminClassName($component_string, true)}' => '{$this->getAdminPath($component_string,true)}'";
        });
        return $paths;
    }


    /**
     * The name of the component. eg 'module' or 'message_type'.  Should correspond to the
     * argument key used by the related command.
     *
     * @return string
     */
    public function componentName()
    {
        return 'admin_pages';
    }


    /**
     * Returns the class name assembled from known data points.
     *
     * @param ComponentString $component_string
     * @param bool            $init
     * @return string
     */
    private function getAdminClassName(ComponentString $component_string, $init = false)
    {
        $class_name = $component_string->package() . '_Admin_Page';
        return $init ? $class_name . '_Init' : $class_name;
    }


    /**
     * Returns the path to the full file from known data points.
     *
     * @param ComponentString $component_string
     * @param bool            $init
     * @return string
     */
    private function getAdminPath(ComponentString $component_string, $init = false)
    {
        return $this->addon_string->constants()->path()
               . '/admin/'
               . strtolower($component_string->package())
               . '/'
               . $this->getAdminClassName($component_string, $init)
               . '.core.php';
    }

    /**
     * Generate scaffold for admin pages.
     *
     * When called individually this command only generates the files related to admin pages but does not generate
     * items included in main addon code, nor does it generate the main addon scaffold.  It's purpose is for when
     * you want to quickly add the scaffold for additional admin pages after the fact.
     *
     * ## Options
     *
     * <addon_slug>
     * : The slug of the addon the admin pages are being added to.
     *
     * [--admin_pages=<admin_page_slug>]
     * : Comma-delimited list of slugs for each set of admin_page elements you want created.
     *
     * [--addon_author=<name>]
     * : Adds your name with the @author tag in any phpdocs
     *
     * [--force]
     * : Use to indicate overwriting any files that may already exist for the given slugs.
     * default: false
     *
     * ## Examples
     *
     *      # Generate Admin Pages for the slugs system_settings and system_shortcodes for my-awesome-addon.
     *      $ wp ee scaffold admin_pages my-awesome-addon --admin_pages=system_settings,system_shortcodes
     *      Success: Files for addon created.
     *      Success: /path/to/wp-plugins/eea-my-awesome-addon/admin/system_settings/System_Settings_Admin_Page.core.php
     *      Success: /path/to/wp-plugins/eea-my-awesome-addon/admin/system_settings/System_Settings_Admin_Page_Init.core.php
     *      Success: /path/to/wp-plugins/eea-my-awesome-addon/admin/system_shortcodes/System_Shortcodes_Admin_Page.core.php
     *      Success: /path/to/wp-plugins/eea-my-awesome-addon/admin/system_shortcodes/System_Shortcodes_Admin_Page_Init.core.php
     *
     */
    public function scaffoldCommand($args, array $assoc_args = array())
    {
        $addon_details = $this->getAddonDetails($args[0]);
        $this->initializeScaffold(
            $assoc_args,
            $addon_details
        );
        foreach ($this->file_generators as $file_generator) {
            $file_generator->writeFiles();
        }

        if (! cliUtils\get_flag_value($assoc_args, 'ignore_main_file_warning', false)) {
            WP_CLI::log(
                $this->registryArgumentWarning()
            );
        }
    }

    /**
     * A short description for the command.
     *
     * @return string
     */
    function commandShortDescription()
    {
        return 'Generate starter files and code for admin pages that are part of an Event Espresso Addon';
    }

    /**
     * Return the synopsis array which is an array of various descriptive properties for the command.
     *
     * @see  wp cli cookbook (link) for example format of the synopsis arguments.
     * @link https://make.wordpress.org/cli/handbook/commands-cookbook/#wp_cliadd_commands-third-args-parameter
     * @param bool $skip_global  This indicates that any arguments that might come from a global command should be skipped.
     * @return array
     */
    function commandSynopsis($skip_global = true)
    {
        if ($skip_global) {
            return array(
                array(
                    'type'        => 'assoc',
                    'name'        => 'admin_pages',
                    'description' => 'A comma-delimited list of admin_page slugs for pages you\'d like to create',
                    'optional'    => true,
                ),
            );
        } else {
            return array(
                array(
                    'type'        => 'positional',
                    'name'        => 'addon_slug',
                    'description' => 'The slug used to reference this add-on. Used for generating classnames and other references for the addon.',
                    'optional'    => false,
                    'multiple'    => false,
                ),
                array(
                    'type'        => 'assoc',
                    'name'        => 'admin_pages',
                    'description' => 'A comma-delimited list of admin_page slugs for pages you\'d like to create',
                    'optional'    => true,
                    'multiple'    => true,
                ),
                array(
                    'type'        => 'assoc',
                    'name'        => 'addon_author',
                    'description' => 'Adds the given name with the @author tag in any phpdocs',
                    'optional'    => true,
                ),
                array(
                    'type'        => 'flag',
                    'name'        => 'force',
                    'description' => 'Use this to indicate overwriting any files that already exist.',
                    'optional'    => true,
                ),
            );
        }
    }
}