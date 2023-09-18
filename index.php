<?php

header("Content-Type: text/html");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE");

include(__DIR__ . "/api/database.php");

if (isset($_ENV["PROD_MODE"])) {
    Database::connect(__DIR__ . "/api/prod.env");
} else {
    Database::connect(__DIR__ . "/api/dev.env");
}

ini_set('display_errors', 1);
error_reporting(E_ALL);


$request_uri = $_SERVER["REQUEST_URI"];
$query_str_pos = strpos($request_uri, "?");
$query_str_pos = $query_str_pos ? $query_str_pos : strlen($request_uri);
$sanitised_request_uri = substr($request_uri, 0,  $query_str_pos);


// echo json_encode($_SERVER);

switch ($sanitised_request_uri) {
    case "/":
        include(__DIR__ . "/pages/home.php");

        break;

    case "/submit":
        include(__DIR__ . "/pages/actions.php");
        break;

    case "/new%20title":
        include(__DIR__ . "/pages/new-title.php");
        break;

    case "/filter%20titles":
        include(__DIR__ . "/pages/filter-titles.php");
        break;

    case "/edit%20title":
        include(__DIR__ . "/pages/edit-title.php");
        break;

    default:
        include(__DIR__ . "/pages/error.php");
}
