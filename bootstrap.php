<?php
/**
 * File: bootstrap.php
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

/**
 * Error reporting and display settings for development environment.
 */
error_reporting(error_level: E_ALL);
ini_set(option: "display_errors",                  value: 1);
ini_set(option: "xdebug.var_display_max_depth",    value: "25");
ini_set(option: "xdebug.var_display_max_children", value: "512");
ini_set(option: "xdebug.var_display_max_data",     value: "2048");

/**
 * Set UTF-8 encoding.
 */
mb_internal_encoding(encoding: "UTF-8");
mb_http_output(encoding: "UTF-8");

/**
 * Define the base path and other important paths for the application.
 */
define(constant_name: 'BASE_PATH',     value: realpath(path: __DIR__) . DIRECTORY_SEPARATOR);
define(constant_name: 'ARCHIVE_PATH',  value: BASE_PATH . 'Archive' . DIRECTORY_SEPARATOR);
define(constant_name: 'COMPOSER_PATH', value: BASE_PATH . 'vendor' . DIRECTORY_SEPARATOR);
define(constant_name: 'CONFIG_PATH',   value: BASE_PATH . 'Config' . DIRECTORY_SEPARATOR);
define(constant_name: 'DATABASE_PATH', value: BASE_PATH . 'Database' . DIRECTORY_SEPARATOR);
define(constant_name: 'VIEWS_PATH',    value: BASE_PATH . 'Views' . DIRECTORY_SEPARATOR);

/**
 * Modify to reflect the timezone.
 * Set the default timezone for the application.
 * This is important for all date and time functions to work correctly and consistently across the application.
 */
date_default_timezone_set(timezoneId: 'Europe/Bucharest');

try
{
    if (file_exists(filename: COMPOSER_PATH . 'autoload.php') && is_readable(filename: COMPOSER_PATH . 'autoload.php')) 
    {
        require_once COMPOSER_PATH . 'autoload.php';
        
        // Initialize the database connection
        $database = \Database\Database::getInstance();
        $database->connect(path: DATABASE_PATH);

        // Set error handler
        //set_error_handler("GamerHelpDesk\Exception\GamerHelpDeskException::handleError");

        /**
         * Initialize the router
         */
        $router = \GamerHelpDesk\Http\Router\Router::getInstance();

        // Set the base path for the router
        $router->basePath = "/sites/gamerhelpdesk-v2/www";

        /**
         * Ads routes to router
         */
        // Add attribute routes to the router
        $router->addAttributeRoute(routes: ["Stream\\Stream"]);

        // Add named routes to the router
        $router->addNamedRoute(verb: "GET", route: "/tools", method: "Tools\\Tools::index");

        // Dispatch the request to the appropriate route handler
        $router->dispatch();
    } 
    else
    {
        throw new \RuntimeException(message: "Autoloader not found or not readable. Please run 'composer install' to generate the autoloader and install dependencies first and/or ensure the file is readable.");
    }
}
 catch (\Throwable $e) 
{
    if($e->getCode() == 404)
    {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        echo "<h1 style='color: #721c24;'>Page not found!</h1>";
        echo "<br>";
        echo "<hr>";
        echo "<br>";
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;'><pre>";
        echo "Error: " . $e->getMessage();
        echo "<br>";
        echo "Code: " . $e->getCode();
        echo "<br>";
        echo "<hr>";
        echo "<br>";
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;'><pre>";
        echo "This page was not found. Please check the URL and try again.";
        echo "<br>";
        echo "</pre></div>";
        echo "<br>";
        echo "<hr>";
        echo "<br>";
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;'><pre>";
        echo "If you think this is a mistake, please contact the site administrator.";
        echo "<br>";
        echo "</pre></div>";
        echo "<br>";
        echo "<hr>";
    }
    else
    {
        header("HTTP/1.1 500 Internal Server Error");
        header("Status: 500 Internal Server Error");
        echo "<h1 style='color: #721c24;'>An error occurred!</h1>";
        echo "<br>";
        echo "<hr>";
        echo "<br>";
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;'><pre>";
        echo "Error: " . $e->getMessage();
        echo "<br>";
        echo "Code: " . $e->getCode();
        echo "<br>";
        echo "Type: " . get_class(object: $e);
        echo "<br>";
        echo "Message: " . $e->getMessage();
        echo "<br>";
        echo "File: " . $e->getFile();
        echo "<br>";
        echo "Line: " . $e->getLine();
        echo "<br>";
        echo "<hr>";
        echo "<br>";
        echo "Trace: <br>" . $e->getTraceAsString();
        echo "<br>";
        echo "<br></pre>";
        echo "</div>";
        exit(1);
    }
}
finally
{
    // Close the database connection
    $database->disconnect();

    // Restore the error handler
    //restore_error_handler();
}