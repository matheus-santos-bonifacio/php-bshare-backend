<?php

namespace BShare\Webservice\Models;

use BShare\Webservice\Database\Database;

class Video
{
    /**
     * Video UUID code
     * @var string
     */
    private string $code;

    /**
     * Video filename
     * @var string
     */
    private string $video;

    /**
     * Register videos into the database
     * @param array $videos Project videos
     * @param string $codeProject Project code
     */
    public function registerVideo(array $videos, string $codeProject)
    {
        // Init the variables
        $database = new Database();

        // Insert video into database
        $database->setTable('tb_video');

        foreach ($videos as $video) {
            $database->insert([
                "im_video" => $video,
                "cd_projeto" => $codeProject
            ], ["uuid" => true, "colunmName" => "cd_video"]);
        }
    }
}
