<?php
/**
 * File: Database.php
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

namespace Database;

use GamerHelpDesk\Util\SingletonTrait\SingletonTrait;

class Database extends \GamerHelpDesk\Database\Database
{
    // use SingletonTrait;
    use SingletonTrait;
    /**
     * Connect to the database
     * @param string $path The path to the SQLite database file
     * @return void
     */
    
    public function connect($path): void
    {
        parent::connect($path.'/gamerhelpdesk.db');
    }

    /**
     * Creates the necessary tables for the database.
     * Tables created are: image, video, audio, title, and slideshow.
     * If the tables already exist, this function does nothing.
     * @return void
     */
    public function createTables(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS image 
        (id INT PRIMARY KEY, 
        filename VARCHAR(255) NOT NULL, 
        path VARCHAR(255) NOT NULL,
        hash VARCHAR(255) NOT NULL);"
        );
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS video 
        (id INT PRIMARY KEY, 
        filename VARCHAR(255) NOT NULL, 
        path VARCHAR(255) NOT NULL,
        hash VARCHAR(255) NOT NULL);"
        );
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS audio 
        (id INT PRIMARY KEY, 
        filename VARCHAR(255) NOT NULL, 
        path VARCHAR(255) NOT NULL,
        hash VARCHAR(255) NOT NULL);"
        );
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS title 
        (id INT PRIMARY KEY, 
        title VARCHAR(255) NOT NULL, 
        description VARCHAR(255) NOT NULL);"
        );  
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS slideshow 
        (id INT PRIMARY KEY, 
        title VARCHAR(255) NOT NULL, 
        effect VARCHAR(255) NOT NULL,
        images VARCHAR(255) NOT NULL);"
        );    
    }
    
}