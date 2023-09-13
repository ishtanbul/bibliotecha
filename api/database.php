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

    public static function get_all_titles()
    {


        $statement = self::$connection->prepare("SELECT titles.id, titles.title FROM titles");



        $statement->execute();

        $statement->bind_result($title_id, $title);

        $all_titles = [];
        $titles = $statement->get_result();
        $statement->free_result();
        while ($row = $titles->fetch_assoc()) {
            $title_id = $row["id"];
            $title = $row["title"];
            $all_titles[] = array("id" => $title_id, "title" => $title, "authors" => self::get_authors($title_id), "genre" => self::get_genre($title_id));
        }
        return $all_titles;
    }

    public static function get_title($title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            error_log("Problem with the Title ID");
            exit(1);
        }

        $statement = self::$connection->prepare("SELECT titles.title FROM titles WHERE titles.id = ?");

        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            error_log("Failure in binding");
            exit(1);
        }
        $statement->execute();

        $statement->bind_result($title);

        $statement->fetch();

        $statement->free_result();

        return $title;
    }

    public static function get_authors($title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            error_log("Problem with the Title ID");
            exit(1);
        }

        $statement = self::$connection->prepare("SELECT authors.id, authors.name FROM authors INNER JOIN authors_join_titles 
        ON authors_join_titles.authors_id = authors.id INNER JOIN titles 
        ON authors_join_titles.titles_id = titles.id WHERE titles.id = ?");

        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            error_log("Failure in binding");
            exit(1);
        }
        $authors_info = [];
        $statement->execute();

        $statement->bind_result($author_id, $author_name);

        while ($statement->fetch()) {
            $authors_info[] = array("id" => $author_id, "name" => $author_name);
        }

        $statement->free_result();

        return $authors_info;
    }

    public static function get_genre($title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            error_log("Problem with the Title ID");
            exit(1);
        }

        $statement = self::$connection->prepare("SELECT genre.id, genre.genre FROM genre INNER JOIN genre_join_titles 
        ON genre_join_titles.genre_id = genre.id INNER JOIN titles 
        ON genre_join_titles.titles_id = titles.id WHERE titles.id = ?");

        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            error_log("Failure in binding");
            exit(1);
        }
        $genre_info = [];
        $statement->execute();

        $statement->bind_result($genre_id, $genre);

        while ($statement->fetch()) {
            $genre_info[] = array("id" => $genre_id, "genre" => $genre);
        }

        $statement->free_result();

        return $genre_info;
    }


    public static function insert_title(array $author_id_list, array $genre_id_list, $title)
    {

        if (empty($author_id_list) || empty($title) || empty($genre_id_list)) {
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
        $statement->free_result();
        self::insert_title_author_assoc($author_id_list, $title_id);
        self::insert_title_genre_assoc($genre_id_list, $title_id);
    }

    private static function insert_title_genre_assoc(array $genre_id_list, $title_id)
    {
        if (empty($genre_id_list) || empty($title_id)) {
            error_log("Empty parameter");
            exit(1);
        }

        foreach ($genre_id_list as $genre_id) {
            $statement = self::$connection->prepare("INSERT INTO genre_join_titles VALUES (?, ?)");
            $bind_success = $statement->bind_param("ii", $title_id, $genre_id);
            if (!$bind_success) {
                echo ("Failure in binding");
                exit(1);
            }

            $statement->execute();
            $statement->free_result();
        }
    }

    private static function insert_title_author_assoc(array $author_id_list, $title_id)
    {
        if (empty($author_id_list) || empty($title_id)) {
            error_log("Empty parameter");
            exit(1);
        }

        foreach ($author_id_list as $author_id) {
            $statement = self::$connection->prepare("INSERT INTO authors_join_titles VALUES (?, ?)");
            $bind_success = $statement->bind_param("ii", $author_id, $title_id);
            if (!$bind_success) {
                echo ("Failure in binding");
                exit(1);
            }
            $statement->execute();
            $statement->free_result();
        }
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

    private static function delete_title_author_assoc($title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            echo ("Error in ID");
            exit(1);
        }

        $statement = self::$connection->prepare("DELETE FROM authors_join_titles WHERE authors_join_titles.titles_id = ?");
        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            echo ("Failure in binding");
            exit(1);
        }
        $statement->execute();
    }

    public static function update_author(array $author_id_list, $title_id)
    {

        if (empty($title_id) || !is_numeric($title_id)) {
            return;
        }
        self::delete_title_author_assoc($title_id);
        self::insert_title_author_assoc($author_id_list, $title_id);
    }

    private static function delete_title_genre_assoc($title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            echo ("Error in ID");
            exit(1);
        }

        $statement = self::$connection->prepare("DELETE FROM genre_join_titles WHERE genre_join_titles.titles_id = ?");
        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            echo ("Failure in binding");
            exit(1);
        }
        $statement->execute();
    }

    public static function update_genre(array $genre_id_list, $title_id)
    {

        if (empty($title_id) || !is_numeric($title_id)) {
            return;
        }
        self::delete_title_genre_assoc($title_id);
        self::insert_title_genre_assoc($genre_id_list, $title_id);
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

    public static function filter_titles_by_author($author_id)
    {
        if (empty($author_id) || !is_numeric($author_id)) {
            return;
        }

        $statement = self::$connection->prepare("SELECT authors_join_titles.titles_id FROM authors_join_titles WHERE authors_join_titles.authors_id = ?");

        $bind_success = $statement->bind_param("i", $author_id);
        if (!$bind_success) {
            echo ("Failure in binding");
            exit(1);
        }
        $statement->execute();

        $title_id_list = $statement->get_result();

        $statement->free_result();

        $filtered_titles = [];

        while ($row = $title_id_list->fetch_assoc()) {
            $title_id = $row["titles_id"];
            $title_name = self::get_title($title_id);
            $authors = self::get_authors($title_id);
            $genre = self::get_genre($title_id);
            $filtered_titles[] = array("id" => $title_id, "title" => $title_name, "authors" => $authors, "genre" => $genre);
        }
        return $filtered_titles;
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


    public static function get_all_genre()
    {

        $statement = "SELECT * FROM genre";
        $result = self::$connection->query($statement);

        if (!$result) {
            echo ("No data");
            exit(1);
        }

        $all_genre = [];

        while ($row = $result->fetch_array()) {
            $all_genre[] = array("id" => $row["id"], "genre" => $row["genre"]);
        }
        return $all_genre;
    }
}
