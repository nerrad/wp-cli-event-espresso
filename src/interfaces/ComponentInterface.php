<?php
namespace Nerrad\WPCLI\EE\interfaces;

interface ComponentInterface
{

    /**
     * Return an array of parts output to the registration array for this component when the addon is registered.
     * @return array
     */
    public function registrationParts();


    /**
     * If this component has any autoloader paths needing registered with the 'autolaoder_paths' array in addon registration
     * it should get returned via this method.
     * @return array
     */
    public function autoloaderPaths();


    /**
     * The name of the component. eg 'module' or 'message_type'.  Should correspond to the
     * argument key used by the related command.
     * @return string
     */
    public function componentName();


    /**
     * This should return any slugs being registered for the component.
     * @return array
     */
    public function getSlugs();
}