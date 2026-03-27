<?php
/**
 * File: View.php
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

namespace GamerHelpDesk\View;

use GamerHelpDesk\Exception\{
    GamerHelpDeskException,
    GamerHelpDeskExceptionEnum
};

/**
 * View class
 * This class is responsible for rendering views in the GamerHelpDesk application.
 * It takes a view file name and an array of data, and renders the view with the provided data.
 * The view file should be a PHP file that can use the provided data to generate the output.
 * The class provides methods to assign data to the view and to render the view.
 * If the view file is not found or is not readable, a GamerHelpDeskException is thrown with the FILE_SYSTEM_EXCEPTION code.
 * The rendered content is returned as a string, or false on failure.
 * 
 * @package GamerHelpDesk\View
 * @version 1.0.0
 */
class View
{
    protected string $viewName;

    
    /**
     * Constructs a new View object.
     *
     * This constructor takes two parameters, the name of the view file and an array of data to be used in the view.
     * The view file name should not include the .php extension, as it is added automatically.
     * If the view file is not found or is not readable, a GamerHelpDeskException is thrown with the FILE_SYSTEM_EXCEPTION code.
     *
     * @param string $viewName The name of the view file.
     * @param array $data An array of data to be used in the view.
     *
     * @throws GamerHelpDeskException If the view file is not found or is not readable.
     */
    public function __construct(string $viewName = "", protected array $data = [])
    {
        $viewName .= ".php";
        if(file_exists (filename: $viewName) && is_readable(filename: $viewName))
        {
            $this->viewName = $viewName;
        }
        else
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "View file not found or not readable: $viewName");
        }
    }

    
    /**
     * Assigns a value to a key in the view data array.
     *
     * This method takes two parameters, a key and a value.
     * The key is the name of the variable to be used in the view, and the value is the value of the variable.
     *
     * @param string $key The name of the variable to be used in the view.
     * @param mixed $value The value of the variable.
     */
    public function assign(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    
    /**
     * Renders the view and returns the rendered content as a string.
     *
     * This method first extracts the data array into the current scope.
     * Then it starts the output buffering and includes the view file.
     * Finally, it returns the rendered content as a string.
     *
     * If the output buffering fails, it returns false.
     *
     * @return bool|string The rendered content as a string or false on failure.
     */
    public function render(): bool|string
    {
        extract(array:$this->data);
        ob_start();
        include $this->viewName;
        return ob_get_clean();
    }
}