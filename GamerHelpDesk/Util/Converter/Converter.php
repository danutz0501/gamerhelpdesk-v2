<?php
/**
 * File: Converter.php
 * Project: GamerHelpDesk
 * Created Date: March 2026
 * Author: danutz0501 (M. Dumitru Daniel)
 * -----
 * Last Modified:
 * Modified By:
 * -----
 * Copyright (c) 2026 M. Dumitru Daniel (M. Dumitru Daniel)
 *  This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace GamerHelpDesk\Util\Converter;

class Converter
{
    /**
     * Converts a string to a slug.
     * @param string $string The string to convert.
     * @return string The converted slug.
     */
    public static function stringToSlug(string $string): string
    {
        // Convert the string to lowercase
        $slug = strtolower($string);
        // Replace spaces and underscores with hyphens
        $slug = str_replace([' ', '_'], '-', $slug);
        // Remove all non-alphanumeric characters except hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        // Trim hyphens from the beginning and end of the slug
        $slug = trim($slug, '-');
        return $slug;
    }

    /**
    * Converts a file size in bytes to a human-readable format.
    * @param int $size The file size in bytes.
    * @return string The converted file size in a human-readable format.
    */
    public static function convertSize($size)
  {
     $unit=array('b','kb','mb','gb','tb','pb');
     return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }
}