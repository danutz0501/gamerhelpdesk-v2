<?php
/*
 * File: SingletonTrait.php
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

namespace GamerHelpDesk\Util\SingletonTrait;

/**
 * SingletonTrait
 * A trait that implements the singleton design pattern.
 * It provides a static method to get the single instance of the class that uses this trait.
 * It also prevents cloning and unserialization of the instance.
 * Classes that use this trait should have a private or protected constructor to prevent direct instantiation.
 * 
 * @package GamerHelpDesk\Util\SingletonTrait
 * @version 1.0.0
 */
trait SingletonTrait
{
    /**
     * The single instance of the class.
     * @var static|null
     */
    private static ?self $instance = null;

    /**
     * Get the single instance of the class.
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Prevent cloning of the instance.
     */
    private function __clone() {}
}