<?php
/**
 * File: RouteCollection.php
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

use GamerHelpDesk\Util\Collection\Collection;

/**
 * RouteCollection class
 * This class extends the Collection class to manage a collection of routes.
 * It provides methods to add, remove, and retrieve routes.
 * 
 * @package GamerHelpDesk\Http\Router\RouteCollection
 * @version 1.0.0
 */
class RouteCollection extends Collection
{

    /**
     * Adds a route to the collection.
     * This method appends a new route to the collection.
     * @param \GamerHelpDesk\Http\Router\Route $route The route to be added to the collection.
     * @return void
     */
    public function addRoute(Route $route): void
    {
        $this->collection[] = $route;
    }
}