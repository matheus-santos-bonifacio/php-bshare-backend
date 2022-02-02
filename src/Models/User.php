<?php

namespace BShare\Webservice\Models;

use BShare\Webservice\Functions\CommonFunctions;
use BShare\Webservice\Database\Database;

use PDO;

/**
 * Class from the user
 */
class User
{
    /**
     * Key user code
     * @var string
     */
    private string $code;

    /**
     * User name
     * @var string
     */
    private string $name;

    /**
     * User password
     * @var string
     */
    private string $password;

    /**
     * User email
     * @var string
     */
    private string $email;

    /**
     * File name from the user front cover
     * @var string|null
     */
    private $frontCover = null;

    /**
     * User created projects
     * @var array<Projects>
     */
    private array $createdProjects = [];

    /**
     * Projects acquired from the user
     * @var array<Projects>
     */
    private array $acquiredProjects = [];

    /**
     * Pastes from the user
     * @var array<Paste>
     */
    private array $paste = [];

    /**
     * User card
     * @var Card ( code => value, lastChange => value, projects => value )
     */
    private Card $card;

    /**
     * Getting value of object outside class
     * @param string
     */
    public function __get(string $key)
    {
        return $this->$key;
    }

    /**
     * Setting value of database into object
     * @param string $key
     * @param string $value
     */
    public function __set(string $key, string $value)
    {
        $this->$key = $value;
    }

    /**
     * Checks the information from database and set into object
     * @param string $proposedKey
     * @param string $key
     * @param string $value
     * @return string
     */
    public function setValueFromDatabase(string $proposedKey, string $key, string $value)
    {
        if ($key == $proposedKey) {
            return $value;
        }
    }

    /**
     * Insert the data from the converted json into the object, later insert data in database.
     * The json data comes in the following format:
     * 
     * {
     *   "name": "name",
     *   "password": "password",
     *   "email": "example@gmail.com"
     * }
     * 
     * @param object $data
     * @return array
     */
    public function registerUser(object $data): array
    {
        $this->card = new Card();
        $databaseObject = new Database();

        // Generete key code
        $this->code = CommonFunctions::UUIDV4();

        // Inserting data from object into object
        $this->setAll($data);

        // Data encryption
        $this->password = $this->encryption($this->password);

        // Insert data from object into database
        // Create user card into database
        // Recuperate ID
        $this->card->registerCard();

        // Create user into database
        $databaseObject->setTable('tb_usuario');
        $databaseObject->insert([
            "cd_usuario" => $this->code,
            "im_capa" => $this->frontCover
        ]);

        // Insert user data into database
        $databaseObject->setTable('tb_dados_usuario');
        $databaseObject->insert([
            "cd_usuario" => $this->code,
            "nm_usuario" => $this->name,
            "cd_senha" => $this->password,
            "nm_email" => $this->email
        ]);

        // Return sucess status and data array
        http_response_code(201);
        return $this->getAll();
    }

    /**
     * Getting all values of object outside class
     * @return array
     */
    public function getAll(): array
    {
        $data = [];
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * Getting all user(s) of database
     * @param string|null $where
     * @param string|null $order
     * @param string|null $limit
     * @return array
     */
    public function getUsers(string $where = null, string $order = null, string $limit = null)
    {
        return array_map(function ($result) {
            return $result->getAll();
        }, (new Database('tb_dados_usuario'))->select([
            "cd_usuario AS code",
            "nm_usuario AS name",
            "nm_email AS email"
        ], $where, $order, $limit)->fetchAll(PDO::FETCH_CLASS, self::class));
    }

    public function delete()
    {
    }

    /**
     * Setting data into object
     * @param $data data to insert into object
     * @return void
     */
    private function setAll(object $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Encryption password
     * @param $password password
     * @return string password hash
     */
    private function encryption(string $password)
    {
        $passwordHash = hash("sha256", $password);

        return $passwordHash;
    }
}
