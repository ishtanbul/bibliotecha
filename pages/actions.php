<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  
    if ($_GET["action"] == "insert_title") {
        $author_id = $_GET["author_id"];
        $title = $_GET["title"];
        Database::insert_title($author_id, $title);
    }

    if ($_GET["action"] == "update_title") {
        $author_id = $_GET["author_id"];
        $title = $_GET["title"];
        $title_id = $_GET["title_id"];
        Database::update_title($title, $title_id);
        Database::update_author($author_id, $title_id);
    }

    if ($_GET["action"] == "delete_title") {
        $title_id = $_GET["title_id"];
        Database::delete_title($title_id);
    }
    $_GET = array();
    header("Location: /");
}

?>
