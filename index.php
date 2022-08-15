<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$RequestMethod = $_SERVER['REQUEST_METHOD'];
$url = substr($_SERVER['REQUEST_URI'], 1);
$params = explode('/', $url);


if ($RequestMethod === 'POST') {
    require_once 'Controllers/PostController.php';

    $controller = new PostController;

    $value = false;
    if (method_exists($controller, $params[0])) {
        $value = (new PostController)->{$params[0]}($_POST);
    }

    echo json_encode($value);
    exit;
}

if ($RequestMethod === 'GET') {
    require_once 'Controllers/GetController.php';

    $controller = new GetController;

    $value = false;

    if (method_exists($controller, $params[0])) {
        $value = (new GetController)->{$params[0]}($params[1]);
    }

    echo json_encode($value);
    exit;
}
?>