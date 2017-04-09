<?php

namespace Nerrad\WPCLI\EE\services\components;

use Nerrad\WPCLI\EE\interfaces\ComponentHasScaffoldInterface;
use Nerrad\WPCLI\EE\interfaces\ComponentInterface;
use Nerrad\WPCLI\EE\interfaces\ScaffoldCommandInterface;
use Nerrad\WPCLI\EE\traits\ComponentScaffoldTrait;
use Nerrad\WPCLI\EE\traits\ArgumentParserTrait;
use Nerrad\WPCLI\EE\traits\ScaffoldFiles;
use Nerrad\WPCLI\EE\services\utils\Template;
use WP_CLI\utils as cliUtils;
use WP_CLI;

class Module implements
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
        $module_paths = array();
        array_walk($this->component_strings, function ($component_string) use(&$module_paths) {
            $module_paths[] = "{$this->addon_string->constants()->path()} "
                              . ". 'module/EED_{$component_string->package()}.module.php'";
        });
        return array("'module_paths' => " . Template::formattedArrayString($module_paths, 5));
    }


    /**
     * If this component has any autoloader paths needing registered with the 'autoloader_paths' array in addon
     * registration it should get returned via this method.
     *
     * @return array
     */
    public function autoloaderPaths()
    {
        return array();
    }


    /**
     * The name of the component. eg 'module' or 'message_type'.  Should correspond to the
     * argument key used by the related command.
     *
     * @return string
     */
    public function componentName()
    {
        return 'module';
    }


    /**
     * Generate scaffold for modules.
     *
     * When called individually this command only generates the files related to modules but does not generate
     * items included in main addon code, nor does it generate the main addon scaffold.  It's purpose is for when
     * you want to quickly add the scaffold for additional modules after the fact.
     *
     * ## Options
     *
     * <addon_slug>
     * : The slug of the addon the admin pages are being added to.
     *
     * [--module=<module_slugs>]
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
     *      # Generate Modules for the slugs extra_functions and extra_stuff for my-awesome-addon.
     *      $ wp ee scaffold admin_pages my-awesome-addon --module=extra_functions,extra_stuff
     *      Success: Files created.
     *      /path/to/wp-plugins/eea-my-awesome-addon/module/EED_Extra_Functions.module.php
     *      /path/to/wp-plugins/eea-my-awesome-addon/module/EED_Extra_Stuff.module.php
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
        return 'Generate starter files and code for modules that are part of an Event Espresso Addon';
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
                    'name'        => 'module',
                    'description' => 'A comma-delimited list of module slugs modules you\'d like created.',
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
                    'name'        => 'module',
                    'description' => 'A comma-delimited list of module slugs for modules you\'d like created.',
                    'optional'    => true,
                    'multiple'    => true,
                ),
                array(
                    'type'        => 'assoc',
                    'name'        => 'addon_author',
                    'description' => 'Adds the given name with the @author tag in any phpdocs.',
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