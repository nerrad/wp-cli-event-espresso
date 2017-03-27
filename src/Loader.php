<?php

namespace Nerrad\WPCLI\EE\commands;
use Nerrad\WPCLI\EE\utils\Files;

class Loader
{
    protected $command_classes = array();


    public function __construct()
    {
        //glob this directory for all the files and use that to set the commands.
        $this->setCommandClasses();
    }


    public function addCommands()
    {
        foreach ($this->command_classes as $command_class) {
            $fqcn = 'Nerrad\\WPCLI\\EE\\commands\\' . $command_class;
            /** @var \Nerrad\WPCLI\EE\interfaces\CommandInterface $command */
            $command = new $fqcn;
            $command->addCommand();
        }
    }


    private function setCommandClasses()
    {
        foreach (glob(dirname(__FILE__) . '/*') as $file) {
            $class_name = Files::getClassnameFromFilePath($file);
            //this class isn't a command so don't load.
            if ($class_name == 'Loader' || empty($class_name)) {
                continue;
            }
            $this->command_classes[] = $class_name;
        }
    }


    public function __invoke() {
        /** @todo temporary while developing */
        return;
    }
}