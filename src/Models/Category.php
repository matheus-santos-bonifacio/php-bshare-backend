<?php

namespace BShare\Webservice\Models;

use BShare\Webservice\Database\Database;

use PDO;

class Category
{
    /**
     * Category UUID code
     * @var string
     */
    private string $code;

    /**
     * Category
     * @var string
     */
    private string $category;

    /**
     * Getting code category
     * @param string $category
     * @return string
     */
    public function getCodeCategory(string $category)
    {
        return (new Database('tb_categoria'))->select([
            "cd_categoria AS code",
            "ds_categoria AS category"
        ], "ds_categoria = '$category'")->fetchAll(PDO::FETCH_CLASS, self::class)[0]->getAll();
    }

    /**
     * Getting all category
     * @return array
     */
    public function getAll()
    {
        $data = [];
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }
}
