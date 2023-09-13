<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $url = "/";
    if ($_GET["action"] == "insert_title") {
        validate_field("author_id", "Invalid author ID", 400);
        validate_field("genre_id", "Invalid genre ID", 400);
        validate_field("title", "Invalid title", 400);
        $author_id_list = $_GET["author_id"];
        $genre_id_list = $_GET["genre_id"];

        $title = $_GET["title"];
        Database::insert_title($author_id_list, $genre_id_list, $title);
    }

    if ($_GET["action"] == "update_title") {
        validate_field("author_id", "Invalid author ID", 400);
        validate_field("genre_id", "Invalid genre ID", 400);
        validate_field("title", "Invalid title", 400);
        validate_field("title_id", "Invalid title ID", 400);

        $author_id_list = $_GET["author_id"];
        $genre_id_list = $_GET["genre_id"];
        $title = $_GET["title"];
        $title_id = $_GET["title_id"];
        Database::update_genre($genre_id_list, $title_id);
        Database::update_title($title, $title_id);
        Database::update_author($author_id_list, $title_id);
    }

    if ($_GET["action"] == "delete_title") {
        validate_field("title_id", "Invalid title ID", 400);
        $title_id = $_GET["title_id"];

        Database::delete_title($title_id);
    }

    if ($_GET["action"] == "filter_titles") {
        validate_field("author_id", "Invalid author ID", 400);
        $author_id = $_GET["author_id"];
        $url = "/?filter&author_id={$author_id}";
    }

    $_GET = array();
    header("Location: $url");
}

function validate_field($index, $message, $status)
{
    if (!array_key_exists($index, $_GET) || empty($_GET[$index])) {
        error_log($message);
        $url = "/error?status=$status&message=$message";
        header("Location: $url");
        exit(1);
    }
}
