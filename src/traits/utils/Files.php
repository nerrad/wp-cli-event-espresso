<?php

namespace Nerrad\WPCLI\EE\traits\utils;

/**
 * Files
 * Utility methods for working with files or getting classnames from files etc.
 *
 * @package Nerrad\WPCLI\EE\
 * @subpackage traits\utils
 * @author  Darren Ethier
 * @since   1.0.0
 */
trait Files
{
    /**
     * This receives a file_path and returns the class_name from that file path.
     * @param $file_path
     * @return bool|string
     */
    private function getClassnameFromFilePath($file_path)
    {
        //extract file from path
        $filename = basename($file_path);
        //now remove the first period and everything after
        $pos_of_first_period = strpos($filename, '.');

        return substr($filename, 0, $pos_of_first_period);
    }
}