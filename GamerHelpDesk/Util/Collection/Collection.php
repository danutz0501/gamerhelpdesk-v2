<?php
/**
 * File: Collection.php
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

namespace GamerHelpDesk\Util\Collection;

use IteratorAggregate;
use JsonSerializable;
use Countable;
use Traversable;
use ArrayIterator;

/**
 * Collection class
 * A simple collection class that implements IteratorAggregate, Countable, and JsonSerializable interfaces.
 * It provides basic functionalities to manage a collection of items.
 * @package GamerHelpDesk\Util\Collection
 * @version 1.0.0
 */
class Collection implements IteratorAggregate, Countable ,JsonSerializable
{

    /**
     * Constructor to initialize the collection with an optional array of items.
     * @param array $collection
     */
    public function __construct(protected array $collection = []) {}

    /**
     * Implementing iterator aggregate to allow foreach iteration over the collection.
     * @return ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->collection);
    }

    /**
     * Implementing countable to allow counting the number of items in the collection.
     * @return int
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * Implementing json serializable to allow easy JSON encoding of the collection.
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->collection;
    }

    /**
     * Check if the collection is empty.
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->collection);
    }
}