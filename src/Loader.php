<?php

namespace Nerrad\WPCLI\EE;

use Nerrad\WPCLI\EE\services\ComponentManager;
use Nerrad\WPCLI\EE\traits\utils\Files;
use ReflectionClass;


/**
 * Loader
 * This is the main class for this package that takes care of loading the component manager and all commands.
 *
 * @package Nerrad\WPCLI\EE
 * @subpackage
 * @author  Darren Ethier
 * @since   1.0.0
 */
class Loader
{
    use Files;

    /**
     * Array of command class names (just the class name not the fully qualified class name) for all commands found in
     * the /commands/ directory
     *
     * @var array
     */
    protected $command_classes = array();


    /**
     * Loader constructor.
     */
    public function __construct()
    {
        //glob this directory for all the files and use that to set the commands.
        $this->setCommandClasses();
    }


    /**
     * Loads any registered command classes and invokes the method for registering the command.  Note this in turn may
     * register any related commands from registered components.
     */
    public function addCommands()
    {
        $component_manager = new ComponentManager();
        foreach ($this->command_classes as $command_class) {
            $class_to_instantiate = 'Nerrad\\WPCLI\\EE\\commands\\' . $command_class;
            if (! in_array(
                '\\Nerrad\\WPCLI\\EE\\interfaces\\CommandInterface',
                class_implements($class_to_instantiate)
            )
            ) {
                /**
                 * @todo, when more command types are created, this will need to be modified to account for them.
                 */
                if ($this->getCommandParent($class_to_instantiate) === 'Nerrad\WPCLI\EE\abstracts\CommandWithComponents') {
                    /** @var \Nerrad\WPCLI\EE\interfaces\BaseCommandInterface $command */
                    $command = new $class_to_instantiate($component_manager);
                } else {
                    /** @var \Nerrad\WPCLI\EE\interfaces\BaseCommandInterface $command */
                    $command = new $class_to_instantiate();
                }
                $command->command();
            }
        }
    }


    /**
     * This is called on construct to detect all the command classes and add to the command array.
     */
    private function setCommandClasses()
    {
        foreach (glob(dirname(__FILE__) . '/commands/*') as $file) {
            $class_name = $this->getClassnameFromFilePath($file);
            //this class isn't a command so don't load.
            if (empty($class_name)) {
                continue;
            }
            $this->command_classes[] = $class_name;
        }
    }


    /**
     * Get the parent of the given class.
     *
     * @param  string $command_class
     * @return string
     */
    private function getCommandParent($command_class)
    {
        $reflection = new ReflectionClass($command_class);
        if ($reflection->getParentClass()) {
            return $reflection->getParentClass()->getName();
        }
        return '';
    }
}