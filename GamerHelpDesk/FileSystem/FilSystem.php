<?php
/**
 * File: FilSystem.php
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

use GamerHelpDesk\Exception\{
    GamerHelpDeskException,
    GamerHelpDeskExceptionEnum
};
use JsonException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;
use SplFileInfo;
use Iterator;
use SimpleXMLIterator;

/**
 * This class provides various methods for performing file system operations, such as reading and writing files, creating and deleting files and directories, and retrieving information about files and directories.
 * It also includes methods for filtering files by their extensions and for loading XML and JSON files.
 *
 * @package GamerHelpDesk\FileSystem
 * @version 1.0.1
 */
class FileSystem
{
    /*************************************************
     * Public Methods
     * File manipulation and management
     * ************************************************
     */
    
    /**
     * Reads the contents of a file.
     *
     * This method reads the contents of the file located at the given path.
     * It checks if the file exists and if it is readable before attempting to read it.
     * If the checks pass, it returns the contents of the file as a string.
     *
     * @param string $filePath The path to the file to read.
     * @return string The contents of the file.
     * @throws GamerHelpDeskException If the file does not exist or if it is not readable.
     */
    public function readFile(string $filePath): string
    {
        $this->fileExists($filePath) && $this->isReadable($filePath);
        return file_get_contents(filename: $filePath);
    }
    
    /**
     * Writes the given content to a file.
     * 
     * This method writes the given content to the file located at the given path.
     * It checks if the file is writable before attempting to write to it.
     * If the checks pass, it writes the content to the file using the given write mode.
     * If the given write mode is not 'append', 'prepend' or 'overwrite', it throws a GamerHelpDeskException.
     * 
     * @param string $filePath The path to the file to write.
     * @param string $content The content to write to the file.
     * @param string $mode The write mode to use. Available modes are "append", "prepend" and "overwrite".
     * @throws GamerHelpDeskException If the file does not exist or if it is not writable.
     */
    public function writeFile(string $filePath, string $content, string $mode = "overwrite"): mixed
    {
        $this->isWritable($filePath);
        match(strtolower(string: $mode)) 
        {
            "append"    => $status = file_put_contents(filename: $filePath, data: $content, flags: FILE_APPEND),
            "prepend"   => $status = file_put_contents(filename: $filePath, data: $content . file_get_contents(filename: $filePath)),
            "overwrite" => $status = file_put_contents(filename: $filePath, data: $content),
             default => throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "Invalid write mode: $mode. Use 'append', 'prepend' or 'overwrite'."
            )
        };
        return $status;
    }

    /**
     * Deletes a file.
     * 
     * This method deletes the file located at the given path.
     * It checks if the file exists and if it is writable before attempting to delete it.
     * If the checks pass, it deletes the file using the unlink() function.
     * If the file deletion fails, it throws a GamerHelpDeskException.
     * 
     * @param string $filePath The path to the file to delete.
     * @throws GamerHelpDeskException If the file does not exist or if it is not writable.
     */
    public function deleteFile(string $filePath): mixed
    {
        $this->fileExists($filePath) && $this->isWritable($filePath);
        if(!unlink(filename: $filePath))
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "Failed to delete file: $filePath"
            );
        }
        return true;
    }

    /**
     * Creates a new file with the given content.
     * 
     * This method creates a new file located at the given path with the provided content.
     * It checks if the file already exists and if it is writable before attempting to create it.
     * If the checks pass, it creates the file using the file_put_contents() function.
     * If the file creation fails, it throws a GamerHelpDeskException.
     * 
     * @param string $filePath The path to the file to create.
     * @param string $content The content to write to the new file. Defaults to an empty string.
     * @throws GamerHelpDeskException If the file already exists or if it is not writable.
     */
    public function createFile(string $filePath, string $content = ""): mixed
    {
        if($this->fileExists($filePath))
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "File already exists: $filePath"
            );
        }
        $this->isWritable(dirname(path: $filePath));
        if(file_put_contents(filename: $filePath, data: $content) === false)
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "Failed to create file: $filePath"
            );
        }
        return true;
    }

    /**
    * Deletes a directory and all of its contents.
    * 
    * This method deletes the directory located at the given path, along with all of its contents (files and subdirectories).
    * It checks if the directory exists and if it is writable before attempting to delete it.
    * If the checks pass, it traverses the directory using a RecursiveIterator and deletes each file and subdirectory it encounters.
    * Finally, it deletes the main directory itself. If any deletion fails, it throws a GamerHelpDeskException.
    * 
    * @param string $directoryPath The path to the directory to delete.
    * @throws GamerHelpDeskException If the directory does not exist or if it is not writable.
    */
    public function deleteDirectory(string $directoryPath): mixed
    {
        $this->directoryExists($directoryPath) && $this->isWritable($directoryPath);
        $iterator = $this->listContentIterator(directoryPath: $directoryPath, depth: null);
        foreach($iterator as $item)
        {
            /** @var SplFileInfo $item */
            if($item->isDir())
            {
                if(!rmdir(directory: $item->getPathname())) 
                {
                    throw new GamerHelpDeskException(
                        GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                        "Failed to delete directory: " . $item->getPathname()
                    );
                }
            }
            else
            {
                if(!unlink(filename: $item->getPathname())) 
                {
                    throw new GamerHelpDeskException(
                        GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                        "Failed to delete file: " . $item->getPathname()
                    );
                }
            }
        }
        if(!rmdir(directory: $directoryPath)) 
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "Failed to delete directory: $directoryPath"
            );
        }
        return true;
    }

    /**
    * Creates a new directory.
    * 
    * This method creates a new directory located at the given path.
    * It checks if the directory already exists and if it is writable before attempting to create it.
    * If the checks pass, it creates the directory using the mkdir() function.
    * If the directory creation fails, it throws a GamerHelpDeskException.
    * 
    * @param string $directoryPath The path to the directory to create.
    * @param int $permissions The permissions to apply to the new directory. Defaults to 0755.
    * @param bool $recursive Whether to create intermediate directories if they don't exist. Defaults to true.
    * @throws GamerHelpDeskException If the directory already exists or if it is not writable.
    */
    public function createDirectory(string $directoryPath, int $permissions = 0755, bool $recursive = true): mixed
    {
        if($this->directoryExists($directoryPath))
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "Directory already exists: $directoryPath"
            );
        }
        $this->isWritable(dirname(path: $directoryPath));
        if(!mkdir(directory: $directoryPath, permissions: $permissions, recursive: $recursive))
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "Failed to create directory: $directoryPath"
            );
        }
        return true;
    }

    /**
    * Copies a file.
    * 
    * This method copies the file located at the source path to the destination path.
    * It checks if the source file exists and if it is readable, and if the destination file already exists and if it is writable before attempting to copy the file.
    * If the checks pass, it copies the file using the copy() function.
    * If the file copy fails, it throws a GamerHelpDeskException.
    * If the $overwrite parameter is set to false and the destination file already exists, it throws a GamerHelpDeskException without attempting to copy the file.
    * 
    * @param string $sourceFilePath The path to the source file to copy.
    * @param string $destinationFilePath The path to the destination file to copy to.
    * @param bool $overwrite Whether to overwrite the destination file if it already exists. Defaults to false.
    * @throws GamerHelpDeskException If the source file does not exist or if it is not readable, or if the destination file already exists and if it is not writable.
    */
    public function copyFile(string $sourceFilePath, string $destinationFilePath, bool $overwrite = false): mixed
    {
        $this->fileExists($sourceFilePath) && $this->isReadable($sourceFilePath); 
        if(!$overwrite && $this->fileExists($destinationFilePath))
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "Destination file already exists: $destinationFilePath"
            );
        }
        $this->isWritable(dirname(path: $destinationFilePath));
        if(!copy(from: $sourceFilePath, to: $destinationFilePath))
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "Failed to copy file from $sourceFilePath to $destinationFilePath"
            );
        }
        return true;
    }

    /**
     * Moves a file from one location to another.
     * 
     * This method moves the file located at the source path to the destination path.
     * It checks if the source file exists and if it is readable, and if the destination file already exists and if it is writable before attempting to move the file.
     * If the checks pass, it copies the file using the copyFile() method and then deletes the source file using the deleteFile() method.
     * If the file copy fails, it throws a GamerHelpDeskException without attempting to delete the source file.
     * If the file delete fails, it throws a GamerHelpDeskException.
     * If the $overwrite parameter is set to false and the destination file already exists, it throws a GamerHelpDeskException without attempting to move the file.
     * 
     * @param string $sourceFilePath The path to the source file to move.
     * @param string $destinationFilePath The path to the destination file to move to.
     * @param bool $overwrite Whether to overwrite the destination file if it already exists. Defaults to false.
     * @throws GamerHelpDeskException If the source file does not exist or if it is not readable, or if the destination file already exists and if it is not writable.
     * @return mixed Returns true on success, false on failure.
     */
    public function moveFile(string $sourceFilePath, string $destinationFilePath, bool $overwrite = false): mixed
    {
        if(!$this->copyFile(sourceFilePath: $sourceFilePath, destinationFilePath: $destinationFilePath, overwrite: $overwrite)
        && !$this->deleteFile(filePath: $sourceFilePath))
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
                "Failed to move file from $sourceFilePath to $destinationFilePath"
            );
        }
        return true;
    }
    /*************************************************
     * Public Methods
     * Iteration and Information Retrieval
     * ************************************************
     */

    /**
     * Returns information about a given file or directory.
     * 
     * @param string $filePath The path to the file or directory to get information from.
     * @return array An array containing information about the file or directory, with the following keys:
     *                          - "name": The name of the file or directory.
     *                          - "path": The path to the file or directory.
     *                          - "type": The type of the file or directory, either "directory" or "file".
     *                          - "size": The size of the file in bytes, or null if it is a directory.
     *                          - "last_modified": The last modified time of the file or directory as a string in the format "Y-m-d H:i:s".
     *                          - "permissions": The permissions of the file or directory as a string in the format "rwxr-x".
     *                          - "owner": The owner of the file or directory as an integer.
     *                          - "group": The group of the file or directory as an integer.
     *                          - "is_readable": Whether the file or directory is readable.
     *                          - "is_writable": Whether the file or directory is writable.
     *                          - "is_executable": Whether the file or directory is executable.
     *                          - "extension": The extension of the file (without dot), or null if it is a directory.
     * @throws GamerHelpDeskException If the file or directory does not exist or if it is not readable.
     */
    public function getFileInfo(string $filePath): array
    {
        $fileInfo = $this->returnFileInfo(filePath: $filePath);
        return [
            "name" => $fileInfo->getFilename(),
            "path" => $fileInfo->getPathname(),
            "type" => $fileInfo->isDir() ? "directory" : "file",
            "size" => $fileInfo->isFile() ? $fileInfo->getSize() : null,
            "last_modified" => date(format: "Y-m-d H:i:s", timestamp: $fileInfo->getMTime()),
            "permissions" => $fileInfo->getPerms(),
            "owner" => $fileInfo->getOwner(),
            "group" => $fileInfo->getGroup(),
            "is_readable" => $fileInfo->isReadable(),
            "is_writable" => $fileInfo->isWritable(),
            "is_executable" => $fileInfo->isExecutable(),
            "extension" => $fileInfo->getExtension(),
        ];
    }

    /**
     * Lists all files and directories in a given directory, with optional depth limit.
     * 
     * @param string $directoryPath The path to the directory to list content from.
     * @param int|null $depth The maximum depth to traverse, or null to traverse all subdirectories.
     * @return array An array containing information about all files and directories in the directory, with the following keys for each item:
     *                          - "name": The name of the file or directory.
     *                          - "path": The path to the file or directory.
     *                          - "type": The type of the file or directory, either "directory" or "file".
     *                          - "size": The size of the file in bytes, or null if it is a directory.
     *                          - "last_modified": The last modified time of the file or directory as a string in the format "Y-m-d H:i:s".
     * @throws GamerHelpDeskException If the directory does not exist or if it is not readable.
     * Note: If the $depth parameter is set to a non-null value, the method will only list files and directories up to the specified depth. 
     * A depth of 0 means only the contents of the specified directory will be listed, while a depth of 1 means the contents of the specified directory and its immediate subdirectories will be listed, and so on. If $depth is null, all subdirectories will be traversed regardless of their depth.
     */
    public function listDirectoryContent(string $directoryPath, int|null $depth = null): array
    {
        $this->directoryExists($directoryPath) && $this->isReadable($directoryPath);
        $content = [];
        $iterator = $this->listContentIterator(directoryPath: $directoryPath, depth: $depth);
        foreach($iterator as $item)
        {
            /** @var SplFileInfo $item */
            $content[] = [
                "name" => $item->getFilename(),
                "path" => $item->getPathname(),
                "type" => $item->isDir() ? "directory" : "file",
                "size" => $item->isFile() ? $item->getSize() : null,
                "last_modified" => date(format: "Y-m-d H:i:s", timestamp: $item->getMTime())
            ];
        }
        return $content;
    }
    
    /**
     * Returns an iterator that traverses the contents of a directory, with each item containing information about the file or directory.
     * 
     * @param string $directoryPath The path to the directory to list content from.
     * @param int|null $depth The maximum depth to traverse. If set to 0 or less, the iterator will not traverse subdirectories.
     * @return Iterator An iterator that yields the contents of the directory.
     * @throws GamerHelpDeskException If the directory does not exist or if it is not readable.
     */
    public function listDirectoryContentIterator(string $directoryPath, int|null $depth = null): Iterator
    {
        return $this->listContentIterator(directoryPath: $directoryPath, depth: $depth);
    }

    /**
     * Filters files in a given directory by their extensions.
     * 
     * @param string $directory The path to the directory to filter files from.
     * @param string|array $extensions A single extension or an array of extensions to filter by.
     * @return Iterator A FilterByExtension iterator which filters the children of the current item.
     * @throws GamerHelpDeskException If the directory does not exist or if it is not readable.
     */
    public function filterByExtension(string $directory, string|array $extensions): Iterator
    {
        $this->directoryExists($directory) && $this->isReadable($directory);
        $items  = new RecursiveDirectoryIterator(directory: $directory);
        $items->setFlags(FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS);
        $filter = new FilterByExtension(iterator: $items, extensions: $extensions);
        return new RecursiveIteratorIterator(iterator: $filter, mode: RecursiveIteratorIterator::SELF_FIRST);
    }    
    /**************************************************
     * Public Methods
     * File content parsing and manipulation
     * ************************************************
     */

    /**
     * Loads an XML file from a given path.
     * 
     * @param string $filePath The path to the XML file to load.
     * @return SimpleXMLIterator A SimpleXMLIterator object containing the loaded XML data.
     * @throws GamerHelpDeskException If the file does not exist or if it is not readable.
     * @throws GamerHelpDeskException If the XML file could not be loaded. The exception message will contain the errors encountered while loading the file.
     */
    public function loadXMLFile(string $filePath): SimpleXMLIterator
    {
        $this->fileExists($filePath) && $this->isReadable($filePath);
        libxml_use_internal_errors(use_errors: true);
        $xml = simplexml_load_file(filename: $filePath, class_name: SimpleXMLIterator::class, options: LIBXML_NOCDATA);
        if($xml === false)
        {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Failed to load XML file: $filePath. Errors: " . implode(", ", array_map(fn($error) => $error->message, $errors)));
        }
        return $xml;
    }
    
    /**
     * Loads a JSON file from a given path.
     * 
     * @param string $filePath The path to the JSON file to load.
     * @return array An array containing the decoded JSON data.
     * @throws GamerHelpDeskException If the file does not exist or if it is not readable.
     * @throws JsonException|GamerHelpDeskException If the JSON file could not be loaded. The exception message will contain the error encountered while loading the file.
     */
    public function getJSONFile(string $filePath): array
    {
        $this->fileExists($filePath) && $this->isReadable($filePath);
        return json_decode(file_get_contents(filename: $filePath), associative: true, depth: 512,flags: JSON_THROW_ON_ERROR);
    }
   
    /**
     * Creates a new JSON file with the given data.
     * 
     * @param string $filePath The path to the file to create.
     * @param array $data The data to write to the file.
     * @param string $mode The write mode to use. Available modes are "append", "prepend", and "overwrite".
     * @return mixed The result of the write operation.
     * @throws GamerHelpDeskException If the file already exists and the write mode is not "overwrite".
     * @throws JsonException|GamerHelpDeskException If the JSON data could not be written to the file. The exception message will contain the error encountered while writing the file.
     */
    public function createJSONFile(string $filePath, array $data, string $mode = "overwrite"): mixed
    {
        $jsonData = json_encode(value: $data, flags: JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR);
        return $this->writeFile(filePath: $filePath, content: $jsonData, mode: $mode);
    }

    /**
     * Counts the number of lines in a given file.
     * This function will ignore empty lines and lines which only contain whitespace.
     * 
     * @param string $filePath The path to the file to count lines from.
     * @return int The number of lines in the given file.
     * @throws GamerHelpDeskException If the given file does not exist or if it is not readable.
     */
    public function countLines(string $filePath): int
    {
        $this->fileExists($filePath) && $this->isReadable($filePath);
        return count(file(filename: $filePath, flags: FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES));
    }

    /**
     * Returns the contents of a specific line in a file.
     * This function will ignore empty lines and lines which only contain whitespace.
     * 
     * @param string $filePath The path to the file to get the line from.
     * @param int $lineNumber The number of the line to get.
     * @return string The contents of the specified line.
     * @throws GamerHelpDeskException If the given file does not exist or if it is not readable, or if the line number is out of range.
     */
    public function getLine(string $filePath, int $lineNumber): string
    {
        $this->fileExists($filePath) && $this->isReadable($filePath);
        $lines = file(filename: $filePath, flags: FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
        if($lineNumber < 1 || $lineNumber > count($lines))
        {
            throw new GamerHelpDeskException(
                GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION,
                "Line number out of range. The file has " . count($lines) . " lines."
            );
        }
        return $lines[$lineNumber - 1];
    }

    /**************************************************
     * Internal Methods
     * Verification and Utility
     * ************************************************
     */
    
    /**
     * Checks if a file exists.
     * 
     * @param string $filePath The path to the file to check.
     * @return bool True if the file exists, false otherwise.
     * @throws GamerHelpDeskException If the file does not exist.
     */
    private function fileExists(string $filePath): bool
    {
        if (file_exists($filePath)) 
        {
            return true;
        }
        throw new GamerHelpDeskException(
            GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
            "File not found: $filePath"
        );
    }
    
    /**
     * Checks if a directory exists.
     *
     * @param string $directoryPath The path to the directory to check.
     * @return bool True if the directory exists, false otherwise.
     * @throws GamerHelpDeskException If the directory does not exist.
     */
    private function directoryExists(string $directoryPath): bool
    {
        if (is_dir($directoryPath)) 
        {
            return true;
        }
        throw new GamerHelpDeskException(
            GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
            "Directory not found: $directoryPath"
        );
    }
    
    /**
     * Checks if a path is readable.
     * 
     * @param string $path The path to check.
     * @return bool True if the path is readable, false otherwise.
     * @throws GamerHelpDeskException If the path is not readable.
     */
    private function isReadable(string $path): bool
    {
        if (is_readable($path)) 
        {
            return true;
        }
        throw new GamerHelpDeskException(
            GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
            "Path is not readable: $path"
        );
    }

    /**
     * Checks if a path is writable.
     * 
     * @param string $path The path to check.
     * @return bool True if the path is writable, false otherwise.
     * @throws GamerHelpDeskException If the path is not writable.
     */
    private function isWritable(string $path): bool
    {
        if (is_writable($path)) 
        {
            return true;
        }
        throw new GamerHelpDeskException(
            GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION,
            "Path is not writable: $path"
        );
    }

    /**
     * Returns an iterator that traverses the contents of a directory.
     * 
     * @param string $directoryPath The path to the directory to list content from.
     * @param int|null $depth The maximum depth to traverse. If set to 0 or less, the iterator will not traverse subdirectories.
     * @return Iterator An iterator that yields the contents of the directory.
     * @throws GamerHelpDeskException If the directory does not exist or if it is not readable.
     */
    private function listContentIterator(string $directoryPath, int|null $depth): Iterator
    {
        $this->directoryExists($directoryPath) && $this->isReadable($directoryPath);
        $iterator = new RecursiveDirectoryIterator(directory: $directoryPath);
        $iterator->setFlags(FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS);
        $iterator = new RecursiveIteratorIterator(iterator:$iterator, mode: RecursiveIteratorIterator::SELF_FIRST);
        if($depth !== null && $depth >= 0)
        {
            $iterator->setMaxDepth(maxDepth: $depth);
        }
        return $iterator;
    }

    /**
     * Returns an SplFileInfo object for a file.
     * 
     * @param string $filePath The path to the file.
     * @return SplFileInfo An SplFileInfo object for the file.
     * @throws GamerHelpDeskException If the file does not exist or if it is not readable.
     */
    private function returnFileInfo(string $filePath): SplFileInfo
    {
        $this->fileExists($filePath) && $this->isReadable($filePath);
        return new SplFileInfo(filename: $filePath);
    }
}