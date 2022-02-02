<?php

namespace BShare\Webservice\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rakit\Validation\Validator;

use BShare\Webservice\Models\Projects;
use BShare\Webservice\Error\ValidateException;

class ProjectController
{
    public function create(Request $req, Response $res, array $args)
    {
        // Init variables
        $project = new Projects();
        $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/assets/project/';
        $validator = new Validator;
        $uploadfile = $uploaddir . basename($_FILES['project']['name']);
        $data = $_POST + $_FILES;

        $validation = $validator->validate($data, [
            "title" => "required",
            "price" => "required|numeric",
            "ageClassification" => "required|numeric",
            "category" => "required",
            "author" => "required|digits:36",
            "project" => "required|uploaded_file:0,2G,blend",
            "mainImage" => "uploaded_file:0,2G,png,jpeg",
            "image" => "uploaded_file:0,2G,png,jpeg",
        ]);

        if ($validation->fails()) {
            throw new ValidateException(($validation->errors())->firstOfAll());
        }

        // Getting request body and move to the project object
        $data = $project->registerProject((object) $data);

        // Insert data into body response
        $res->getBody()->write(json_encode($data));

        // Return object and sucess status
        return $res;
    }

    public function showAll(Request $req, Response $res, array $args)
    {
        // Init variables
        $project = new Projects();

        // Insert projects into body
        $res->getBody()->write(json_encode($project->selectAllProjects()));

        // Return response
        return $res;
    }

    public function showAllUserProjects(Request $req, Response $res, array $args)
    {
        // Init variables
        $project = new Projects();

        // Insert projects into body
        $res->getBody()->write(json_encode($project->selectAllProjects("author = '" . $args['userCode'] . "'")));

        // Return response
        return $res;
    }

    public function show(Request $req, Response $res, array $args)
    {
        // Init variables
        $project = new Projects();

        // Insert projects into body
        $res->getBody()->write(json_encode($project->selectAllProjects("code = '" .  $args['projectCode'] . "'")[0]));

        // Return response
        return $res;
    }
}
