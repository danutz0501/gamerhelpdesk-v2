<?php
/**
 * File: Zip.php
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

namespace GamerHelpDesk\Util\Zip;

use GamerHelpDesk\Exception\{
    GamerHelpDeskException,
    GamerHelpDeskExceptionEnum
};
use \ZipArchive;

/**
 * Custom Zip Utility Class
 * Provides methods to create, add files, and extract ZIP archives.
 * This class is designed to handle ZIP file operations with error handling and is intended for use within the GamerHelpDesk application.
 * It relies on the PHP ZipArchive class and includes custom exceptions for better error reporting.
 * Example usage is provided at the end of the class definition to demonstrate how to use the Zip utility for creating and extracting ZIP files.
 * Note: Ensure that the PHP zip extension is installed and enabled for this class to function properly.
 * @package GamerHelpDesk\Util\Zip
 * @version 1.0.0
 */
class Zip
{
    /** @var ZipArchive */
    private ZipArchive $zip;

    /**
     * Constructor initializes the ZipArchive instance.
     * If the ZipArchive class is not available, an exception will be thrown.
     *
     * @throws GamerHelpDeskException
     */
    public function __construct()
    {
        if (!class_exists('ZipArchive')) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::SYSTEM_EXCEPTION, "ZipArchive class is not available. Please ensure the PHP zip extension is installed and enabled.");
        }
        $this->zip = new ZipArchive();
    }

    /**
     * Create a new ZIP file.
     * If a file already exists at the specified path, it will be overwritten. 
     * If the ZIP file cannot be created for any reason, an exception will be thrown with details about the failure.
     *
     * @param string $zipFilePath Path to the ZIP file to create.
     * @return bool
     * @throws GamerHelpDeskException
     */
    public function createZip(string $zipFilePath): bool
    {
        if (file_exists($zipFilePath))
        {
            unlink($zipFilePath); // Remove existing file
        }

        if ($this->zip->open($zipFilePath, ZipArchive::CREATE) !== true) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::SYSTEM_EXCEPTION, "Unable to create ZIP file at: $zipFilePath");
        }
        return true;
    }

    /**
     * Add a file to the ZIP archive.
     * The $localName parameter allows you to specify a different name for the file inside the ZIP archive. If not provided, the original filename will be used.
     * If the file does not exist or is not readable, an exception will be thrown.
     * If the file cannot be added to the ZIP archive for any reason, an exception will be thrown with details about the failure.
     *
     * @param string $filePath Path to the file to add.
     * @param string|null $localName Optional name inside the ZIP.
     * @return bool
     * @throws GamerHelpDeskException
     */
    public function addFile(string $filePath, ?string $localName = null): bool
    {
        if (!file_exists($filePath) || !is_readable($filePath)) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::SYSTEM_EXCEPTION, "File does not exist or is not readable: $filePath");
        }

        $nameInZip = $localName ?? basename($filePath);
        if (!$this->zip->addFile($filePath, $nameInZip)) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::SYSTEM_EXCEPTION, "Failed to add file to ZIP: $filePath");
        }
        return true;
    }

    /**
     * Close the ZIP archive and finalize the creation.
     *
     * @return bool
     */
    public function closeZip(): bool
    {
        return $this->zip->close();
    }

    /**
     * Extract a ZIP archive to a directory.
     *
     * @param string $zipFilePath Path to the ZIP file.
     * @param string $destinationDir Destination directory.
     * @return bool
     * @throws GamerHelpDeskException
     */
    public function extractZip(string $zipFilePath, string $destinationDir): bool
    {
        if (!file_exists($zipFilePath)) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::SYSTEM_EXCEPTION, "ZIP file does not exist: $zipFilePath");
        }

        if (!is_dir($destinationDir) && !mkdir($destinationDir, 0777, true)) {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::SYSTEM_EXCEPTION, "Failed to create destination directory: $destinationDir");
        }

        if ($this->zip->open($zipFilePath) !== true) {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::SYSTEM_EXCEPTION, "Unable to open ZIP file: $zipFilePath");
        }

        if (!$this->zip->extractTo($destinationDir)) {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::SYSTEM_EXCEPTION, "Failed to extract ZIP to: $destinationDir");
        }

        $this->zip->close();
        return true;
    }
}

// // -------------------
// // Example Usage
// // -------------------
// try {
//     $zipper = new Zip();

//     // Create a new ZIP
//     $zipper->createZip("example.zip");

//     // Add files
//     $zipper->addFile("file1.txt");
//     $zipper->addFile("file2.txt", "custom_name.txt");

//     // Close ZIP
//     $zipper->closeZip();

//     echo "ZIP file created successfully.\n";

//     // Extract ZIP
//     $zipper->extractZip("example.zip", "output_folder");
//     echo "ZIP extracted successfully.\n";

// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }
