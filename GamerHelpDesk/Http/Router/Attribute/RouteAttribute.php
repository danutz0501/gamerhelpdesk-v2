<?php
/**
 * File: RouteAttribute.php
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

namespace GamerHelpDesk\Http\Router\Attribute;

use GamerHelpDesk\Http\Router\Router;
#[\Attribute(\Attribute::TARGET_METHOD|\Attribute::IS_REPEATABLE)]

/**
 * RouteAttribute
 * This attribute is used to define a route for a controller method.
 * It will automatically register the route with the Router class when the attribute is instantiated.
 * @package GamerHelpDesk\Http\Router
 * @version 1.0.0
 */
class RouteAttribute
{
    /**
     * RouteAttribute constructor.
     * This attribute is used to define a route for a controller method.
     * It will automatically register the route with the Router class when the attribute is instantiated.
     * @param string $verb The HTTP verb (GET, POST, PUT, DELETE, etc.)
     * @param string $route The route path (e.g. "/users/{id}")
     * @param array|null $middleware An optional array of middleware to be applied to the route.
     */
    public function __construct(
        public string $verb,
        public string $route,
        public ?array $middleware = null
    ) 
    {
    }
}