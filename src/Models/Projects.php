<?php

namespace BShare\Webservice\Models;

use BShare\Webservice\Database\Database;
use BShare\Webservice\Functions\CommonFunctions;

use PDO;

/**
 * Class from the project
 */
class Projects
{
    /**
     * UUID key project code
     * @var string
     */
    private string $code;

    /**
     * Project title
     * @var string
     */
    private string $title;

    /**
     * Project creation date
     * @var string
     */
    private string $creationDate;

    /**
     * Project description file name
     * @var string
     */
    private string $description;

    /**
     * Project age classification
     * @var string
     */
    private string $ageClassification;

    /**
     * Project price
     * @var float
     */
    private float $price;

    /**
     * Project category
     * @var string|Category
     */
    private $category;

    /**
     * Project description
     * @var array|string
     */
    private $project;

    /**
     * If project was active
     */
    private bool $active;

    /**
     * Project authors
     * @var string
     */
    private string $author;

    /**
     * Projects comment
     * @var array
     */
    private array $comment = [];

    /**
     * Project images
     * @var array<Image>
     */
    private array $images = [];

    /**
     * Project main images
     * @var array|string
     */
    private $mainImage = null;

    /**
     * Project videos
     */
    private array $videos = [];

    public function __get($key)
    {
        return $this->$key;
    }

    /**
     * Register the project for a user, through of json data
     * @param object $data
     * @return array Project object converted for the array
     */
    public function registerProject(object $data)
    {
        // Started the variable
        $image = new Image();
        $video = new Video();
        $category = new Category();
        $database = new Database();
        $this->code = CommonFunctions::UUIDV4();

        // Setting project create date
        $this->creationDate = str_replace("-", "/", date('Y-m-d'));

        // Insert the information of json data into object
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        // Verify the category and insert data category into object project
        $this->category = $category->getCodeCategory($this->category);

        // Insert project into server
        $this->project = CommonFunctions::insertFileIntoServer($this->project, rand(100, 999), 'project');

        // If exist insert main image into server
        if ($this->mainImage != []) {
            $this->mainImage = CommonFunctions::insertFileIntoServer($this->mainImage, rand(100, 999), 'img');
        }

        // Create project into database
        $database->setTable('tb_projeto');
        $database->insert([
            "cd_projeto" => $this->code,
            "nm_titulo" => $this->title,
            "ds_projeto" => $this->description,
            "im_principal" => $this->mainImage,
            "qt_classificacao_idade" => $this->ageClassification,
            "dt_criacao" => $this->creationDate,
            "vl_preco_projeto" => $this->price,
            "im_projeto" => $this->project,
            "ic_ativo" => true,
            "cd_categoria" => $this->category['code']
        ]);

        // Create image into database
        // Return last image ID
        // Save file into documents
        $image->registerImage($this->images, $this->code);

        // Create video into database
        // Return last video ID
        // Save file into documents
        $video->registerVideo($this->videos, $this->code);

        // Return sucess status and data array
        return $this->getAll();
    }

    /**
     * Show all projects
     * @return array Projects object converted for the array
     */
    public function selectAllProjects(string $where = null, string $order = null, string $limit = null)
    {
        return array_map(function (Projects $result) {
            // Init the variables
            $image = new Image();
            $category = new Category();

            $result->images = $image->getImages("cd_projeto = '$result->code'");

            return $result->getAll();
        }, (new Database('tb_projeto'))->select([
            "cd_projeto AS code",
            "nm_titulo AS title",
            "ds_projeto AS description",
            "im_principal AS mainImage",
            "qt_classificacao_idade AS ageClassification",
            "dt_criacao AS creationDate",
            "vl_preco_projeto AS price",
            "im_projeto AS project",
            "ic_ativo AS active",
            "cd_categoria AS category"
        ], $where, $order, $limit)->fetchAll(PDO::FETCH_CLASS, self::class));
    }

    public function comment(string $comment, string $projectCode)
    {
        // Init variables
        $database = new Database();

        $database->insert([
            "cd_comentario" => CommonFunctions::UUIDV4(),
            "ds_comentario" => $comment,
            "cd_projeto" => $projectCode
        ]);
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
