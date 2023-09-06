<?php
 header("Content-Type: text/html");
header('Access-Control-Allow-Origin: *');
$env = parse_ini_file("/var/www/html/bibliotecha/api/.env");
if (!$env) {
    echo "Unable to parse environment variable";
    exit(1);
}
ini_set('display_errors', 1);
error_reporting(E_ALL);
$user = $env["MYSQL_USER"];
$password = $env["MYSQL_PASSWORD"];
$host = $env["MYSQL_HOST"];
$database = $env["MYSQL_DATABASE"];
$connection = new mysqli($host, $user, $password, $database);

// if ($connection->error)
//     echo "Connection failed: " . $connection->error;
// else
//     echo "Connection successful";

