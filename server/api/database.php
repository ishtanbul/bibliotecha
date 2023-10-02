<?php

enum ColumnType
{
    case AUTHOR;
    case GENRE;

    case TITLE;

    case ERROR;
}

enum QueryCommand
{
    case IS;
    case IS_NOT;
    case STARTS_WITH;
    case ENDS_WITH;

    case ERROR;
}

enum BooleanOperator
{
    case AND;
    case OR;

    case DEFAULT;

    case ERROR;
}
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
            self::redirect_to_error_page(500, "Expecting an environment variable file");
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
            self::redirect_to_error_page(500, "Invalid Title ID");
        }

        $statement = self::$connection->prepare("SELECT titles.title FROM titles WHERE titles.id = ?");

        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            self::redirect_to_error_page(500, "Failure in binding");
        }
        $statement->execute();

        $statement->bind_result($title);

        $statement->fetch();

        $statement->free_result();

        return array("id" => $title_id, "title" => $title, "authors" => self::get_authors($title_id), "genre" => self::get_genre($title_id));
    }

    public static function get_authors($title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            self::redirect_to_error_page(500, "Invalid Title ID");
        }

        $statement = self::$connection->prepare("SELECT authors.id, authors.name FROM authors INNER JOIN authors_join_titles 
        ON authors_join_titles.authors_id = authors.id INNER JOIN titles 
        ON authors_join_titles.titles_id = titles.id WHERE titles.id = ?");

        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            self::redirect_to_error_page(500, "Failure in binding");
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
            return -1;
        }

        $statement = self::$connection->prepare("SELECT genre.id, genre.genre FROM genre INNER JOIN genre_join_titles 
        ON genre_join_titles.genre_id = genre.id INNER JOIN titles 
        ON genre_join_titles.titles_id = titles.id WHERE titles.id = ?");

        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            self::redirect_to_error_page(500, "Failure in binding");
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
            return -1;
        }


        $statement = self::$connection->prepare("INSERT INTO titles VALUES (NULL, ?)");
        $bind_success = $statement->bind_param("s", $title);
        if (!$bind_success) {
            return -1;
        }
        $statement->execute();
        $title_id = $statement->insert_id;
        $statement->free_result();
        self::insert_title_author_assoc($author_id_list, $title_id);
        self::insert_title_genre_assoc($genre_id_list, $title_id);
        return 0;
    }

    private static function insert_title_genre_assoc(array $genre_id_list, $title_id)
    {
        if (empty($genre_id_list) || empty($title_id)) {
            self::redirect_to_error_page(500, "Invalid empty parameters");
        }

        foreach ($genre_id_list as $genre_id) {
            $statement = self::$connection->prepare("INSERT INTO genre_join_titles VALUES (?, ?)");
            $bind_success = $statement->bind_param("ii", $title_id, $genre_id);
            if (!$bind_success) {
                self::redirect_to_error_page(500, "Failure in binding");
            }

            $statement->execute();
            $statement->free_result();
        }
    }

    private static function insert_title_author_assoc(array $author_id_list, $title_id)
    {
        if (empty($author_id_list) || empty($title_id)) {
            return -1;
        }

        foreach ($author_id_list as $author_id) {
            $statement = self::$connection->prepare("INSERT INTO authors_join_titles VALUES (?, ?)");
            $bind_success = $statement->bind_param("ii", $author_id, $title_id);
            if (!$bind_success) {
                return -1;
            }
            $statement->execute();
            $statement->free_result();
        }
    }


    public static function delete_title($id)
    {
        if (empty($id) || !is_numeric($id)) {
            self::redirect_to_error_page(500, "Invalid Title ID");
        }

        $statement = self::$connection->prepare("DELETE FROM titles WHERE titles.id = ?");
        $bind_success = $statement->bind_param("i", $id);
        if (!$bind_success) {
            self::redirect_to_error_page(500, "Failure in binding");
        }
        $statement->execute();
    }

    private static function delete_title_author_assoc($title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            self::redirect_to_error_page(500, "Invalid Title ID");
        }

        $statement = self::$connection->prepare("DELETE FROM authors_join_titles WHERE authors_join_titles.titles_id = ?");
        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            self::redirect_to_error_page(500, "Failure in binding");
        }
        $statement->execute();
    }

    public static function update_author(array $author_id_list, $title_id)
    {

        if (empty($title_id) || !is_numeric($title_id)) {
            return -1;
        }
        self::delete_title_author_assoc($title_id);
        self::insert_title_author_assoc($author_id_list, $title_id);
        return 0;
    }

    private static function delete_title_genre_assoc($title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            self::redirect_to_error_page(500, "Invalid Title ID");
        }

        $statement = self::$connection->prepare("DELETE FROM genre_join_titles WHERE genre_join_titles.titles_id = ?");
        $bind_success = $statement->bind_param("i", $title_id);
        if (!$bind_success) {
            self::redirect_to_error_page(500, "Failure in binding");
        }
        $statement->execute();
    }

    public static function update_genre(array $genre_id_list, $title_id)
    {

        if (empty($title_id) || !is_numeric($title_id)) {
            return -1;
        }
        self::delete_title_genre_assoc($title_id);
        self::insert_title_genre_assoc($genre_id_list, $title_id);
        return 0;
    }

    public static function update_title($title, $title_id)
    {
        if (empty($title_id) || !is_numeric($title_id)) {
            return -1;
        }

        if (empty($title)) {
            return -1;
        }

        $statement = self::$connection->prepare("UPDATE titles SET titles.title = ? WHERE titles.id = ?");
        $bind_success = $statement->bind_param("si", $title, $title_id);
        if (!$bind_success) {
            self::redirect_to_error_page(500, "Failure in binding");
        }
        $statement->execute();
        return 0;
    }

    public static function filter_titles_by_author($author_id)
    {
        $title_id_list = self::get_title_id_by_author($author_id);

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


    private static function get_title_id_by_author($author_id)
    {
        if (empty($author_id) || !is_numeric($author_id)) {
            self::redirect_to_error_page(500, "Failure in binding");
        }

        $statement = self::$connection->prepare("SELECT authors_join_titles.titles_id FROM authors_join_titles WHERE authors_join_titles.authors_id = ?");

        $bind_success = $statement->bind_param("i", $author_id);
        if (!$bind_success) {
            self::redirect_to_error_page(500, "Failure in binding");
        }
        $statement->execute();

        $title_id_list = $statement->get_result();

        $statement->free_result();

        return $title_id_list;
    }

    public static function filter_titles_by_genre($genre_id)
    {
        $title_id_list = self::get_title_id_by_genre($genre_id);

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

    private static function get_title_id_by_genre($genre_id)
    {
        if (empty($genre_id) || !is_numeric($genre_id)) {
            self::redirect_to_error_page(500, "Failure in binding");
        }

        $statement = self::$connection->prepare("SELECT genre_join_titles.titles_id FROM genre_join_titles WHERE genre_join_titles.genre_id = ?");

        $bind_success = $statement->bind_param("i", $genre_id);
        if (!$bind_success) {
            self::redirect_to_error_page(500, "Failure in binding");
        }
        $statement->execute();

        $title_id_list = $statement->get_result();

        $statement->free_result();

        return $title_id_list;
    }


    private static function extract_boolean_operator($filter_rule)
    {
        if (!key_exists("boolean_operator", $filter_rule)) {
            return BooleanOperator::ERROR;
        }

        switch ($filter_rule["boolean_operator"]) {
            case "AND":
                return BooleanOperator::AND;

            case "OR":
                return BooleanOperator::OR;

            case "DEFAULT":
                return BooleanOperator::DEFAULT;

            default:
                return BooleanOperator::ERROR;
        }
    }

    private static function extract_column_type($filter_rule)
    {
        if (!key_exists("column_type", $filter_rule)) {
            return ColumnType::ERROR;
        }

        switch ($filter_rule["column_type"]) {
            case "AUTHORS":
                return ColumnType::AUTHOR;

            case "TITLE":
                return ColumnType::TITLE;

            case "GENRE":
                return ColumnType::GENRE;

            default:
                return ColumnType::ERROR;
        }
    }

    private static function extract_query_command($filter_rule)
    {
        if (!key_exists("query_command", $filter_rule)) {
            return ColumnType::ERROR;
        }

        switch ($filter_rule["query_command"]) {
            case "IS":
                return QueryCommand::IS;

            case "IS_NOT":
                return QueryCommand::IS_NOT;

            case "STARTS_WITH":
                return QueryCommand::STARTS_WITH;

            case "ENDS_WITH":
                return QueryCommand::ENDS_WITH;

            default:
                return ColumnType::ERROR;
        }
    }

    private static function extract_query_value($filter_rule)
    {
        if (!key_exists("query_value", $filter_rule)) {
            return null;
        }

        $query_value = $filter_rule["query_value"];

        if (empty($query_value)) {
            return null;
        }

        return $query_value;
    }

    public static function filter_with_rule_set($filter_rule_set)
    {
        $result = [];
        foreach ($filter_rule_set as $filter_rule) {
            $boolean_operator = self::extract_boolean_operator($filter_rule);
            $column_type = self::extract_column_type($filter_rule);
            $query_command = self::extract_query_command($filter_rule);
            $query_value = self::extract_query_value($filter_rule);
            if ($boolean_operator == BooleanOperator::ERROR || $column_type == ColumnType::ERROR || $query_command == QueryCommand::ERROR) {
                return -1;
            }


            $title_ids = self::perform_filter_query($column_type, $query_command, $query_value);
            if ($boolean_operator == BooleanOperator::DEFAULT) {
                $result = $title_ids;
            } else if ($boolean_operator == BooleanOperator::AND) {
                $result = self::and($result, $title_ids);
            } else if ($boolean_operator == BooleanOperator::OR) {
                $result = self::or($result, $title_ids);
            }
        }

        $result = array_unique($result);
        $filtered_titles = [];
        foreach ($result as $title_id) {
            $filtered_titles[] = self::get_title($title_id);
        }

        return $filtered_titles;
    }

    private static function perform_filter_query($column_type, $query_command, $query_value)
    {
        $statement = self::construct_filter_query($column_type, $query_command, $query_value);

        $result = self::$connection->query($statement);

        if(!$result) {
            return [];
        }
        $title_ids = [];

        while ($row = $result->fetch_assoc()) {
            $title_ids[] = $row["id"];
        }

        return $title_ids;
    }

    private static function construct_filter_query($column_type, $query_command, $query_value)
    {
        $query_value = self::$connection->escape_string($query_value);
        $sql = null;
        switch ($column_type) {
            case ColumnType::TITLE:
                $sql = "SELECT titles.id FROM titles WHERE titles.title %s";
                break;

            case ColumnType::AUTHOR:
                $sql = "SELECT titles.id FROM titles INNER JOIN authors_join_titles ON titles.id = authors_join_titles.titles_id INNER JOIN authors ON authors_join_titles.authors_id = authors.id WHERE authors.name %s";
                break;

            case ColumnType::GENRE:
                $sql = "SELECT titles.id FROM titles INNER JOIN genre_join_titles ON titles.id = genre_join_titles.titles_id INNER JOIN genre ON genre_join_titles.genre_id = genre.id WHERE genre.genre %s";

                break;

            default:
        }

        $condition = null;
        switch ($query_command) {
            case QueryCommand::IS:
                $condition = "= '$query_value'";
                break;
            case QueryCommand::IS_NOT:
                $condition = "<> '$query_value'";
                break;
            case QueryCommand::STARTS_WITH:
                $condition = "LIKE '$query_value%'";
                break;
            case QueryCommand::ENDS_WITH:
                $condition = "LIKE '%$query_value'";
                break;
                default:
        }

        $statement = sprintf($sql, $condition);
        return $statement;
    }


    public static function get_all_authors()
    {

        $statement = "SELECT * FROM authors";
        $result = self::$connection->query($statement);

        if (!$result) {
            self::redirect_to_error_page(500, "No author data in database");
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
            self::redirect_to_error_page(500, "No genre data");
        }

        $all_genre = [];

        while ($row = $result->fetch_array()) {
            $all_genre[] = array("id" => $row["id"], "genre" => $row["genre"]);
        }
        return $all_genre;
    }

    private static function redirect_to_error_page($http_status_code, $message)
    {
        error_log($message);
        $url = "/error?status=$http_status_code&message=$message";
        header("Location: $url");
        exit(1);
    }


    private static function and(array $a, array $b): array
    {
        return array_intersect($a, $b);
    }


    private static function or(array $a, array $b): array
    {
        return array_unique(array_merge($a, $b));
    }
}
