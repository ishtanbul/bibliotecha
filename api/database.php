<?php


class Database
{

    private static string $user;
    private static string $password;
    private static string $host;
    private static string $database;

    private static mysqli $connection;


    public  function __construct()
    {
    }


    public static function connect(string $path_to_env_file)
    {
        if (!preg_match("/.env/i", $path_to_env_file)) {
            error_log("Expecting an environment variable file");
            exit(1);
        }
        $env = parse_ini_file($path_to_env_file);
        self::$user = $env["MYSQL_USER"];
        self::$password = $env["MYSQL_PASSWORD"];
        self::$host = $env["MYSQL_HOST"];
        self::$database = $env["MYSQL_DATABASE"];
        self::$connection =  new mysqli(self::$host, self::$user, self::$password, self::$database);
    }

    public static function get_all_books()
    {

        $statement = "SELECT `titles`.`id`,`titles`.`title`, `authors`.`name`, authors_join_titles.authors_id, authors_join_titles.titles_id FROM 
    ((`authors` INNER JOIN `authors_join_titles` ON `authors_join_titles`.`authors_id` = `authors`.`id`) 
    INNER JOIN `titles` ON `authors_join_titles`.`titles_id` = `titles`.`id`) ORDER BY titles.id ASC";

        $result = self::$connection->query($statement);

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


    public static function insert_title($author_id, $title)
    {

        if (empty($author_id) || empty($title) || !is_numeric($author_id)) {
            echo ("Error in insertion");
            exit(1);
        }


        $statement = self::$connection->prepare("INSERT INTO titles VALUES (NULL, ?)");
        $bind_success = $statement->bind_param("s", $title);
        if (!$bind_success) {
            echo ("Failure in binding");
            exit(1);
        }
        $statement->execute();
        $title_id = $statement->insert_id;
        self::insert_title_author_assoc($author_id, $title_id);
    }

    private static function insert_title_author_assoc($author_id, $title_id)
    {
        if (empty($author_id) || empty($title_id)) {
            error_log("Empty parameter");
            exit(1);
        }
        if (!is_numeric($author_id) || !is_numeric($title_id)) {
            error_log("Parameters expected to be numeric");
            exit(1);
        }

        $statement = self::$connection->prepare("INSERT INTO authors_join_titles VALUES (?, ?)");
        $bind_success = $statement->bind_param("ii", $author_id, $title_id);
        if (!$bind_success) {
            echo ("Failure in binding");
            exit(1);
        }
        $statement->execute();
    }


    public static function delete_title($id)
    {
        if (empty($id) || !is_numeric($id)) {
            echo ("Error in ID");
            exit(1);
        }

        $statement = self::$connection->prepare("DELETE FROM titles WHERE titles.id = ?");
        $bind_success = $statement->bind_param("i", $id);
        if (!$bind_success) {
            echo ("Failure in binding");
            exit(1);
        }
        $statement->execute();
    }

    public static function update_author($author_id, $title_id)
    {
        if (empty($author_id) || !is_numeric($author_id)) {
            return;
        }

        if (empty($title_id) || !is_numeric($title_id)) {
            return;
        }

        $statement = self::$connection->prepare("UPDATE authors_join_titles SET authors_join_titles.authors_id = ? WHERE authors_join_titles.titles_id = ?");
        $bind_success = $statement->bind_param("ii", $author_id, $title_id);
        if (!$bind_success) {
            echo ("Failure in binding");
            exit(1);
        }
        $statement->execute();
    }

    public static function update_title($title, $title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            return;
        }

        if (empty($title)) {
            return;
        }

        $statement = self::$connection->prepare("UPDATE titles SET titles.title = ? WHERE titles.id = ?");
        $bind_success = $statement->bind_param("si", $title, $title_id);
        if (!$bind_success) {
            echo ("Failure in binding");
            exit(1);
        }
        $statement->execute();
    }


    public static function get_all_authors()
    {

        $statement = "SELECT * FROM authors";
        $result = self::$connection->query($statement);

        if (!$result) {
            echo ("No data");
            exit(1);
        }

        $all_authors = [];

        while ($row = $result->fetch_array()) {
            $all_authors[] = array("id" => $row["id"], "name" => $row["name"]);
        }
        return $all_authors;
    }
}
