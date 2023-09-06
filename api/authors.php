<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

function get_all_authors()
{
    include("/var/www/html/bibliotecha/api/conn.php");
    $statement = "SELECT `name` FROM authors";
    $result = $connection->query($statement);

    if (!$result) {
        echo ("No data");
        exit(1);
    }

    $all_authors = [];
    $i = 0;
    while ($row = $result->fetch_array()) {
        $all_authors[$i++] = $row["name"];
    }
    return $all_authors;
}
