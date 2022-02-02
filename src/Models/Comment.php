<?php

namespace BShare\Webservice\Models;

use BShare\Webservice\Database\Database;

class Comment
{
    /**
     * Comment UUID code
     * @var string
     */
    private string $code;

    /**
     * Comment filename
     * @var string
     */
    private string $comment;

    public function registerComment(array $comments, string $projectCode)
    {
        // Init variables
        $database = new Database();

        // Register comments into database
        $database->setTable('tb_comentario');
        foreach ($comments as $comment) {
            $database->insert([
                "ds_comentario" => $comment,
                "cd_projeto" => $projectCode
            ], ["uuid" => true, "colunmName" => "cd_comentario"]);
        }
    }
}
