<?php

namespace Nerrad\WPCLI\EE\interfaces;


/**
 * Interface AddonStringInterface
 * This is the interface for any class that is used to expose core addon strings such as slug, name, and package.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage interfaces
 * @author     Darren Ethier
 * @since      1.0.0
 */
interface AddonStringInterface
{
    /**
     * Typically this would be something like 'some-slug'
     *
     * @return string
     */
    public function slug();


    /**
     * Typically this would be something like 'Some Slug'
     *
     * @return string
     */
    public function name();


    /**
     * Typically this would be something like 'Some_Slug'
     *
     * @return string
     */
    public function package();
}