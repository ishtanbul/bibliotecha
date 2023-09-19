<?php


use Psr\Http\Message\ResponseInterface as Res;
use Psr\Http\Message\ServerRequestInterface as Req;

use Slim\Factory\AppFactory as AppFactory;
use Slim\Views\PhpRenderer as PHPRenderer;

require "vendor/autoload.php";

$app = AppFactory::create();

$renderer = new PHPRenderer("pages");

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/api/get/titles/{id}', function (Req $request, Res $response, array $args) {
    $id = $args["id"];
    $titles = [];
    if ($id == "*") {
        $titles = json_encode(Database::get_all_titles());
    } else if (is_numeric($id)) {
        $titles = json_encode(Database::get_title($id));
    }
    $response->getBody()->write($titles);
    return $response->withHeader("Content-Type", "application/json");
});

$app->post("/api/post/title", function (Req $request, Res $response) {
    $data = $request->getParsedBody();
    $author_id_list = $data["author_id"];
    $genre_id_list = $data["genre_id"];
    $title = $data["title"];
    Database::insert_title($author_id_list, $genre_id_list, $title);
    return $response->withStatus(302)->withHeader("Location", "/");
});

$app->post("/api/update/title", function (Req $request, Res $response) {
    $data = $request->getParsedBody();
    $author_id_list = $data["author_id"];
    $genre_id_list = $data["genre_id"];
    $title = $data["title"];
    $title_id = $data["title_id"];
    Database::update_genre($genre_id_list, $title_id);
    Database::update_title($title, $title_id);
    Database::update_author($author_id_list, $title_id);
    return $response->withStatus(302)->withHeader("Location", "/");
});

$app->get("/api/delete/title/{id}", function (Req $request, Res $response, array $args) {
    $id = $args["id"];
    Database::delete_title($id);
    return $response->withStatus(302)->withHeader("Location", "/");
});


$app->post("/api/filter/title", function (Req $request, Res $response) {
    $data = $request->getParsedBody();

    $result = [];
    if (array_key_exists("author_required", $data)  && array_key_exists("genre_required", $data) && array_key_exists("option", $data)) {
        $author_id = $data["author_id"];
        $genre_id = $data["genre_id"];
        $option = $data["option"];
        if ($option == "and") {
            $result = Database::filter(FilterType::AUTHOR, $author_id, FilterOption::AND, FilterType::GENRE, $genre_id);
        } else if ($option == "or") {
            $result = Database::filter(FilterType::AUTHOR, $author_id, FilterOption::OR, FilterType::GENRE, $genre_id);
        }
    } else if (array_key_exists("author_required", $data)) {
        $author_id = $data["author_id"];
        $result = Database::filter_titles_by_author($author_id);
    } else if (array_key_exists("genre_required", $data)) {
        $genre_id = $data["genre_id"];
        $result = Database::filter_titles_by_genre($genre_id);
    } else {
        $result = Database::get_all_titles();
    }
    $result = urlencode(json_encode($result));
    return  $response->withStatus(302)->withHeader("Location", "/filtered?data=$result");
});


/*
    Frontend endpoints
*/

$app->get("/", function (Req $request, Res $res) use ($renderer) {
    return  $renderer->render($res, "home.php", Database::get_all_titles());
});

$app->get("/filtered", function (Req $request, Res $response) use ($renderer) {
    $data = ($request->getQueryParams())["data"];
    $data = json_decode(urldecode($data), JSON_OBJECT_AS_ARRAY);
    return $renderer->render($response, "home.php", $data);
});

$app->get("/new-title", function (Req $request, Res $res) use ($renderer) {
    return  $renderer->render($res, "new-title.php");
});

$app->get("/filter-titles", function (Req $request, Res $res) use ($renderer) {
    return  $renderer->render($res, "filter-titles.php");
});

$app->get("/edit-title", function (Req $request, Res $res) use ($renderer) {
    return  $renderer->render($res, "edit-title.php");
});



$app->run();
