<?php
/**
 * File: Get.php
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
#[\Attribute(\Attribute::TARGET_METHOD|\Attribute::IS_REPEATABLE)]

/**
 * Extends RouteAttribute, representing a HTTP GET route for a controller method.
 * @package GamerHelpDesk\Http\Router\Attribute
 * @version 1.0.0
 */
class Get extends RouteAttribute 
{
    /**
     * Constructor to initialize the route.
     * It takes two parameters: the route path and the middleware associated with the route.
     * It calls the parent constructor with the verb set to 'GET'.
     * @param string $route The route path.
     * @param array|null $middleware The middleware associated with the route.
     */
    public function __construct(string $route, ?array $middleware = null)
    {
        parent::__construct(verb:'GET', route: $route, middleware: $middleware);
    }
}