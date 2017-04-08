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

class Config implements
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
            "'config_class' => 'EE_{$this->addon_string->package()}_Config'",
            "'config_name' => 'EE_{$this->addon_string->package()}'",
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
        return array(
            "'EE_{$this->addon_string->package()}_Config' => {$this->addon_string->constants()->path()} . 'EE_{$this->addon_string->package()}_Config'"
        );
    }


    /**
     * The name of the component. eg 'module' or 'message_type'.  Should correspond to the
     * argument key used by the related command.
     *
     * @return string
     */
    public function componentName()
    {
        return 'config';
    }


    /**
     * Generate scaffold for config.
     * When called individually this command only generates the file related to a config class for the add-on but does
     * not generate items included in main addon code, nor does it generate the main addon scaffold.  It's purpose is
     * for when you want to quickly add the scaffold for the config class after the fact.
     *
     * ## Options
     *
     * <addon_slug>
     * : The slug of the addon the config is being generated for.
     *
     * [--addon_author=<name>]
     * : Adds your name with the @author tag in any phpdocs
     *
     * [--force]
     * : Use to indicate overwriting any files that may already exist for the given slugs.
     * default: false
     *
     * ## Examples
     *      # Generate config for the eea-my-awesome-addon.
     *      $ wp ee scaffold config my-awesome-addon
     *      Success: Files created.
     *      Success: /path/to/wp-plugins/eea-my-awesome-addon/EE_My_Awesome_Addon_Config.php
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
        return 'Generate starter files and code for the config that is part of an Event Espresso Addon';
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