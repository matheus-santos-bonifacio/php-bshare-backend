<?php

namespace BShare\Webservice;

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Psr\Log\LoggerInterface;

use BShare\Webservice\Controllers\UserController;
use BShare\Webservice\Controllers\ProjectController;
use BShare\Webservice\Controllers\StaticFileController;
use BShare\Webservice\Controllers\PasteController;

use BShare\Webservice\Error\ValidateException;

// require __DIR__ . "/headers.php";
require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();


$app->addRoutingMiddleware();

// Define Custom Error Handler
$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    ?LoggerInterface $logger = null
) use ($app) {
    $logger != null ? $logger->error($exception->getMessage()) : null;

    error_log(print_r(["error" => $exception->getMessage(), "file" => $exception->getFile(), "line" => $exception->getLine()], true));

    $response = $app->getResponseFactory()->createResponse();
    return $response->withStatus(500);
};


$errorMiddleware = $app->addErrorMiddleware(false, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

// User routes
$app->get('/user/{data}', UserController::class . ':show');
$app->get('/users', UserController::class . ':showAll');
$app->post('/user/logIn', UserController::class . ':logIn');
$app->post('/user', UserController::class . ':create');

// Project routes
$app->get('/projects', ProjectController::class . ':showAll');
$app->get('/project/{projectCode}', ProjectController::class . ':show');
$app->get('/user/project/{userCode}', ProjectController::class . ':showAllUserProjects');
$app->post('/project', ProjectController::class . ':create');
$app->post('/project/comment', ProjectController::class . ':createComment');

// Paste routes
$app->post('/paste', PasteController::class . ':create');

// Static files
$app->get('/upload/img/{img}', StaticFileController::class . ':showImage');
$app->get('/upload/video/{video}', StaticFileController::class . ':showVideo');
$app->get('/upload/project/{project}', StaticFileController::class . ':showProject');

$app->run();

// phpinfo();
