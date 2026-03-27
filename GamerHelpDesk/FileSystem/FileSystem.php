<?php
/**
 * File: FileSystem.php
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
 * The FileSystem class provides methods for performing various file system operations, such as reading and writing files, creating and deleting directories, and listing files and directories.
 * It also includes methods for getting information about files and directories, as well as loading XML files. 
 * The class throws GamerHelpDeskException exceptions for various error conditions, such as when a file or directory is not found, when a file is not readable or writable, or when an invalid argument is provided.
 * The FileSystem class is designed to be used as a utility class for handling file system operations in the GamerHelpDesk application, 
 * and it can be easily extended or modified to include additional functionality as needed.
 * 
 * @package GamerHelpDesk\FileSystem
 * @version 1.0.0
 */
class FileSystem
{
    
    /**
     * Reads the contents of a file.
     * 
     * @param string $filePath The path to the file to read.
     * @return string The contents of the file.
     * @throws GamerHelpDeskException If the file does not exist or if it is not readable.
     */
    public function readFile(string $filePath): string
    {
        if(!file_exists(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File not found: $filePath");
        }

        if(!is_readable(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File is not readable: $filePath");
        }

        return file_get_contents(filename:$filePath);
    }

    /**
     * Writes the given content to a file.
     * 
     * @param string $filePath The path to the file to write.
     * @param string $content The content to write to the file.
     * @param string $mode The write mode to use. Available modes are "append", "prepend", and "overwrite".
     * @return bool Returns true on success, false on failure.
     * @throws GamerHelpDeskException If the file does not exist or if it is not writable.
     */
    public function writeFile(string $filePath, string $content, string $mode = "append"): bool
    {
        if(!file_exists(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File not found: $filePath");
        }

        if(!is_writable(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File is not writable: $filePath");
        }

        match(strtolower(string: $mode))
        {
            "append"    => $success = file_put_contents(filename: $filePath, data: $content, flags: FILE_APPEND),
            "prepend"   => $success = file_put_contents(filename: $filePath, data: $content . file_get_contents(filename: $filePath)),
            "overwrite" => $success = file_put_contents(filename: $filePath, data: $content),
            default     => throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Invalid write mode: $mode")
        };

        return $success;
    }

    /**
     * Creates a new file with the given content.
     * 
     * @param string $filePath The path to the file to create.
     * @param string $content The content to write to the file.
     * @throws GamerHelpDeskException If the file already exists or if the directory is not writable.
     */
    public function createFile(string $filePath, string $content = ""): void
    {
        if(file_exists(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File already exists: $filePath");
        }

        if(!is_writable(dirname(path: $filePath)))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory is not writable: " . dirname(path: $filePath));
        }

        if(file_put_contents(filename: $filePath, data: $content) === false)
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Failed to create file: $filePath. Check permissions.");
        }
    }

    /**
     * Deletes a file.
     * 
     * @param string $filePath The path to the file to delete.
     * @throws GamerHelpDeskException If the file does not exist or if it is not writable.
     */
    public function deleteFile(string $filePath): void
    {
        if(!file_exists(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File not found: $filePath");
        }

        if(!is_writable(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Cannot delete file: $filePath. Check permissions.");
        }

        if(!unlink(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Failed to delete file: $filePath. Check permissions.");
        }
    }

    /**
     * Deletes a directory and its contents recursively.
     * 
     * @param string $directoryPath The path to the directory to delete.
     * @throws GamerHelpDeskException If the directory does not exist or if it is not writable(permissions).
     */
    public function deleteDirectory(string $directoryPath): void
    {
        if(!is_dir(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory not found: $directoryPath");
        }

        if(!is_writable(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Cannot delete directory: $directoryPath. Check permissions.");
        }

        $items = new RecursiveDirectoryIterator(directory: $directoryPath);
        $items->setFlags(FilesystemIterator::SKIP_DOTS);
        $items = new RecursiveIteratorIterator(iterator: $items, mode: RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($items as $item)
        {
            /** @var SplFileInfo $item */
            if ($item->isDir()) 
            {
                if(!rmdir(directory: $item->getRealPath()))
                {
                    throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Failed to delete directory: {$item->getRealPath()}. Check permissions.");
                }
            } 
            else 
            {
                if(!unlink(filename:$item->getRealPath()))
                {
                    throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Failed to delete file: {$item->getRealPath()}. Check permissions.");
                }
            }
        }

        if(!rmdir(directory: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Failed to delete directory: $directoryPath. Check permissions.");
        }
    }

    /**
     * Creates a new directory.
     * 
     * @param string $directoryPath The path to the directory to create.
     * @param int $permissions The permissions for the new directory.
     * @param bool $recursive Whether to create parent directories if they don't exist.
     * @throws GamerHelpDeskException If the directory already exists or if the parent directory is not writable.
     */
    public function createDirectory(string $directoryPath, int $permissions = 0755, bool $recursive = true): void
    {
        if(is_dir(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory already exists: $directoryPath");
        }

        if(!is_writable(dirname(path: $directoryPath)))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Cannot create directory: $directoryPath. Check permissions.");
        }

        if(!mkdir(directory: $directoryPath, permissions: $permissions, recursive: $recursive))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Failed to create directory: $directoryPath. Check permissions.");
        }
    }

    /**
     * Copies a file from one location to another.
     * 
     * @param string $sourcePath The path to the source file.
     * @param string $destinationPath The path to the destination file.
     * @param bool $overwrite Whether to overwrite the destination file if it already exists.
     * @throws GamerHelpDeskException If the source file does not exist or if the source file is not readable or if the destination directory does not exist
     *                                   or if the destination directory is not writable or if the destination file already exists and overwrite is set to false.
     */
    public function copyFile(string $sourcePath, string $destinationPath, bool $overwrite = false): void
    {
        if(!file_exists(filename: $sourcePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Source file not found: $sourcePath");
        }

        if(!is_readable(filename: $sourcePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Source file is not readable: $sourcePath. Check permissions.");
        }

        if(!is_dir(dirname(path: $destinationPath)))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Destination directory does not exist: " . dirname(path: $destinationPath));
        }

        if(file_exists(filename: $destinationPath) && !$overwrite)
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Destination file already exists: $destinationPath and overwrite is set to false.");
        }

        if(!is_writable(dirname(path: $destinationPath)))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Cannot copy file to destination: $destinationPath. Check permissions.");
        }

        if(!copy(from: $sourcePath, to:  $destinationPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Failed to copy file from $sourcePath to $destinationPath. Check permissions.");
        }
    }

    /**
     * Moves a file from one location to another.
     * 
     * @param string $sourcePath The path to the source file.
     * @param string $destinationPath The path to the destination file.
     * @param bool $overwrite Whether to overwrite the destination file if it already exists.
     * @throws GamerHelpDeskException If the source file does not exist or if the source file is not readable or if the destination directory does not exist
     *                                   or if the destination directory is not writable or if the destination file already exists and overwrite is set to false.
     */
    public function moveFile(string $sourcePath, string $destinationPath, bool $overwrite = false): void
    {
        if(!file_exists(filename: $sourcePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Source file not found: $sourcePath");
        }

        if(!is_readable(filename:$sourcePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Source file is not readable: $sourcePath. Check permissions.");
        }

        if(!is_dir(filename: dirname(path: $destinationPath)))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Destination directory does not exist: " . dirname($destinationPath));
        }

        if(file_exists(filename: $destinationPath) && !$overwrite)
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Destination file already exists: $destinationPath and overwrite is set to false.");
        }

        if(!is_writable(filename: dirname(path:$destinationPath)))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Cannot move file to destination: $destinationPath. Check permissions.");
        }

        if(!rename(from: $sourcePath, to: $destinationPath))
        {
            if(!copy(from: $sourcePath, to:  $destinationPath) && !unlink(filename: $sourcePath))
            {
                throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Failed to move file from $sourcePath to $destinationPath. Check permissions.");
            }
        }
    }

    /**
     * Lists all files in a directory.
     * @param string $directoryPath The path to the directory to list files from.
     * @return array An array of file paths.
     * @throws GamerHelpDeskException If the directory does not exist or if it is not readable.
     */
    public function listFiles(string $directoryPath): array
    {
        if(!is_dir(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory not found: $directoryPath");
        }

        if(!is_readable(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory is not readable: $directoryPath. Check permissions.");
        }

        $files = [];
        $items = new RecursiveDirectoryIterator(directory: $directoryPath);
        $items->setFlags(FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS);
        $items = new RecursiveIteratorIterator(iterator: $items, mode: RecursiveIteratorIterator::SELF_FIRST);

        foreach ($items as $item)
        {
            /** @var SplFileInfo $item */
            if ($item->isFile()) 
            {
                $files[] = $item->getPathname();
            }
        }

        return $files;
    }

    public function listContent(string $directoryPath): array
    {
        if(!is_dir(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory not found: $directoryPath");
        }

        if(!is_readable(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory is not readable: $directoryPath. Check permissions.");
        }

        $content = [];
        $items = new RecursiveDirectoryIterator(directory: $directoryPath);
        $items->setFlags(FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS);
        $items = new RecursiveIteratorIterator(iterator:$items, mode: RecursiveIteratorIterator::SELF_FIRST);

        foreach ($items as $item)
        {
            /** @var SplFileInfo $item */
            $content[] = $item->getPathname();
        }

        return $content;
    }

    
    /**
     * Lists all directories in a given directory.
     * 
     * @param string $directoryPath The path to the directory to list directories from.
     * @return array An array of directory paths.
     * @throws GamerHelpDeskException If the directory does not exist or if it is not readable.
     */
    public function listDirectories(string $directoryPath): array
    {
        if(!is_dir(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory not found: $directoryPath");
        }

        if(!is_readable(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory is not readable: $directoryPath. Check permissions.");
        }

        $directories = [];
        $items = new RecursiveDirectoryIterator(directory: $directoryPath);
        $items->setFlags(FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS);
        $items = new RecursiveIteratorIterator(iterator:$items, mode: RecursiveIteratorIterator::SELF_FIRST);

        foreach ($items as $item)
        {
            /** @var SplFileInfo $item */
            if ($item->isDir()) 
            {
                $directories[] = $item->getPathname();
            }
        }

        return $directories;
    }

    
    /**
     * Lists all files and directories in a given directory, with their respective types.
     * 
     * @param string $directoryPath The path to the directory to list content from.
     * @return array An array of content, with each item containing the path and type ("directory" or "file").
     * @throws GamerHelpDeskException If the directory does not exist or if it is not readable.
     */
    public function listContentWithType(string $directoryPath): array
    {
        if(!is_dir(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory not found: $directoryPath");
        }

        if(!is_readable(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory is not readable: $directoryPath. Check permissions.");
        }

        $content = [];
        $items = new RecursiveDirectoryIterator(directory: $directoryPath);
        $items->setFlags(FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS);
        $items = new RecursiveIteratorIterator(iterator:$items, mode: RecursiveIteratorIterator::SELF_FIRST);

        foreach ($items as $item)
        {
            /** @var SplFileInfo $item */
            $content[] = [
                "path" => $item->getPathname(),
                "type" => $item->isDir() ? "directory" : "file"
            ];
        }

        return $content;
    }

    /**
     * Gets information about a file or directory.
     * 
     * @param string $filePath The path to the file or directory to get information from.
     * @return array An array containing information about the file or directory, with the following keys:
     *                          - "path": The path to the file or directory.
     *                          - "name": The name of the file or directory.
     *                          - "extension": The extension of the file (without dot).
     *                          - "size": The size of the file in bytes.
     *                          - "last_modified": The last modified time of the file or directory as a Unix timestamp.
     *                          - "is_directory": Whether the path points to a directory.
     *                          - "is_file": Whether the path points to a file.
     *                          - "is_readable": Whether the file or directory is readable.
     *                          - "is_writable": Whether the file or directory is writable.
     * @throws GamerHelpDeskException If the file or directory does not exist or if it is not readable.
     */
    public function getFileInfo(string $filePath): array
    {
        if(!file_exists(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File not found: $filePath");
        }

        if(!is_readable(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File is not readable: $filePath. Check permissions.");
        }

        $fileInfo = new SplFileInfo(filename: $filePath);
        return [
            "path" => $fileInfo->getRealPath(),
            "name" => $fileInfo->getFilename(),
            "extension" => $fileInfo->getExtension(),
            "size" => $fileInfo->getSize(),
            "last_modified" => $fileInfo->getMTime(),
            "is_directory" => $fileInfo->isDir(),
            "is_file" => $fileInfo->isFile(),
            "is_readable" => $fileInfo->isReadable(),
            "is_writable" => $fileInfo->isWritable(),
        ];
    }

    
    /**
     * Gets information about all files and directories in a given directory.
     * 
     * @param string $directoryPath The path to the directory to get information from.
     * @return array An array containing information about all files and directories in the directory, with the following keys for each item:
     *                          - "path": The path to the file or directory.
     *                          - "name": The name of the file or directory.
     *                          - "extension": The extension of the file (without dot).
     *                          - "size": The size of the file in bytes.
     *                          - "last_modified": The last modified time of the file or directory as a Unix timestamp.
     *                          - "is_directory": Whether the path points to a directory.
     *                          - "is_file": Whether the path points to a file.
     *                          - "is_readable": Whether the file or directory is readable.
     *                          - "is_writable": Whether the file or directory is writable.
     * @throws GamerHelpDeskException If the directory does not exist or if it is not readable.
     */
    public function getFileInfoByDirectory(string $directoryPath): array
    {
        if(!is_dir(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory not found: $directoryPath");
        }

        if(!is_readable(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory is not readable: $directoryPath. Check permissions.");
        }

        $contentInfo = [];
        $items = new RecursiveDirectoryIterator(directory: $directoryPath);
        $items->setFlags(FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS);
        $items = new RecursiveIteratorIterator(iterator:$items, mode: RecursiveIteratorIterator::SELF_FIRST);

        foreach ($items as $item)
        {
            /** @var SplFileInfo $item */
            $contentInfo[] = [
                "path" => $item->getRealPath(),
                "name" => $item->getFilename(),
                "extension" => $item->getExtension(),
                "size" => $item->getSize(),
                "last_modified" => $item->getMTime(),
                "is_directory" => $item->isDir(),
                "is_file" => $item->isFile(),
                "is_readable" => $item->isReadable(),
                "is_writable" => $item->isWritable(),
            ];
        }

        return $contentInfo;
    }

    /**
     * Returns an iterator that traverses the contents of a directory.
     * 
     * @param string $directoryPath The path to the directory to list content from.
     * @param int $depth The maximum depth to traverse. If set to 0 or less, the iterator will not traverse subdirectories.
     * @return Iterator An iterator that yields the contents of the directory.
     * @throws GamerHelpDeskException If the directory does not exist or if it is not readable.
     */
    public function listContentIterator(string $directoryPath, int $depth = 0): Iterator
    {
        if(!is_dir(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory not found: $directoryPath");
        }

        if(!is_readable(filename: $directoryPath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory is not readable: $directoryPath. Check permissions.");
        }

        $items = new RecursiveDirectoryIterator(directory: $directoryPath);
        $items->setFlags(FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS);
        $items = new RecursiveIteratorIterator(iterator:$items, mode: RecursiveIteratorIterator::SELF_FIRST);
        if($depth > 0)
        {
            $items->setMaxDepth(maxDepth: $depth);
        }

        return $items;
    }

    /**
     * Returns a SplFileInfo object for a given file path.
     * 
     * @param string $filePath The path to the file to get information from.
     * @return SplFileInfo A SplFileInfo object containing information about the file.
     * @throws GamerHelpDeskException If the file does not exist or if it is not readable.
     */
    public function getFileInfoObject(string $filePath): SplFileInfo
    {
        if(!file_exists(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File not found: $filePath");
        }

        if(!is_readable(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File is not readable: $filePath. Check permissions.");
        }

        return new SplFileInfo(filename: $filePath);
    }

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
        if(!file_exists(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File not found: $filePath");
        }

        if(!is_readable(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File is not readable: $filePath. Check permissions.");
        }

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
        if(!file_exists(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File not found: $filePath");
        }

        if(!is_readable(filename: $filePath))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File is not readable: $filePath. Check permissions.");
        }

        return json_decode(file_get_contents(filename: $filePath), associative: true, depth: 512,flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Filters the files in a given directory by their extensions.
     * 
     * @param string $directory The path to the directory to filter.
     * @param string|array $extensions A string or array of file extensions to filter by.
     * @return Iterator An iterator containing the files in the given directory which have one of the specified extensions.
     * @throws GamerHelpDeskException If the given directory does not exist or if it is not readable.
     */
    public function filterByExtension(string $directory, string|array $extensions): Iterator
    {
        if(!is_dir(filename: $directory))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory not found: $directory");
        }

        if(!is_readable(filename: $directory))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "Directory is not readable: $directory. Check permissions.");
        }

        $items  = new RecursiveDirectoryIterator(directory: $directory);
        $items->setFlags(FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS);
        $filter = new FilterByExtension(iterator: $items, extensions: $extensions);
        
        return new RecursiveIteratorIterator(iterator: $filter, mode: RecursiveIteratorIterator::SELF_FIRST);
    }

    /**
     * Counts the number of lines in a given file.
     * This function will ignore empty lines and lines which only contain whitespace.
     * @param string $filePath The path to the file to count lines from.
     * @return int The number of lines in the given file.
     * @throws GamerHelpDeskException If the given file does not exist or if it is not readable.
     */
    public function countLines(string $filePath): int
    {
        return count(file(filename: $filePath, flags: FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }  
    
    /**
     * Gets a specific line from a file.
     * This function will ignore empty lines and lines which only contain whitespace.
     * @param string $filePath The path to the file to get the line from.
     * @param int $lineNumber The number of the line to get.
     * @return string The contents of the specified line.
     * @throws GamerHelpDeskException If the given file does not exist or if it is not readable.
     */
    public function getLine(string $filePath, int $lineNumber): string
    {
        return file(filename: $filePath, flags: FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)[$lineNumber - 1];
    }
}