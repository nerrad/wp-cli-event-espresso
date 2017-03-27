<?php

namespace Nerrad\WPCLI\EE\entities\components;

use Nerrad\WPCLI\EE\interfaces\ComponentStringInterface;

class ComponentString implements ComponentStringInterface
{

    /**
     * Something like general-admin-page.
     * @var string
     */
    protected $slug;

    /**
     * Something like General Admin Page.
     * @var string
     */
    protected $name;


    /**
     * Something like General_Admin_Page
     * @var string
     */
    protected $package;


    public function __construct($slug)
    {
        $this->slug = strtolower(str_replace('_', '-', $slug));
        $this->name = ucwords(str_replace('-', ' ', $this->slug));
        $this->package = str_replace(' ', '_', $this->name);
    }


    public function slug()
    {
        return $this->slug;
    }


    public function name()
    {
        return $this->name;
    }

    public function package()
    {
        return $this->package;
    }

}