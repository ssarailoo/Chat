<?php

class DatabaseConnection
{
    private static DatabaseConnection|null $instance=null;
    private PDO $pdo;

    private function __construct()
    {

        $this->pdo = new PDO("mysql:host=localhost;dbname=chat_project", 'root');
       $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    }

    public static function getInstance(): ?DatabaseConnection
    {
        if (static::$instance == null)
            static::$instance = new DatabaseConnection();

        return static::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}