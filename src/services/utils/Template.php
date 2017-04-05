<?php

namespace Nerrad\WPCLI\EE\services\utils;

/**
 * Template
 * Helpers for things to do with templates
 *
 * @package Nerrad\WPCLI\EE\
 * @subpackage services\utils
 * @author  Darren Ethier
 * @since   1.0.0
 */
class Template
{
    /**
     * Returns a space string with the number of spaces for the given count.
     * @param int $count
     * @return string
     */
    public static function xSpaces($count)
    {
        $content = '';
        do {
            $space = ' ';
            $content .= $space;
            $count--;
        } while ($count > 0);
        return $content;
    }


    /**
     * With an indent representing 4 spaces, this returns the number of indents for the given count.
     * @param int $count  number of indents
     * @return string
     */
    public static function xIndents($count)
    {
        $content = '';
        do {
            $content .= self::xSpaces(4);
            $count --;
        } while($count > 0);
        return $content;
    }
}