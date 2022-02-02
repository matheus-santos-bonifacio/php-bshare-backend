<?php

namespace BShare\Webservice\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rakit\Validation\Validator;

use BShare\Webservice\Error\ValidateException;
use BShare\Webservice\Models\Paste;

class PasteController
{
    public function create(Request $req, Response $res, array $args)
    {
        // Init variables
        $validator = new Validator;
        $paste = new Paste();
        $data = json_decode(file_get_contents('php://input'));

        $validation = $validator->validate((array) $data, [
            "path" => "required",
            "user" => "required"
        ]);

        if ($validation->fails()) {
            throw new ValidateException(($validation->errors())->firstOfAll());
        }

        $res->getBody()->write(json_encode($paste->registerPaste($data)));
        return $res;
    }
}
