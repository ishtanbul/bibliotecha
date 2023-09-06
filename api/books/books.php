
<?php
header("Content-Type: text/html");
ini_set('display_errors', 1);
error_reporting(E_ALL);


function get_all_books()
{
    include("/var/www/html/bibliotecha/api/conn.php");
    $statement = "SELECT * FROM books";
    $result = $connection->query($statement);

    if (!$result) {
        echo ("No data");
        exit(1);
    }

    $all_books = [];
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $all_books[$i++] = $row;
    }

    return $all_books;
}


function insert_book($author, $title)
{

    if (empty($author) || empty($title)) {
        echo ("Error in insertion");
        exit(1);
    }

    include("/var/www/html/bibliotecha/api/conn.php");
    $statement = $connection->prepare("INSERT INTO books (`id`, `title`, `author`) VALUES (NULL, ?, ?)");
    $bind_success = $statement->bind_param("ss", $title, $author);
    if (!$bind_success) {
        echo ("Failure in binding");
        exit(1);
    }
    $statement->execute();
}


function delete_book($id) {
    if(empty($id) || !is_numeric($id)) {
        echo("Error in ID");
        exit(1);
    }
include("/var/www/html/bibliotecha/api/conn.php");
    $statement = $connection->prepare("DELETE FROM books WHERE books.id = ?");
    $bind_success = $statement->bind_param("i", $id);
    if (!$bind_success) {
        echo ("Failure in binding");
        exit(1);
    }
    $statement->execute();
}


