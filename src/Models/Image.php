<?php

namespace BShare\Webservice\Models;

use BShare\Webservice\Database\Database;
use BShare\Webservice\Functions\CommonFunctions;

use PDO;

class Image
{
    /**
     * Image UUID code
     * @var string
     */
    private string $code;

    /**
     * Image filename
     * @var string
     */
    private string $image;

    /**
     * Register imagem into the server and into the database
     * @param array $images Project images
     * @param string $codeProject Project code
     */
    public function registerImage(array &$images, string $codeProject)
    {
        // Init the variables
        $database = new Database();

        // Insert files images into server and rename file
        $images = CommonFunctions::insertFileIntoServer($images, rand(100, 999), 'img', true);

        // Insert image name into database
        $database->setTable('tb_imagem');

        foreach ($images as $image) {
            $database->insert([
                "im_imagem" => $image,
                "cd_projeto" => $codeProject
            ], ["uuid" => true, "colunmName" => "cd_imagem"]);
        }
    }

    /**
     * Getting all project images
     * @param string $projectCode Project code
     * @return array
     */
    public function getImages(string $where = null, string $order = null, string $limit = null)
    {
        return array_map(function (Image $result) {
            return $result->getAll();
        }, (new Database('tb_imagem'))->select(['cd_imagem AS code', 'im_imagem AS image'], $where, $order, $limit)->fetchAll(PDO::FETCH_CLASS, self::class));
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
