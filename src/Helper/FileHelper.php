<?php

namespace Dvsa\Olcs\Utils\Helper;

/**
 * Utility file for File
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class FileHelper
{
    /**
     * Get file extention from path
     *
     * @param string $path Path to file
     *
     * @return string
     */
    public static function getExtension($path)
    {
        return substr(strrchr($path, '.'), 1);
    }
}
