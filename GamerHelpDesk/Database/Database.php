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

namespace GamerHelpDesk\Database;

use PDO;
use PDOException;

/**
 * Class Database
 * 
 * This class manages the database connection and operations for the GamerHelpDesk application.
 * It uses the Singleton pattern to ensure that only one instance of the Database class exists throughout the application.
 */
abstract class Database
{
    /**
     * The PDO instance for database connection.
     * @var ?PDO
     */
    public protected(set) ?PDO $pdo
    {
        get
        {
            return $this->pdo;
        }
    }

    /**
     * Connect to the database.
     * 
     * This method will create the necessary database and tables if they do not exist.
     * 
     * @param string $path The path to the database file.
     * 
     * @throws PDOException If the database cannot be opened.
     * @throws PDOException If the database cannot be created.
     * @throws PDOException If any of the database tables cannot be created.
     */
    public function connect($path): void 
    {
        $this->pdo = new PDO(dsn: "sqlite:$path");
        $this->pdo->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);
        $this->createTables();
    }  

    /**
     * Disconnect from the database.
     */
    public function disconnect(): void 
    {
        $this->pdo = null;
    }

    abstract protected function createTables(): void;
}