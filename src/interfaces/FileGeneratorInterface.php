<?php

namespace Nerrad\WPCLI\EE\interfaces;


/**
 * Interface FileGeneratorInterface
 * Interface for all classes generating files.
 *
 * @package Nerrad\WPCLI\EE
 * @subpackage interfaces
 * @author  Darren Ethier
 * @since   1.0.0
 */
interface FileGeneratorInterface
{

    /**
     * This is called to take care of actually writing files.
     */
    public function writeFiles();
}