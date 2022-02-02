<?php

namespace BShare\Webservice\Database;

use BShare\Webservice\Error\DatabaseException;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Methods that deal with the database
 */
class Database
{
    /**
     * Connection host with the database
     * @var string
     */
    private string $host;

    /**
     * User name in the database
     * @var string
     */
    private string $user;

    /**
     * Database password
     * @var string
     */
    private string $password;

    /**
     * Database name
     * @var string
     */
    private string $dbName;

    /**
     * Name of the table to be manipulated
     * @var string
     */
    private $table;

    /**
     * Instance of connection with the database
     * @var PDO
     */
    private PDO $connection;

    /**
     * Setting table
     * @param string $value
     * @return void
     */
    public function setTable(string $value): void
    {
        $this->table = $value;
    }

    /**
     * Defines the table and instance the connection with the database
     * @param string|null $table
     */
    public function __construct(string $table = null)
    {
        $this->table = $table;
        $this->getAndInsertEnvVar();
        $this->setConnection();
    }

    /**
     * Getting env var of database and insert into object
     */
    private function getAndInsertEnvVar()
    {
        // Validate the env vars

        // Insert the env vars into object
        $this->host = getenv("MYSQL_HOST");
        $this->user = getenv("MYSQL_USER");
        $this->password = getenv("MYSQL_PASSWORD");
        $this->dbName = getenv("MYSQL_DB");
    }

    /**
     * Create connection with the database
     * @return void
     */
    private function setConnection(): void
    {
        try {
            $this->connection = new PDO(
                "mysql:host=$this->host;dbname=$this->dbName",
                $this->user,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            http_response_code(400);
            die(json_encode(["status" => "error", "message" => $e->getMessage()]));
        }
    }

    /**
     * Execute query into database
     * @param string $query
     * @param array|null $params
     * @return PDOStatement
     */
    public function execute(string $query, array $params = []): PDOStatement
    {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            throw new DatabaseException(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Insert data into database
     * @param array $values [ field => value ]
     * @return mixed last code
     */
    public function insert(array $values, array $uuid = ["uuid" => false, "colunmName" => null])
    {
        // Query data
        $filds = array_keys($values);
        $binds = implode(',', array_pad([], count($filds), '?'));
        $filds = implode(',', $filds);
        $colunmName = $uuid['uuid'] != false ? $uuid['colunmName'] . "," : '';
        $uuid = $uuid['uuid'] != false ? "uuid()," : '';

        // Mount query
        $query = "INSERT INTO $this->table ($colunmName$filds) VALUE ($uuid$binds)";

        // Execute the insert
        $this->execute($query, array_values($values));

        // Return last id
        return $this->connection->lastInsertId();
    }

    /**
     * Getting data from database
     * @param string|null $coluns
     * @param string|null $where
     * @param string|null $order
     * @param string|null $limit
     * @return PDOStatement
     */
    public function select(array $coluns = ['*'], string $where = null, string $order = null, string $limit = null)
    {
        // Query data
        $coluns = $coluns != ['*'] ? implode(',', $coluns) : '*';
        $where = strlen($where) ? "WHERE $where" : '';
        $order = strlen($order) ? "ORDER BY $order" : '';
        $limit = strlen($limit) ? "LIMIT $limit" : '';

        // Mount query
        $query = "SELECT $coluns FROM $this->table $where $order $limit";

        // Return result
        return $this->execute($query);
    }
}
