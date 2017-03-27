<?php

namespace Nerrad\WPCLI\EE\services\components;

use Nerrad\WPCLI\EE\abstracts\ComponentAbstract;
use Nerrad\WPCLI\EE\entities\components\ComponentString;

class AdminPages extends ComponentAbstract
{


    /**
     * Return an array of parts output to the registration array for this component when the addon is registered.
     * @return array
     */
    public function registrationParts()
    {
        return array(
            "'admin_path' => '{$this->constants['path']}admin'",
            "'admin_callback' => ''"
        );
    }


    /**
     * If this component has any autoloader paths needing registered with the 'autoloader_paths' array in addon registration
     * it should get returned via this method.
     * @return array
     */
    public function autoloaderPaths()
    {
       $paths = array();
       array_walk($this->component_strings, function (ComponentString $component_string) use ($paths) {
            $paths[] = "'{$this->getAdminClassName($component_string)}' => '{$this->getAdminPath($component_string)}'";
       });
       return $paths;
    }


    /**
     * The name of the component. eg 'module' or 'message_type'.  Should correspond to the
     * argument key used by the related command.
     * @return string
     */
    public function componentName()
    {
        return 'admin_pages';
    }


    /**
     * Returns the class name assembled from known data points.
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
        return $this->constants['path']
               . 'admin/'
               . strtolower($component_string->name())
               . '/'
               . $this->getAdminClassName($component_string, $init)
               . '.core.php';
    }
}