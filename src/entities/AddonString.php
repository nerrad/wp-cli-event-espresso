<?php

namespace Nerrad\WPCLI\EE\entities;

use Nerrad\WPCLI\EE\interfaces\AddonStringInterface;

/**
 * AddonString
 * Used to build and describe the various elements of an "AddonString".  Instantiated by a slug identifier supplied to
 * the constructor.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage entities
 * @author     Darren Ethier
 * @since      1.0.0
 */
class AddonString implements AddonStringInterface
{

    /**
     * Something like my-new-add-on.
     *
     * @var string
     */
    protected $slug;

    /**
     * Something like My New Addon.
     *
     * @var string
     */
    protected $name;


    /**
     * Something like My_New_Addon
     *
     * @var string
     */
    protected $package;


    /**
     * @var AddonBaseConstantString
     */
    protected $constants;


    /**
     * AddonString constructor.
     *
     * @param string $slug The slug that serves as the base for all entity elements.
     */
    public function __construct($slug)
    {
        $this->slug    = strtolower(str_replace('_', '-', $slug));
        $this->name    = ucwords(str_replace('-', ' ', $this->slug));
        $this->package = str_replace(' ', '_', $this->name);
    }


    /**
     * Something like 'some-slug'
     *
     * @return string
     */
    public function slug()
    {
        return $this->slug;
    }


    /**
     * Something like 'Some Slug'
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }


    /**
     * Something like 'Some_Slug'
     *
     * @return string
     */
    public function package()
    {
        return $this->package;
    }


    /**
     * This returns all the constant generated from the strings in this object.
     * This is lazy loaded because not every component type will need this (currently only used by
     * ComponentType::SCAFFOLD components)
     *
     * @return \Nerrad\WPCLI\EE\entities\AddonBaseConstantString
     */
    public function constants()
    {
        if (! $this->constants instanceof AddonBaseConstantString) {
            $this->constants = new AddonBaseConstantString($this);
        }
        return $this->constants;
    }

}