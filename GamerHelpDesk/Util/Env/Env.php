<?php
/**
 * File: Env.php
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

namespace GamerHelpDesk\Util\Env;

use GamerHelpDesk\Exception\{
    GamerHelpDeskException,
    GamerHelpDeskExceptionEnum
};
use FileSystemIterator;

/**
 * Class Env
 * This class is responsible for loading environment variables from a .env file and providing access to them.
 * It supports loading from a specified directory, parsing key=value pairs, and setting environment variables in the $_ENV and $_SERVER superglobals as well as using putenv().
 * It also supports expanding environment variables in values using {VAR_NAME} syntax and only sets variables if they are not already set.
 * It also provides a method to retrieve environment variable values with an optional default value if the variable is not set.
 * It will throw a GamerHelpDeskException if the specified directory or file does not exist or is not readable.
 * Note: This class is designed to be used in the bootstrap process of the application to load environment variables before handling any requests.
 * It is recommended to place the .env file in the base directory of the application and call the load method with that directory to ensure environment variables are available throughout the application.
 * @package GamerHelpDesk\Util\Env
 * @version 1.0.0
 */
class Env
{
    /**
     * Loads environment variables from a .env file in the specified directory.
     * Supports comments (# or ;) and ignores empty lines.
     * Will throw a GamerHelpDeskException if the directory does not exist or is not readable.
     * Will stop after loading the first .env file found.
     *
     * @param string $directory The directory to search for a .env file.
     * @throws GamerHelpDeskException If the directory does not exist or is not readable.
     */
    public function load(string $directory): void
    {
        $filePath = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if(!is_dir($directory))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "The specified directory does not exist: {$directory} .");
        }
        if(!is_readable($directory))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "The specified directory is not readable: {$directory} .");
        }

        $iterator = new FileSystemIterator($directory);
        foreach ($iterator as $fileInfo) 
        {
            if ($fileInfo->isFile() && $fileInfo->getFilename() === '.env') 
            {
                $this->loadEnvFile($fileInfo->getPathname());
                break; // Stop after loading the first .env file found
            }
        }
    }
    
    /**
     * Loads environment variables from a .env file at the specified path.
     * Supports comments (# or ;) and ignores empty lines.
     * Parses key=value pairs and sets them in $_ENV, $_SERVER, and using putenv().
     * Expands environment variables in values using {VAR_NAME} syntax.
     * Only sets environment variables if they are not already set in $_ENV, $_SERVER, or getenv().
     * Casts values to appropriate types (boolean, integer, float).
     * Will throw a GamerHelpDeskException if the file does not exist or is not readable.
     * Will stop after loading the first .env file found.
     *
     * @param string $filePath The path to the .env file to load.
     * @throws GamerHelpDeskException If the file does not exist or is not readable.
     */
    private function loadEnvFile(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];

        foreach ($lines as $line)
        {
            $line = trim($line);

            // Skip comments
            if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, ';'))
            {
                continue;
            }

            // Parse key=value
            if (strpos($line, '=') === false) 
            {
                continue; // Ignore malformed lines
            }

            list($name, $value) = explode('=', $line, 2);

            $name  = trim($name);
            $value = trim($value);

            // Remove surrounding quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'")) ||
                (str_starts_with($value, '{') && str_ends_with($value, '}'))) 
            {
                $value = substr($value, 1, -1);
            }

            $env[$name] = $value;
        }

        // Expand environment variables in values using {VAR_NAME} syntax
        foreach ($env as $key => $value) 
        {
            $env[$key] = preg_replace_callback('/\{([A-Z0-9_]+)\}/i', function ($matches) use ($env) 
            {
                $varName = $matches[1];
                return $env[$varName] ?? $matches[0]; // Keep original if not found
            }, $value);
        }

        // Set environment variables
        foreach ($env as $name => $value)
        {
            // Only set the environment variable if it is not already set in $_ENV, $_SERVER, or getenv()
            if (!isset($_ENV[$name]) && !isset($_SERVER[$name]) && getenv($name) === false) 
            {
                $_ENV[$name]    = $this->castValue($value);
                $_SERVER[$name] = $this->castValue($value);
                $this->castValue($value) && putenv("{$name}={$value}");
            }
        }
    }

    /**
     * Get an environment variable value.
     *
     * @param string $key The variable name.
     * @param mixed $default Default value if not found.
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Casts a value to the appropriate type (boolean, integer, float).
     *
     * @param string $value The value to cast.
     * @return mixed The casted value.
     */
    private function castValue(string $value): mixed
    {
        return match (strtolower($value)) {
            'true' => true,
            'false' => false,
            'null' => null,
            default => is_numeric($value) ? (strpos($value, '.') !== false ? (float)$value : (int)$value) : $value,
        };
    }
}