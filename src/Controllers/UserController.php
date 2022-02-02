<?php

namespace BShare\Webservice\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rakit\Validation\Validator;

use BShare\Webservice\Models\User;
use BShare\Webservice\Error\ValidateException;
use BShare\Webservice\Database\Database;

/**
 * Controllers for user
 */
class UserController
{
    /**
     * Sign user with information of json
     */
    public function create(Request $req, Response $res, array $args)
    {
        // Init variables
        $user = new User();
        $validator = new Validator;
        $data = (array) json_decode(file_get_contents('php://input'));

        // Validate json data
        $validation = $validator->validate($data, [
            "name" => "required",
            "password" => "required|min:8",
            "email" => "required|email"
        ]);

        if ($validation->fails()) {
            throw new ValidateException(($validation->errors())->firstOfAll());
        }

        // Insert json data into user object and register user
        // Insert the result into body response
        $res->getBody()->write(json_encode($user->registerUser(json_decode(file_get_contents('php://input')))));

        // Return response
        return $res;
    }

    // Show one user with informatin of json
    public function show(Request $req, Response $res, array $args)
    {
        $user = new User();
        $emailOurName = $args['data'];

        $res->getBody()->write(json_encode($user->getUsers(
            "nm_email = '$emailOurName' OR nm_usuario = '$emailOurName'"
        )[0]));

        return $res;
    }

    /**
     * Show all users
     */
    public function showAll(Request $req, Response $res, array $args)
    {
        $user = new User();

        $res->getBody()->write(json_encode($user->getUsers()));

        return $res;
    }

    /**
     * Login user with email
     */
    public function logIn(Request $req, Response $res, array $args)
    {
        // Init variables
        $validator = new Validator;
        $data = (array) json_decode(file_get_contents('php://input'));
        $user = new User();

        $validation = $validator->validate($data, [
            "nameOurEmail" => "required",
            "password" => "required|min:8"
        ]);

        if ($validation->fails()) {
            throw new ValidateException(($validation->errors())->firstOfAll());
        }

        $userData = $user->getUsers(
            "nm_usuario = '" . $data['nameOurEmail'] . "' OR nm_email = '" . $data['nameOurEmail'] . "' AND cd_senha = '" . hash(
                "sha256",
                $data['password']
            ) . "'"
        );

        $res->getBody()->write(json_encode(($userData != null) ? $userData[0] : null));

        return $res;
    }
}
