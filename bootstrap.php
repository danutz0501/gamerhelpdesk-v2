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
 * Error reporting and display settings.
 * This configuration is crucial for development and debugging, as it ensures that all errors, warnings, and notices are reported and displayed. 
 * It also includes specific settings for the Xdebug extension, which provides enhanced debugging capabilities.
 * The error reporting level is set to E_ALL to report all types of errors, and display_errors is enabled to show errors in the output. 
 * Additionally, if Xdebug is installed, it is configured to provide detailed information about variables and stack traces, which can be invaluable for diagnosing issues during development.
 * Note: In a production environment, it is recommended to disable display_errors and log errors instead to avoid exposing sensitive information to end users. 
 * You can adjust these settings based on the environment (development vs. production) as needed.
 * 
 */
error_reporting(error_level: E_ALL);
ini_set(option: "display_errors", value: 1);
ini_set(option: "display_startup_errors", value: 1);
if(extension_loaded(extension: 'xdebug'))
{
    ini_set(option: "display_errors",                  value: 1);
    ini_set(option: "xdebug.var_display_max_depth",    value: "25");
    ini_set(option: "xdebug.var_display_max_children", value: "512");
    ini_set(option: "xdebug.var_display_max_data",     value: "2048");
    ini_set(option: "xdebug.show_exception_trace",     value: "1");
    ini_set(option: "xdebug.show_error_trace",         value: "1");
    ini_set(option: "xdebug.show_local_vars",          value: "1");
}

/**
 * Set session save path.
 * This is the directory where the session files will be stored.
 * Make sure this directory exists and is writable by the web server.
 * Setting a custom session save path can help improve security by keeping session files out of the default location, which may be more vulnerable to unauthorized access. 
 * It can also help with performance by allowing you to store session files on a faster storage medium or in a location that is optimized for session handling. 
 * Additionally, setting a custom session save path can help with organization and management of session files, especially in applications with a large number of users or sessions.
 * Comment if you want to use the default session save path defined in php.ini, but make sure to set it to a secure and writable directory if you do so.
 */
ini_set(option: "session.save_path", value: "D:\\php\\temp_php\\sessions\\");

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

        /**
         * Initialize the session
         */
        \GamerHelpDesk\Http\Session\Session::getInstance();

        /**
         * Initialize the database
         */
        $database = \Database\Database::getInstance();
        $database->connect(path: DATABASE_PATH);

        /**
         * Initialize the error handler
         */
        //set_error_handler("GamerHelpDesk\Exception\GamerHelpDeskException::handleError");

        /**
         * Initialize the router
         */
        $router = \GamerHelpDesk\Http\Router\Router::getInstance();

        /**
         * Set the base path for the router to ensure that it correctly handles requests when the application is not hosted at the root of the domain.
         */
        $router->basePath = "/sites/gamerhelpdesk-v2/www";

        /**
         * Ads routes to router
         *
         * The router supports both attribute-based routing and named routes.
         * Attribute-based routing allows you to define routes directly in your controller classes using attributes (annotations). 
         * This provides a clean and intuitive way to manage routes alongside the controller logic.
         * Named routes allow you to define routes in a centralized location (e.g., in the bootstrap file) and reference them by name throughout your application.
         * This can be useful for defining common routes that are not tied to specific controllers or actions.
         * One advantage of named routes is that they can be used in other controller for routes that need to be reused across multiple controllers.
         * @example of attribute-based routing in a controller:
         * $router->addAttributeRoute(routes: ["Namespace\\ClassName"]);
         * #[Get(route: '/show-image/{#imageNumber :number}')]
         * public function showImage(...$images)
         * {
         *    echo "Stream - Show Image number:  " . $images["imageNumber"] . "<br>";
         * }
         * @example of named route definition in the bootstrap file:
         * $router->addNamedRoute(verb: "GET", route: "/tools", method: "Tools\\Tools::index");
         */
        // Add attribute routes to the router
        $router->addAttributeRoute(routes: ["Stream\\Stream"]);

        // Add named routes to the router
        $router->addNamedRoute(verb: "GET", route: "/tools", method: "Tools\\Tools::index");

        /**
         * Dispatch the router to handle the incoming request and route it to the appropriate controller and method based on the defined routes.
         */
        $router->dispatch();
        $_SESSION['last_request_time'] = time();
    } 
    else
    {
        /**
         * Throw an exception if the autoloader is not found or not readable.
         */
        throw new \RuntimeException(message: "Autoloader not found or not readable. Please run 'composer install' to generate the autoloader and install dependencies first and/or ensure the file is readable.");
    }
}
 catch (\Throwable $e) 
{
    /**
     * Handle errors and exceptions thrown by the application.
     * This includes both expected exceptions (like 404 Not Found) and unexpected errors (like 500 Internal Server Error).
     * The error handling logic is designed to provide informative feedback to the user while also ensuring that the application fails gracefully in case of errors.
     * The error handling logic distinguishes between different types of errors (e.g., 404 Not Found vs. 500 Internal Server Error) and provides appropriate responses for each case.
     * The error handling logic also includes detailed information about the error (e.g., message, code, type, file, line, stack trace) to aid in debugging and troubleshooting.
     * The error handling logic is designed to be user-friendly and informative, while also providing enough technical details for developers to diagnose and fix issues effectively.
     * The error handling logic is also designed to be extensible and easy to modify, allowing developers to add additional error handling logic as needed.
     * The ERROR EXCEPTION is handled only if you have set the error handler to "GamerHelpDesk\Exception\GamerHelpDeskException::handleError" using set_error_handler() in the bootstrap file. 
     * If you have not set the error handler, then only uncaught exceptions will be handled by this catch block, and errors will not be converted to exceptions and will not be handled here.
     */
    if($e->getCode() == 404) // Handle 404 Not Found errors
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
    else // Handle other errors
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
    /**
     * Clean up the application.
     */
    // Close the database connection
    $database->disconnect();

    // Restore the error handler
    //restore_error_handler();
}