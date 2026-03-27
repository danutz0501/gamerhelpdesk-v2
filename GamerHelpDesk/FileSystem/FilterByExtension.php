<?php
/**
 * File: FilterByExtension.php
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

namespace GamerHelpDesk\FileSystem;

use RecursiveFilterIterator;
use RecursiveIterator;

/**
 * This class extends RecursiveFilterIterator to filter files based on their extensions.
 * It accepts a list of file extensions and only allows files with those extensions to be included in the iteration.
 * Directories are always accepted to allow for recursive traversal.
 *
 * @package GamerHelpDesk\FileSystem
 * @version 1.0.0
 */
class FilterByExtension extends RecursiveFilterIterator
{
    /**
     * @var array List of file extensions to filter by (e.g., ['jpg', 'png']).
     */
    protected array $extensions;

    /**
     * Constructor for the FilterByExtension class.
     *
     * @param RecursiveIterator $iterator The inner iterator to filter.
     * @param array|string $extensions A single extension or an array of extensions to filter by.
     */
    public function __construct(RecursiveIterator $iterator, array|string $extensions)
    {
        parent::__construct(iterator: $iterator);
        $this->extensions = is_array($extensions) ? array_map(callback: 'strtolower', array: $extensions) : [strtolower($extensions)];
    }

    /**
     * Determine whether the current item should be accepted by this filter.
     * If the current item is a directory, it is accepted.
     * If the current item is a file, it is accepted if its extension is in the list of extensions provided in the constructor.
     * @return bool Whether the current item should be accepted.
     */
    public function accept(): bool
    {
    
        if ($this->hasChildren()) 
        {
            return true;
        }

        return $this->current()->isFile() && 
                \in_array(needle: strtolower(string: $this->current()->getExtension()), haystack: $this->extensions);
    }

    /**
     * Returns a new instance of FilterByExtension which filters the children of the current item.
     * @return self A new instance of FilterByExtension which filters the children of the current item.
     */
    public function getChildren(): self
    {
        return new self(iterator: $this->getInnerIterator()->getChildren(), extensions: $this->extensions);
    }
}
