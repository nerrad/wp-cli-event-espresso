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


    /**
     * Just returns a nice formatted string for the parts in the format `array( part1, part2, ...)`
     *
     * @param $parts
     * @return string
     */
    public static function formattedArrayString($parts, $indent_base = 4)
    {
        return 'array('
               . PHP_EOL
               . Template::xIndents($indent_base)
               . implode(',' . PHP_EOL . Template::xIndents($indent_base), $parts)
               . ','
               . PHP_EOL
               . Template::xIndents($indent_base - 1)
               . ')';
    }
}