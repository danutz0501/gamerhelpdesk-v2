<?php
/**
 * File: Registry.php
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

namespace GamerHelpDesk\Util\Registry;

use GamerHelpDesk\Util\Collection\Collection;
use GamerHelpDesk\Exception\{
    GamerHelpDeskException,
    GamerHelpDeskExceptionEnum
};

class Registry extends Collection
{
    /**
     * The collection
     * @var array
     */
    protected static array $collection = new Collection();

    /**
     * Adds a value to the collection.
     * 
     * @param string $key
     * The key to store the value under.
     * @param mixed $value
     * The value to store.
     * @throws GamerHelpDeskException If the key is empty.
     */
    public function add(string $key, mixed $value): void
    {
        if(empty($key)) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Key cannot be empty.");
        }
        $this->collection[$key] = $value;
    }

    /**
     * Gets a value from the collection.
     * @param string $key
     * The key to get the value from.
     * @return mixed
     * The value stored under the key.
     * @throws GamerHelpDeskException If the key is empty.
     */
    public function get(string $key): mixed
    {
        if(empty($key)) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Key cannot be empty.");
        }
        return $this->collection[$key] ?? null;
    }

    /**
     * Removes a value from the collection.
     * @param string $key
     * The key to remove the value from.
     * @throws GamerHelpDeskException If the key is empty.
     */
    public function remove(string $key): void
    {
        if(empty($key)) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Key cannot be empty.");
        }

        unset($this->collection[$key]);
    }

    /**
     * Checks if a key exists in the collection.
     * @param string $key
     * The key to check.
     * @return bool
     * True if the key exists, false otherwise.
     * @throws GamerHelpDeskException If the key is empty.
     */
    public function has(string $key): bool
    {
        if(empty($key)) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Key cannot be empty.");
        }
        return isset($this->collection[$key]);
    }

    /**
     * Clears the collection.
     */
    public function clear(): void
    {
        $this->collection = [];
    }

    /**
     * Converts the collection to an array.
     * @return array
     */
    public function toArray(): array
    {
        return iterator_to_array(iterator: $this->collection);
    }
}