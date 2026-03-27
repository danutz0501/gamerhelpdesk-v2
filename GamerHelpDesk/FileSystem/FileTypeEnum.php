<?php
/**
 * File: FileTypeEnum.php
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

namespace GamerHelpDesk\FileSystem;

enum FileTypeEnum: string
{
    // Document types
    case TXT = ".txt";
    case PDF = ".pdf";
    case DOCX = ".docx";
    case XLSX = ".xlsx";
    case PPTX = ".pptx";
    case CSV = ".csv";
    case JSON = ".json";
    case XML = ".xml";
    case HTML = ".html";
    
    // Image types
    case JPEG = ".jpeg";
    case JPG = ".jpg";
    case PNG = ".png";
    case GIF = ".gif";
    case ICO = ".ico";
    case SVG = ".svg";
    case SVGZ = ".svgz";
    case BMP = ".bmp";
    case TIFF = ".tiff";
    case WEBP = ".webp";
    
    // Web types
    case JAVASCRIPT = ".js";
    case CSS = ".css";
    case WEBASSEMBLY = ".wasm";
    
    // Archive types
    case ZIP = ".zip";
    case RAR = ".rar";
    case GZ = ".gz";
    case TAR = ".tar";
    
    // Audio types
    case MP3 = ".mp3";
    case WAV = ".wav";
    
    // Video types
    case MP4 = ".mp4";
    case WEBM = ".webm";
    case AVI = ".avi";
    case MKV = ".mkv";
    case MOV = ".mov";
    case FLV = ".flv";
    case WMV = ".wmv";
    
    // Font types
    case OTF = ".otf";
    case TTF = ".ttf";
    case WOFF = ".woff";
    case WOFF2 = ".woff2";
    
    // Binary/Executable types
    case BINARY = ".bin";
    case EXECUTABLE = ".exe";
    case DLL = ".dll";
    case LIBRARY = ".lib";
    case OBJECT = ".obj";
    case SO = ".so";

    
    /**
     * Returns the FileTypeEnum associated with the given file extension, or null if no matching file type is found.
     * @param string $extension The file extension to find the FileTypeEnum for.
     * @return ?FileTypeEnum The FileTypeEnum associated with the given file extension, or null if no matching file type is found.
     */
    public static function fromExtension(string $extension): ?FileTypeEnum
    {
        $extension = strtolower($extension);
        foreach (self::cases() as $fileType)
        {
            if ($fileType->value === $extension)
            {
                return $fileType;
            }
        }
        return null; // Return null if no matching file type is found
    }

    /**
     * Returns the file extension associated with the current FileTypeEnum.
     * Alias for Enum value, provided for clarity and consistency with other methods.
     * @return string The file extension associated with the current FileTypeEnum.
     */   
     public function getExtension(): string
    {
        return $this->value;
    }

    /**
     * Returns the file extension associated with the current FileTypeEnum as a string.
     * This method is called when the object is cast to a string, and is used to display the file extension in a human-readable format.
     * @return string The file extension associated with the current FileTypeEnum.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
    * Returns a string representing the name of the current file type.
    * @return string
    * @example "PDF"
    */
    public function label(): string 
    {
        return static::from(value: $this->value)->name;
    }

    /**
    * Checks if the current file type is in the given list of file types.
    * @param array $fileTypes An array of FileTypeEnum objects to check against.
    * @return bool True if the current file type is in the list, false otherwise.
    */
    public function isInList(array $fileTypes): bool
    {
        foreach ($fileTypes as $fileType)
        {
            if ($this === $fileType)
            {
                return true;
            }
        }
        return false;
    }
}