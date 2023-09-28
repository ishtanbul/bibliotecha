<?php

header("Content-Type: text/html");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

include(__DIR__ . "/api/database.php");

if (isset($_ENV["PROD_MODE"])) {
    Database::connect(__DIR__ . "/api/prod.env");
} else {
    Database::connect(__DIR__ . "/api/dev.env");
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

include ("api/routes.php");
