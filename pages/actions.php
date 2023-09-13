<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $url = "/";
    if ($_GET["action"] == "insert_title") {
        $author_id_list = $_GET["author_id"];
        $genre_id_list = $_GET["genre_id"];
       
        $title = $_GET["title"];
        Database::insert_title($author_id_list, $genre_id_list, $title);
       
    }

    if ($_GET["action"] == "update_title") {
        $author_id_list = $_GET["author_id"];
        $genre_id_list = $_GET["genre_id"];
        $title = $_GET["title"];
        $title_id = $_GET["title_id"];
        Database::update_genre($genre_id_list, $title_id);
        Database::update_title($title, $title_id);
        Database::update_author($author_id_list, $title_id);
    }

    if ($_GET["action"] == "delete_title") {
        $title_id = $_GET["title_id"];
        Database::delete_title($title_id);
    }

    if ($_GET["action"] == "filter_titles") {
        $author_id = $_GET["author_id"];
        $url = "/?filter&author_id={$author_id}";
        }

    $_GET = array();
    header("Location: $url");
}
