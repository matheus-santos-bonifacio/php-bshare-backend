<?php

namespace BShare\Webservice\Models;

use BShare\Webservice\Database\Database;
use BShare\Webservice\Functions\CommonFunctions;
use Exception;

/**
 * Class from the card
 */
class Card
{
    /**
     * Card UUID key code
     * @var string
     */
    private string $code;

    /**
     * Last change in card
     * @var string('Y-m-d')
     */
    private string $lastChange;

    /**
     * Projects registeds into card
     * @var array<Projects>
     */
    private array $projects;

    /**
     * Sets the date of the last change
     */
    public function __construct()
    {
        $this->lastChange = str_replace("-", "/", date('Y-m-d'));
    }

    /**
     * Getting value of object outside object
     * @param string $key
     */
    public function __get(string $key)
    {
        return $this->$key;
    }

    /**
     * Setting projects into card, last insert into database
     * @param Project $project
     */
    public function setProject(Projects $project)
    {
        $pass = true;

        // Verify if exist project
        foreach ($this->projects as $value) {
            if ($value->code == $project->code) {
                $pass = false;
            }
        }

        if (!$pass) {
            throw new Exception(["stauts" => "error", "message" => "the project already exists in the cart"], 0);
        }

        // Insert into object
        array_push($this->projects, $project);


        // Insert into database

    }

    /**
     * Create user card into database
     * @return string
     */
    public function registerCard(): string
    {
        // Generate UUID key code
        $this->code = CommonFunctions::UUIDV4();

        // Create card into database
        $databaseObject = new Database('tb_carrinho');

        /**
         * Insert data into data and recuperate ID
         * @var string
         */
        $databaseObject->insert([
            "cd_carrinho" => $this->code,
            "dt_ultima_alteracao" => $this->lastChange
        ]);

        // Return last card ID
        return $this->code;
    }

    /**
     * Getting all values of object
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
}
