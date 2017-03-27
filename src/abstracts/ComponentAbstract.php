<?php

namespace Nerrad\WPCLI\EE\abstracts;

use Nerrad\WPCLI\EE\interfaces\ComponentInterface;
use Nerrad\WPCLI\EE\entities\components\ComponentString;

abstract class ComponentAbstract implements ComponentInterface
{
    /**
     * The arguments coming in from the command
     * @var array
     */
    protected $data = array();

    /**
     * Generated base constant references
     * Should contain the following keys:
     * * version
     * * core_version_required
     * * plugin_file
     * * basename
     * * path
     * * url
     * @var array
     */
    protected $constants = array();


    /**
     * @var ComponentString[]
     */
    protected $component_strings = array();

    public function __construct(array $data, array $constants)
    {
        $this->data = $data;
        $this->constants = $constants;
        $this->setComponentStrings();
    }


    /**
     * Returns whatever slugs were sent in as a part of the argument array for the component.
     * @return array
     */
    public function getSlugs()
    {
        $slugs = isset($this->data[$this->componentName()])
            ? explode(',', $this->data[$this->componentName()])
            : array();
        return empty($slugs) && isset($this->data['slugs'])
            ? explode(',', $this->data['slugs'])
            : array();
    }



    /**
     * Creates a ComponentString object for each component slugs provided by the component and assigns to the
     * component_strings property.
     * For example, admin_pages might have multiple "slugs" representing each admin page the user wants generated.
     */
    private function setComponentStrings()
    {
        $slugs = $this->getSlugs();
        array_walk($slugs, function($slug) {
           $this->component_strings[] = new ComponentString($slug);
        });
    }
}