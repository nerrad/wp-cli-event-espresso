<?php

namespace Nerrad\WPCLI\EE\abstracts;

use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\entities\components\ComponentString;
use Nerrad\WPCLI\EE\entities\AddonBaseTemplateArguments;

abstract class ComponentTemplateArgumentsAbstract extends BaseTemplateArgumentsAbstract
{

    /**
     * @var ComponentString;
     */
    protected $component_string;


    /**
     * @var AddonBaseTemplateArguments;
     */
    protected $addon_base_template_arguments;


    /**
     * ComponentTemplateArguments constructor.
     *
     * @param \Nerrad\WPCLI\EE\entities\components\ComponentString $component_string
     * @param \Nerrad\WPCLI\EE\entities\AddonBaseTemplateArguments $addon_base_template_arguments
     * @param \Nerrad\WPCLI\EE\entities\AddonString                $addon_string
     * @param                                                      $data
     * @param bool                                                 $force
     */
    public function __construct(
        ComponentString $component_string,
        AddonBaseTemplateArguments $addon_base_template_arguments,
        AddonString $addon_string,
        $data,
        $force = false
    ) {
        $this->addon_string = $addon_string;
        $this->component_string = $component_string;
        $this->addon_base_template_arguments = $addon_base_template_arguments;
        parent::__construct($addon_string, $data, $force);
    }
}