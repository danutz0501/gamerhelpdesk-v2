<?php
/**
 * File: FileTypeResponseEnum.php
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

namespace GamerHelpDesk\Http\Response;

/**
 * FileTypeResponseEnum class
 * Represents the different types of file responses that can be returned by the application.
 * @package GamerHelpDesk\Http\Response
 * @version 1.0.0
 */
enum FileTypeResponseEnum: string
{
    // Documents
    case PDF = "application/pdf";
    case DOCX = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
    case XLSX = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    case PPTX = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
    case TXT = "text/plain";
    case CSV = "text/csv";
    
    // Data Formats
    case JSON = "application/json";
    case XML = "application/xml";
    
    // Web
    case HTML = "text/html";
    case JAVASCRIPT = "application/javascript";
    case CSS = "text/css";
    
    // Images
    case JPEG = "image/jpeg";
    case JPG = "image/jpg";
    case PNG = "image/png";
    case GIF = "image/gif";
    case ICO = "image/x-icon";
    case SVG = "image/svg+xml";
    case SVGZ = "image/svg+xml-compressed";
    case BMP = "image/bmp";
    case TIFF = "image/tiff";
    case WEBP = "image/webp";
    
    // Audio
    case MP3 = "audio/mpeg";
    case OGG = "audio/ogg";
    case WAV = "audio/wav";
    
    // Video
    case MP4 = "video/mp4";
    case WEBM = "video/webm";
    case AVI = "video/x-msvideo";
    case MOV = "video/quicktime";
    case MKV = "video/x-matroska";
    
    // Archives
    case ZIP = "application/zip";
    case RAR = "application/vnd.rar";
    case TAR = "application/x-tar";
    case GZIP = "application/gzip";
    case BZIP2 = "application/x-bzip2";
    
    // Fonts
    case OTF = "font/otf";
    case TTF = "font/ttf";
    case WOFF = "font/woff";
    case WOFF2 = "font/woff2";
    
    // Binary
    case BINARY = "application/octet-stream";


     /** Returns the MIME type associated with the current file type.
     * @return string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types
     * @see https://www.iana.org/assignments/media-types/media-types.xhtml
     */
    public function getMimeType(): string
    {
        return $this->value;
    }

     /**
     * Returns a string representing the Content-Type HTTP header
     * associated with the current file type.
     *
     * @return string
     * @example "Content-Type: application/pdf"
     */
    public function getContentTypeHeader(): string
    {
        return "Content-Type: " . $this->value;
    }

        /** Returns the file extension associated with the current file type.
     * @return string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types
     * @see https://www.iana.org/assignments/media-types/media-types.xhtml
     */
    public function getExtension(): string
    {
        return pathinfo(path: $this->value, flags: PATHINFO_EXTENSION);
    }

    /**
     * Returns a string representing the name of the current file type.
     *
     * @return string
     * @example "PDF"
     */
    public function label(): string 
    {
        return static::from(value: $this->value)->name;
    }

    /**
    * Static method to get the label of a file type enum value.
    *
    * @param FileTypeResponseEnum $fileType The file type enum value.
    * @return string The label associated with the given file type.
    */
    public static function getLabel(FileTypeResponseEnum $fileType): string
    {
        return match($fileType){
            FileTypeResponseEnum::PDF => "application/pdf",
            FileTypeResponseEnum::DOCX => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            FileTypeResponseEnum::XLSX => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            FileTypeResponseEnum::PPTX => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            FileTypeResponseEnum::TXT => "text/plain",
            FileTypeResponseEnum::CSV => "text/csv",
            FileTypeResponseEnum::JSON => "application/json",
            FileTypeResponseEnum::XML => "application/xml",
            FileTypeResponseEnum::HTML => "text/html",
            FileTypeResponseEnum::JAVASCRIPT => "application/javascript",
            FileTypeResponseEnum::CSS => "text/css",
            FileTypeResponseEnum::JPEG => "image/jpeg",
            FileTypeResponseEnum::JPG => "image/jpg",
            FileTypeResponseEnum::PNG => "image/png",
            FileTypeResponseEnum::GIF => "image/gif",
            FileTypeResponseEnum::ICO => "image/x-icon",
            FileTypeResponseEnum::SVG => "image/svg+xml",
            FileTypeResponseEnum::SVGZ => "image/svg+xml-compressed",
            FileTypeResponseEnum::BMP => "image/bmp",
            FileTypeResponseEnum::TIFF => "image/tiff",
            FileTypeResponseEnum::WEBP => "image/webp",
            FileTypeResponseEnum::MP3 => "audio/mpeg",
            FileTypeResponseEnum::OGG => "audio/ogg",
            FileTypeResponseEnum::WAV => "audio/wav",
            FileTypeResponseEnum::MP4 => "video/mp4",
            FileTypeResponseEnum::WEBM => "video/webm",
            FileTypeResponseEnum::AVI => "video/x-msvideo",
            FileTypeResponseEnum::MOV => "video/quicktime",
            FileTypeResponseEnum::MKV => "video/x-matroska",
            FileTypeResponseEnum::ZIP => "application/zip",
            FileTypeResponseEnum::RAR => "application/vnd.rar",
            FileTypeResponseEnum::TAR => "application/x-tar",
            FileTypeResponseEnum::GZIP => "application/gzip",
            FileTypeResponseEnum::BZIP2 => "application/x-bzip2",
            FileTypeResponseEnum::OTF => "font/otf",
            FileTypeResponseEnum::TTF => "font/ttf",
            FileTypeResponseEnum::WOFF => "font/woff",
            FileTypeResponseEnum::WOFF2 => "font/woff2",
            FileTypeResponseEnum::BINARY => "application/octet-stream",
        };
    }

    /**
     * Returns the value of the current file type enum as a string.
     *
     * @return string
     * @example "application/pdf"
     */
    public function __toString(): string
    {
        return $this->value;
    }
}