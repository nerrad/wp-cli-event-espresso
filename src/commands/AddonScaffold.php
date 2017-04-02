<?php

namespace Nerrad\WPCLI\EE\commands;

use Nerrad\WPCLI\EE\entities\components\ComponentType;
use Nerrad\WPCLI\EE\entities\AddonBaseTemplateArguments;
use Nerrad\WPCLI\EE\services\file_generators\BaseFileGenerator;
use WP_CLI;
use Nerrad\WPCLI\EE\abstracts\CommandWithComponents;
use Nerrad\WPCLI\EE\traits\ArgumentParserTrait;
use Nerrad\WPCLI\EE\traits\ComponentScaffoldTrait;
use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\entities\AddonScaffoldFlag;

/**
 * AddonScaffold
 * Main command for building and generating the an Event Espresso add-on scaffold.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage command
 * @author     Darren Ethier
 * @since      1.0.0
 */
class AddonScaffold extends CommandWithComponents
{
    use ArgumentParserTrait;
    use ComponentScaffoldTrait;

    /**
     * @var array
     */
    private $data;


    /**
     * @var AddonScaffoldFlag;
     */
    private $addon_scaffold_flag;


    /**
     * This initializes all the base elements for the command from the given data.
     *
     * @param string $slug The slug provided when the command was invoked.
     * @param array  $data The data provided when the command was invoked via associative arguments.
     */
    private function initializeBase($slug, $data)
    {
        $this->initialize(
            $data,
            $this->getAddonDetails($slug)
        );
    }


    /**
     * Takes care of initializing all the properties required for this command.
     *
     * @param array                                 $data
     * @param \Nerrad\WPCLI\EE\entities\AddonString $addon_string
     */
    public function initialize(array $data, AddonString $addon_string)
    {
        $this->data                = $data;
        $this->addon_string        = $addon_string;
        $this->addon_scaffold_flag = new AddonScaffoldFlag($data);
        //remove any components that are based on flags
        if ($this->addon_scaffold_flag->isSkipTests()) {
            $this->component_manager->removeComponent('tests');
        }

        if (! $this->addon_scaffold_flag->isIncludeConfig()) {
            $this->component_manager->removeComponent('config');
        }
    }


    /**
     * Invoked by the loader to register the command.
     */
    public function command()
    {
        $command_document = $this->component_manager->composeDocument($this->commandDocumentArgument());
        WP_CLI::add_command('ee scaffold addon', array($this, 'executeCommand'), $command_document);
        //register sub commands for scaffold
        $this->component_manager->registerSubCommandsForType(ComponentType::SCAFFOLD);
    }


    /**
     * Generate starter code for an Event Espresso Addon.
     */
    public function executeCommand($args, array $assoc_args = array())
    {
        $this->initializeBase($args[0], $assoc_args);
        $this->component_manager->initialize($this->data, $this->addon_string, ComponentType::SCAFFOLD);
        //get the registration array element for the template
        $this->data['addon_registration_array'] =
            $this->component_manager->generateAndReturnRegistrationPartsTemplateString();
        $file_generator                         = new BaseFileGenerator(
            $this->addon_string,
            new AddonBaseTemplateArguments(
                $this->addon_string,
                $this->data,
                $this->addon_scaffold_flag->isForce()
            )
        );

        $file_generator->writeFiles();

        $assoc_args['ignore_main_file_warning'] = true;

        //let's run any subcommands for any included component arguments.
        $this->component_manager->runSubCommandsForArguments(
            $assoc_args,
            ComponentType::SCAFFOLD,
            $this->addon_string
        );


        /**
         * @todo, temporarily here to help with recalling what arguments are needed by various components when those
         *      components are built
         */
        //module?
        /*if ($this->data->getModule()) {
            WP_CLI::run_command(array(
                'ee scaffold addon_module',
                $this->addon_string->slug(),
                array(
                    'dir' => $this->addon_directory,
                    'force' => $this->data->isForce(),
                    'ignore_main_file_warning' => true,
                    'slugs' => $this->data->getModule()
                )
            ));
        }

        //shortcode?
        if (cliUtils\get_flag_value($assoc_args, 'shortcode')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_shortcode',
                $this->addon_string->slug(),
                array(
                    'dir' => $this->addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['shortcode']
                )
            ));
        }

        //widget?
        if (cliUtils\get_flag_value($assoc_args, 'widget')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_widget',
                $this->addon_string->slug(),
                array(
                    'dir' => $this->addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['widget']
                )
            ));
        }

        //message type?
        if (cliUtils\get_flag_value($assoc_args, 'message_type')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_message_type',
                $this->addon_string->slug(),
                array(
                    'dir' => $this->addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['message_type'],
                    'with_messengers' => $this->data['message_type_with']
                )
            ));
        }

        //messages shortcode?
        if (cliUtils\get_flag_value($assoc_args, 'messages_shortcode')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_messages_shortcode',
                $this->addon_string->slug(),
                array(
                    'dir' => $this->addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['messages_shortcode']
                )
            ));
        }

        //admin pages?
        if (cliUtils\get_flag_value($assoc_args, 'admin_pages')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_admin_pages',
                $this->addon_string->slug(),
                array(
                    'dir' => $this->addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['admin_pages']
                )
            ));
        }

        //tests?
        if (! cliUtils\get_flag_value($assoc_args, 'skip-tests')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_tests',
                $this->addon_string->slug(),
                array(
                    'dir' => $this->addon_directory,
                    'force' => $force
                )
            ));
        }/**/
    }


    /**
     * Return the entire document argument that is used as the third argument when registering a command.ˆ
     *
     * @return array
     */
    public function commandDocumentArgument()
    {
        return array(
            'shortdesc' => $this->commandShortDescription(),
            'synopsis'  => $this->commandSynopsis(),
        );
    }

    /**
     * A short description for the command.
     *
     * @return string
     */
    function commandShortDescription()
    {
        return 'Generate starter files and code for an Event Espresso Addon.';
    }

    /**
     * Return the synopsis array which is an array of various descriptive properties for the command.
     *
     * @see  wp cli cookbook (link) for example format of the synopsis arguments.
     * @link https://make.wordpress.org/cli/handbook/commands-cookbook/#wp_cliadd_commands-third-args-parameter
     * @return array
     */
    function commandSynopsis()
    {
        return array(
            array(
                'type'        => 'positional',
                'name'        => 'slug',
                'description' => 'The slug used to reference this add-on. Used for generating classnames and other references for the addon.',
                'optional'    => false,
                'multiple'    => false,
            ),
            array(
                'type'        => 'assoc',
                'name'        => 'addon_title',
                'description' => 'What is used to refer to the add-on as its official title.',
                'optional'    => true,
            ),
            array(
                'type'        => 'assoc',
                'name'        => 'addon_description',
                'description' => 'What is used to describe the add-on.',
                'optional'    => true,
            ),
            array(
                'type'        => 'assoc',
                'name'        => 'addon_author',
                'description' => 'The author for the add-on (used in phpdocs, readme.txt, README.md etc).',
                'optional'    => true,
            ),
            array(
                'type'        => 'assoc',
                'name'        => 'addon_author_uri',
                'description' => 'What to link to with the author name.',
                'optional'    => true,
            ),
            array(
                'type'        => 'assoc',
                'name'        => 'addon_uri',
                'description' => 'What to link any reference of the add-on to.',
                'optional'    => true,
            ),
            array(
                'type'        => 'assoc',
                'name'        => 'addon_tested_up_to',
                'description' => 'Use this to indicate the version of WordPress the addon has been tested up to.',
                'optional'    => true,
                'default'     => 'The currently installed version of WordPress',
            ),
            array(
                'type'        => 'flag',
                'name'        => 'skip-tests',
                'description' => 'Use this to indicate no generation of files for tests.',
            ),
            array(
                'type'        => 'flag',
                'name'        => 'force',
                'description' => 'Use this to indicate overwriting any files that already exist.',
            ),
            array(
                'type'        => 'flag',
                'name'        => 'include_config',
                'description' => 'Whether to generate a config file scaffold for the add-on.',
            ),
            array(
                'type'        => 'assoc',
                'name'        => 'namespace',
                'description' => 'Request the registration of a namespace. Will attach the namespace to the plugin directory.',
                'optional'    => true,
                'default'     => 'EventEspresso\AddonSlug',
            ),
        );
    }
}