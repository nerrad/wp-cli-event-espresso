<?php

namespace Nerrad\WPCLI\EE\utils;


class Files
{
    public static function getClassnameFromFilePath($file_path)
    {
        //extract file from path
        $filename = basename($file_path);
        //now remove the first period and everything after
        $pos_of_first_period = strpos($filename, '.');

        return substr($filename, 0, $pos_of_first_period);
    }
}