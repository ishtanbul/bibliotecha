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


$app->get('/api/get/authors/{id}', function (Req $request, Res $response, array $args) {
    $id = $args["id"];
    $authors = [];
    if ($id == "*") {
        $authors = json_encode(Database::get_all_authors());
    } else if (is_numeric($id)) {
        $authors = json_encode(Database::get_authors($id));
    }

    $response->getBody()->write($authors);
    return $response->withHeader("Content-Type", "application/json");
});

$app->get('/api/get/genre/{id}', function (Req $request, Res $response, array $args) {
    $id = $args["id"];
    $genre = [];
    if ($id == "*") {
        $genre = json_encode(Database::get_all_genre());
    } else if (is_numeric($id)) {
        $genre = json_encode(Database::get_genre($id));
    }

    $response->getBody()->write($genre);
    return $response->withHeader("Content-Type", "application/json");
});

$app->post("/api/post/title", function (Req $request, Res $response) {

    $body = $request->getBody();
    $data = json_decode($body, true);

    $author_id_list = $data["author_id"];
    $genre_id_list = $data["genre_id"];
    $title = $data["title"];
    $status = Database::insert_title($author_id_list, $genre_id_list, $title);
    $message = array("status" => $status);
    $response->getBody()->write(json_encode($message));
    return $response->withHeader("Content-Type", "application/json");
});

$app->post("/api/update/title", function (Req $request, Res $response) {
    $body = $request->getBody();
    $data = json_decode($body, true);

    $author_id_list = $data["author_id"];
    $genre_id_list = $data["genre_id"];
    $title = $data["title"];
    $title_id = $data["title_id"];
    $status = Database::update_genre($genre_id_list, $title_id);
    $status = Database::update_title($title, $title_id);
    $status = Database::update_author($author_id_list, $title_id);
    $message = array("status" => $status);
    $response->getBody()->write(json_encode($message));
    return $response->withHeader("Content-Type", "application/json");
});

$app->get("/api/delete/title/{id}", function (Req $request, Res $response, array $args) {
    $id = $args["id"];
    Database::delete_title($id);
    return $response;
});


$app->post("/api/filter/title", function (Req $request, Res $response) {
    $body = $request->getBody();
    $filter_rule_set = json_decode($body, true);
    $filtered_titles = Database::filter_with_rule_set($filter_rule_set);
    $response->getBody()->write(json_encode($filtered_titles));
    return $response->withHeader("Content-Type", "application/json");
});


/*
    Frontend endpoints
*/

// $app->get("/", function (Req $request, Res $res) use ($renderer) {
//     return  $renderer->render($res, "home.php", Database::get_all_titles());
// });

// $app->get("/filtered", function (Req $request, Res $response) use ($renderer) {
//     $data = ($request->getQueryParams())["data"];
//     $data = json_decode(urldecode($data), JSON_OBJECT_AS_ARRAY);
//     return $renderer->render($response, "home.php", $data);
// });

// $app->get("/new-title", function (Req $request, Res $res) use ($renderer) {
//     return  $renderer->render($res, "new-title.php");
// });

// $app->get("/filter-titles", function (Req $request, Res $res) use ($renderer) {
//     return  $renderer->render($res, "filter-titles.php");
// });

// $app->get("/edit-title", function (Req $request, Res $res) use ($renderer) {
//     return  $renderer->render($res, "edit-title.php");
// });



$app->run();
