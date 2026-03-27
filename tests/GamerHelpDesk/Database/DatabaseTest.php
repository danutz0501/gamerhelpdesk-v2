<?php

use GamerHelpDesk\Database\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{

    public $db;
    public function setUp(): void
    {
        $this->db = new class extends Database {
            protected function createTables(): void {}
        };
    }
    public function testConnect()
    {
        $database = $this->db;
        $database->connect(path: ":memory:");
        $this->assertNotNull($database->pdo);
    }

    public function testDisconnect()
    {
        $database = $this->db;
        $database->connect(path: ":memory:");
        $database->disconnect();
        $this->assertNull($database->pdo);
    }

}