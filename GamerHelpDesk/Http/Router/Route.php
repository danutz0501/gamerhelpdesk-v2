<?php
/**
 * File: Route.php
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

namespace GamerHelpDesk\Http\Router;

/**
 * Route
 * The Route class represents a single route in the application.
 * It contains the regex to match the route, the method to call when the route is matched, and the parameters extracted from the URI when the route is matched.
 * The Route class also contains methods to verify if a given URI matches the route and to extract the parameters from the URI.
 * @package GamerHelpDesk\Http\Router
 * @version 1.0.0
 */
class Route
{
    /**
     * An array of patterns to replace in the regexToConvert property to create a valid PHP regex.
     * The keys of the array are the placeholders that can be used in the regexToConvert property, and the values are the corresponding regex patterns that will replace the placeholders.
     */
    private array $patterns = 
    [
        ":string" => "([a-z\-]+)",
        ":number" => "([\d]+)",
        ":any"    => "([\w\-]+)",
        "{"       => "(",
        "}"       => ")",
        "#"       => "?<",
        " "       => ">",
    ];

    /**
     * The method to call when the route is matched.
     * @var string
     */ 
    public protected(set) string $method
    {
        get
        {
            return $this->method;
        }
    }

    /**
     * The parameters extracted from the URI when the route is matched.
     * @var array
     */
    public protected(set) array $parameters = []
    {
        get
        {
            return $this->parameters;
        }
    }

    /**
     * The regex to match the route.
     * @var string
     */
    public protected(set) string $regex
    {
        get
        {
            return $this->regex;
        }
    }

    /**
     * The middleware`s associated with the route.
     * @var array|null
     */
    public protected(set) ?array $middleware
    {
        get
        {
            return $this->middleware;
        }
    }
    
    /**
     * Constructor to initialize the route.
     * It takes two parameters: the regex to convert, and the method to call when the route is matched.
     * It sets the method and parameters properties and calls the convertRegex method to prepare the regex for matching.
     * @param string $regexToConvert The regex to convert to a valid PHP regex.
     * @param string $method The method to call when the route is matched.
     * @param array|null $middleware The middleware`s associated with the route.
     */
    public function __construct(public string $regexToConvert, string $method, ?array $middleware = null)
    {
        $this->method = $method;
        $this->convertRegex();
        $this->middleware = $middleware;
    }

    /**
     * Verifies if the given URI matches the route's regex.
     * If the URI matches, it extracts the parameters from the matches and sets the parameters property.
     * @param string $uri The URI to verify against the route's regex.
     * @return bool If the URI matches the route's regex, true is returned. Otherwise, false is returned.
     */
    public function verify(string $uri): bool
    {
        if(preg_match(pattern: $this->regex, subject: $uri, matches: $matches))
        {
            $this->parameters = $this->extractParameters($matches);
            return true;
        }
        return false;
    }

    /**
     * Extracts the parameters from the matches array returned by preg_match.
     * The parameters are extracted from the matches array by iterating over it and selecting every third element.
     * The extracted parameters are then returned as an array.
     * @param array $matches The matches array returned by preg_match.
     * @return array The extracted parameters as an array.
     */
    private function extractParameters(array $matches): array
    {
        $temporaryArray = [];
        $matches = array_values(array: $matches);
        $numberOfParameters = count(value: $matches);
        for($i = 1; $i <= $numberOfParameters; $i++)
        {
            if($i % 3 === 0)
            {
               $temporaryArray[] = $matches[$i];
            }
        }
        return $temporaryArray;
    }

    /**
     * Converts the regexToConvert property to a valid PHP regex.
     * The method first replaces all '/' characters with '\/' to escape them.
     * Then, it replaces all the placeholders in the regexToConvert property with their corresponding regex patterns from the patterns property.
     * Finally, it assigns the modified regex to the regex property, adding the '^' and '$/' characters to the start and end, respectively, and sets the 'i' flag to make the regex case-insensitive.
     */
    private function convertRegex(): void
    {
        $this->regex = "/^".str_replace(search: "/", replace: "\/", 
        subject: str_replace(search: array_keys(array: $this->patterns), 
        replace: array_values(array: $this->patterns), subject: $this->regexToConvert))."$/i";
    }
}