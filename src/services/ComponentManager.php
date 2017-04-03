<?php

namespace Nerrad\WPCLI\EE\services;

use Nerrad\WPCLI\EE\entities\AddonScaffoldFlag;
use Nerrad\WPCLI\EE\interfaces\CommandInterface;
use Nerrad\WPCLI\EE\interfaces\ComponentHasScaffoldInterface;
use Nerrad\WPCLI\EE\interfaces\InitializeBeforeCommandInterface;
use WP_CLI;
use Nerrad\WPCLI\EE\traits\utils\Files;
use Nerrad\WPCLI\EE\interfaces\ComponentInterface;
use Exception;
use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\entities\components\ComponentType;
use InvalidArgumentException;


/**
 * This is the manager for all components in the system.  Commands use this to discover components related to the command.
 * This means:
 * - sometimes components may have subcommands that are registered with the command
 * - sometimes components may have provide behaviour exposed as arguments on the main command.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage services
 * @author     Darren Ethier
 * @since      1.0.0
 */
class ComponentManager
{

    use Files;

    /**
     * Found Components
     *
     * @var ComponentInterface[]
     */
    private $components = array();


    /**
     * @var AddonString
     */
    private $addon_string;


    /**
     * Holds the incoming data from the command
     *
     * @var array
     */
    private $data = array();


    /**
     * Used to keep track of whether the initialize method has been called for a given component type.
     * @var bool
     */
    private $initialized = array();


    /**
     * Holds the array of all the various registration parts used to generate the addon registration template string.
     *
     * @var array
     */
    private $registration_parts = array();

    public function __construct()
    {
        $this->loadComponents();
    }


    /**
     * This takes care of looping through our components and retrieving from them any additional elements to add to the
     * main command document argument.
     *
     * @param array $base_command_document
     */
    public function composeDocument(array $base_command_document)
    {
        array_walk($this->components, function ($component) use (&$base_command_document) {
            if ($component instanceof CommandInterface) {
                $component_synopsis = $component->commandSynopsis();
                if ($component_synopsis) {
                    foreach($component_synopsis as $items) {
                       $base_command_document['synopsis'][] = $items;
                    }
                }
            }
        });
        //there's a weird issue with WPCLI where it appears any flag type synopsis items MUST be at the end of the
        //synopsis array.  So let's rearrange things so that happens.
        usort($base_command_document['synopsis'], function ($itema, $itemb) {
            $left_type = isset($itema['type']) ? $itema['type'] : '';
            $right_type = isset($itemb['type']) ? $itemb['type'] : '';
            if ($left_type === 'positional' && $right_type === 'positional') {
                return 0;
            } elseif ($left_type === 'positional' && $right_type !== 'positional') {
                return -1;
            } elseif ($left_type === 'assoc' && $right_type ==='assoc'){
                return 0;
            } elseif ($left_type === 'assoc' && $right_type !== 'positional') {
                return -1;
            } elseif ($left_type === 'assoc' && $right_type === 'positional') {
                return 1;
            } else {
                return 1;
            }
        });
        return $base_command_document;
    }


    /**
     * This initializes all our components for processing commands.  Typically called by a parent command that
     * is calling components as subcommands.
     *
     * @param array                   $data
     * @param AddonString             $addon_string
     */
    public function initialize($data, AddonString $addon_string, $component_type = ComponentType::SCAFFOLD)
    {
        //check if the components have already been initialized for this component type.
        if (! empty($this->initialized[$component_type])) {
            return;
        }
        $this->data         = $data;
        $this->addon_string = $addon_string;
        if ($component_type === ComponentType::SCAFFOLD) {
            $this->buildBaseParts();
        }
        $this->initializeComponents($component_type);
        //register that this component has been initialized so we don't unnecessarily initialize again.
        $this->initialized[$component_type] = true;
    }


    /**
     * @return ComponentInterface[]
     */
    public function components()
    {
        return $this->components;
    }


    /**
     * Retrieves as specific component that has been discovered and registered.
     * @param string $component_name
     * @return ComponentInterface|null
     */
    public function component($component_name)
    {
        return isset($this->components[$component_name])
            ? $this->components[$component_name]
            : null;
    }


    /**
     * Takes care of registering subCommands from loaded components for the given component type.
     * The component should also implement the CommandInterface
     *
     * @param string $component_type Should correspond to one of the component types. eg. ComponentType::SCAFFOLD
     */
    public function registerSubCommandsForType($component_type)
    {
        $component_type = new ComponentType($component_type);
        array_walk($this->components,
            function (ComponentInterface $component) use ($component_type) {
                $component_type->registerSubCommand($component);
            }
        );
    }


    /**
     * Used to run subcommands for any loaded components where the incoming $associative arguments has the component
     * represented in it and the component is of the given component type.  The component should also implement the
     * Command Interface.
     * Note, this also executes the initialize method and runs initialize on all components that implement
     * InitializeBeforeCommand (but that will only happen if this type hasn't been initialized yet).
     *
     * @param array       $data
     * @param string      $component_type
     * @param AddonString $addon_string
     */
    public function runSubCommandsForArguments($data, $component_type, AddonString $addon_string)
    {
        $this->initialize($data, $addon_string, $component_type);
        $component_type = new ComponentType($component_type);
        array_walk($this->components,
            function (ComponentInterface $component) use ($component_type, $data) {
                $component_type->runSubCommand($component, $this->addon_string, $data);
            }
        );
    }


    /**
     * Initializes components for the given type.
     *
     * @param $component_type
     */
    private function initializeComponents($component_type = ComponentType::SCAFFOLD)
    {
        $component_type = new ComponentType($component_type);
        array_walk($this->components,
            function (ComponentInterface $component) use ($component_type) {
                if ($component instanceof InitializeBeforeCommandInterface) {
                    $component_type->initialize($component, $this->addon_string, $this->data);
                }
            }
        );
    }


    /**
     * Construct the base components for the data.
     * Used when initializing ComponentType::SCAFFOLD components.
     */
    private function buildBaseParts()
    {
        $this->registration_parts = array(
            "'version' => {$this->addon_string->constants()->version()}",
            "'plugin_slug' => 'eea-' . {$this->addon_string->slug()}'",
            "'min_core_version' => {$this->addon_string->constants()->coreVersionRequired()}",
            "'main_file_path' => {$this->addon_string->constants()->pluginFile()}",
        );
    }


    /**
     * This is called on construct to discover any components in the /src/components directory and load them.
     */
    private function loadComponents()
    {
        foreach (glob(dirname(__FILE__) . '/components/*') as $file) {
            try {
                $fqcn      = 'Nerrad\\WPCLI\\EE\\services\\components\\' . $this->getClassnameFromFilePath($file);
                $component = new $fqcn;
                //if component is an instance of ComponentInterface then we don't set it on the component array.
                if ($component instanceof ComponentInterface) {
                    $this->components[$component->componentName()] = $component;
                }
            } catch (Exception $e) {
                WP_CLI::error($e->getMessage());
            }
        }
    }



    /**
     * Used to remove a component from the components array - usually when a component is controlled via a flag setting
     * and the main command wants to remove that component from being used.
     * @param string $component_name
     * @throws InvalidArgumentException
     */
    public function removeComponent($component_name)
    {
        unset($this->components[$component_name]);
    }


    /**
     * Called by the AddonScaffold command to generated and return the registration arguments for the add-on's main class
     * scaffold.  These are the registration options that are used with `EE_Addon::register` method call.
     *
     * Note: This builds the template string for the code for all initialized components thus this should ONLY be called
     * after the components have been initialized.  This is because the data won't be known until executing the main
     * command.
     *
     * @return string
     */
    public function generateAndReturnRegistrationPartsTemplateString()
    {
        $autoloader_part = $this->generateAutoloaderPart();
        if ($autoloader_part) {
            $this->registration_parts[] = $autoloader_part;
        }

        //get all the component parts!
        array_walk($this->components, function (ComponentInterface $component) {
            if (! $component instanceof ComponentHasScaffoldInterface) {
                return;
            }
            if ($component->registrationParts()) {
                $this->registration_parts[] = $component->registrationParts();
            }
        });

        //k let's return the parts as the string for the template.
        return $this->formattedString($this->registration_parts);
    }


    /**
     * This unsets from the components any that should not be implemented because they are not requested.
     * @param \Nerrad\WPCLI\EE\entities\AddonScaffoldFlag $scaffold_flag
     */
    public function removeComponentsNotRequested(AddonScaffoldFlag $scaffold_flag)
    {
        //remove any components that are based on flags
        if ($scaffold_flag->isSkipTests()) {
            $this->removeComponent('tests');
        }

        if (! $scaffold_flag->isIncludeConfig()) {
            $this->removeComponent('config');
        }

        //now loop through components and see if the data has the argument for the component and its not empty
        foreach ($this->components as $component) {
            if (empty($this->data[$component->componentName()])) {
                unset($this->components[$component->componentName()]);
            }
        }
    }


    /**
     * Called by generateAndReturnRegistrationPartsTemplateString method to retrieve the autoloader section for the
     * registration options.
     *
     * @return string
     */
    private function generateAutoloaderPart()
    {
        $autoloader_paths = array();
        foreach ($this->components as $component) {
            if (! $component instanceof ComponentHasScaffoldInterface) {
                continue;
            }
            $paths = (array)$component->autoloaderPaths();
            array_push($autoloader_paths, $paths);
        }
        if ($autoloader_paths) {
            return "'autoloader_paths' => " . $this->formattedString($autoloader_paths);
        }
        return '';
    }


    /**
     * Just returns a nice formatted string for the parts in the format `array( part1, part2, ...)`
     *
     * @param $parts
     * @return string
     */
    private function formattedString($parts)
    {
        return 'array('
               . PHP_EOL
               . "\t"
               . implode(',' . PHP_EOL . "\t", $parts)
               . PHP_EOL
               . ')';
    }

}