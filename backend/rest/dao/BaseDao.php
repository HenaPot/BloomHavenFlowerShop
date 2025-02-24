<?php
require_once dirname(__FILE__) . "/../../config.php";

/**
 * The main class for interaction with database.
 *
 * All other DAO classes should inherit this class.
 */
class BaseDao
{
    protected $connection;

    private $table;

    public function __construct($table)
    {
        $this->table = $table;
        try {
            $this->connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8;port=" . DB_PORT, DB_USER, DB_PASSWRD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            print_r($e);
            throw $e;
        }
    }

    protected function query($query, $params) {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function query_unique($query, $params) {
        $results = $this->query($query, $params);
        return reset($results);
    }

    protected function execute($query, $params) {
        $prepared_statement = $this->connection->prepare($query);
        if ($params) {
            foreach ($params as $key => $param) {
                $prepared_statement->bindValue($key, $param);
            }
        }
        $prepared_statement->execute();
        return $prepared_statement;
    }

    public function insert($table, $entity) {
        $query = "INSERT INTO {$table} (";
        // INSERT INTO patients (
        foreach ($entity as $column => $value) {
            $query .= $column . ", ";
        }
        // INSERT INTO patients (first_name, last_name,
        $query = substr($query, 0, -2);
        // INSERT INTO patients (first_name, last_name
        $query .= ") VALUES (";
        // INSERT INTO patients (first_name, last_name) VALUES (
        foreach ($entity as $column => $value) {
            $query .= ":" . $column . ", ";
        }
        // INSERT INTO patients (first_name, last_name) VALUES (:first_name, :last_name,
        $query = substr($query, 0, -2);
        // INSERT INTO patients (first_name, last_name) VALUES (:first_name, :last_name
        $query .= ")";
        // INSERT INTO patients (first_name, last_name) VALUES (:first_name, :last_name)

        $statement = $this->connection->prepare($query);
        $statement->execute($entity); // SQL injection prevention
        $entity['id'] = $this->connection->lastInsertId();
        return $entity;
   }
}