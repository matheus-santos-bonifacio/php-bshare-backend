<?php

namespace BShare\Webservice\Models;

use BShare\Webservice\Database\Database;
use BShare\Webservice\Functions\CommonFunctions;
use BShare\Webservice\Error\DuplicateValueException;

class Paste
{
    /**
     * Paste UUID code
     * @var string
     */
    private string $code;

    /**
     * Path name
     * @var string
     */
    private string $path;

    /**
     * Paste user
     * @var string
     */
    private string $user;

    /**
     * Card code
     * @var string|null 
     */
    private $card;

    /**
     * Register paste into database
     * @param object $data json object
     */
    public function registerPaste(object $data)
    {
        // Init variables
        $database = new Database('tb_pasta');
        $this->code = CommonFunctions::UUIDV4();

        // Getting and setiing into object the data
        $this->setAll($data);

        // Verify if exist paste
        $rows = ($database->select(['*'], "nm_caminho_pasta = '$this->path'"))->rowCount();

        if ($rows != 0) {
            throw new DuplicateValueException(["path" => "Duplicate value"]);
        }

        // Register into database
        $database->insert([
            "cd_pasta" => $this->code,
            "nm_caminho_pasta" => $this->path,
            "cd_usuario" => $this->user,
            "cd_carrinho" => $this->card
        ]);

        // Return the data
        return $this->getAll();
    }

    public function insertProjectIntoPaste()
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
}
