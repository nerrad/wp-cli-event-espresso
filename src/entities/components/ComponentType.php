<?php

namespace Nerrad\WPCLI\EE\entities\components;

use InvalidArgumentException;
use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\interfaces\ComponentInterface;
use WP_CLI;

/**
 * ComponentType
 * This entity describes a component type and allows for executing logic on only components belonging to that type.
 * Component types are described on ComponentInterface objects via the various interfaces they implement.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage entities\components
 * @author     Darren Ethier
 * @since      1.0.0
 */
class ComponentType
{
    /**
     * Constant representing scaffold type components
     */
    const SCAFFOLD = 'Scaffold';


    /**
     * Holds the set type.
     *
     * @var string
     */
    private $type = '';


    /**
     * ComponentType constructor.
     *
     * @param $type
     */
    public function __construct($type)
    {
        if (! defined('self::' . strtoupper($type))) {
            throw new InvalidArgumentException(
                sprintf(
                    'The %s class must be instantiated with a valid type. "%s" is not valid.',
                    __CLASS__,
                    $type
                )
            );
        }
        $this->type = $type;
    }


    /**
     * Return the interface name for this component type.
     *
     * @return string
     */
    public function getComponentInterface()
    {
        return 'ComponentHas' . $this->type;
    }


    /**
     * Return the command interface for this component type.
     *
     * @return string
     */
    public function getCommandInterface()
    {
        return $this->type . 'CommandInterface';
    }


    /**
     * Return the trait name for this component type.
     *
     * @return string
     */
    public function getTrait()
    {
        return 'Component' . $this->type . 'Trait';
    }


    /**
     * Used to initialize a component of a given type.
     *
     * @param ComponentInterface $component
     * @param AddonString        $addon_string
     * @param mixed              $data
     */
    public function initialize(
        ComponentInterface $component,
        AddonString $addon_string,
        $data
    ) {
        //don't allow initializing of a component that doesn't match the current type.
        //no errors are thrown, we just don't initialize it.
        if (! $this->isOfComponentType($component)) {
            return;
        }
        $component->{$this->initializeMethod($component)}($data, $addon_string);
    }


    /**
     * Used to register subcommands for a component.
     *
     * @param ComponentInterface $component
     */
    public function registerSubCommand(ComponentInterface $component)
    {
        //if the component isn't for this type or is not a command then no registration.
        if (! $this->shouldRunCommand($component)) {
            return;
        }
        WP_CLI::add_command($this->subCommandTrigger($component), $this->subCommandCallback($component));
    }


    /**
     * Used to execute a subcommand (usually triggered by a BaseCommandInterface class)
     *
     * @param \Nerrad\WPCLI\EE\interfaces\ComponentInterface $component
     * @param \Nerrad\WPCLI\EE\entities\AddonString          $addon_string
     * @param                                                $associative_arguments
     */
    public function runSubCommand(ComponentInterface $component, AddonString $addon_string, $associative_arguments)
    {
        if (! $this->shouldRunCommand($component, $associative_arguments, true)
        ) {
            return;
        }
        WP_CLI::run_command(array(
            $this->subCommandTrigger($component),
            $addon_string->slug(),
        ),
            $associative_arguments
        );
    }


    /**
     * Determine if the given component is of this type.
     *
     * @param ComponentInterface $component
     * @return bool
     */
    public function isOfComponentType(ComponentInterface $component)
    {
        $interface_to_check = $this->getComponentInterface();
        return $component instanceof $interface_to_check;
    }

    public function isOfCommandType(ComponentInterface $component)
    {
        $interface_to_check = $this->getCommandInterface();
        return $component instanceof $interface_to_check;
    }


    /**
     * Does the component have a command that can run?
     *
     * @param ComponentInterface $component
     * @param array              $associative_arguments
     * @param bool               $check_arguments
     * @return bool
     */
    public function shouldRunCommand(
        ComponentInterface $component,
        $associative_arguments = array(),
        $check_arguments = false
    ) {
        $should_run = $this->isOfComponentType($component) && $this->isOfCommandType($component);
        if ($check_arguments) {
            $should_run = isset($associative_arguments[strtolower($component->componentName())])
                ? $should_run
                : false;
        }
        return $should_run;
    }


    /**
     * Return what is the sub command trigger for the given component.
     * Note: this method makes no assumption about whether the component even supports commands.  It's expected that
     * client code is checking that.
     *
     * @param ComponentInterface $component
     * @return string
     */
    public function subCommandTrigger(ComponentInterface $component)
    {
        // something like ee module scaffold
        return 'ee ' . strtolower($component->componentName()) . ' ' . strtolower($this->type);
    }


    /**
     * Returns the subcommand callback for this component type.
     *
     * @param \Nerrad\WPCLI\EE\interfaces\ComponentInterface $component
     * @return array
     */
    public function subCommandCallback(ComponentInterface $component)
    {
        //something like scaffoldCommand
        $method = strtolower($this->type) . 'Command';
        return array($component, $method);
    }


    /**
     * Returns the initialize method for this component type.
     *
     * @param \Nerrad\WPCLI\EE\interfaces\ComponentInterface $component
     * @return string
     */
    private function initializeMethod(ComponentInterface $component)
    {
        //something like initializeScaffold
        return 'initialize' . $this->type;
    }
}