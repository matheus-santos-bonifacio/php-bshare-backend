<?php

namespace BShare\Webservice\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StaticFileController
{
    public function showImage(Request $req, Response $res, array $args)
    {
        // Getting image

        // Validate image

        // Getting file extension and change header
        $ext = pathinfo($args['img'], PATHINFO_EXTENSION);

        // Send header image
        header("Content-type: image/$ext");
        // Return image
    }

    public function showVideo()
    {
        // Getting video

        // Send header video
        // Return video
    }

    public function showProject()
    {
        // Getting project

        // Send header project
        // Return project
    }
}
