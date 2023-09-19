<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Page Title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <?php include("/var/www/html/bibliotecha/partials/common-lib.php"); ?>
</head>

<body>
    <?php

    if (!array_key_exists("id", $_GET) && !is_numeric($_GET["id"])) {
        exit(1);
    }

    $title_id = $_GET["id"];
    // include("/var/www/html/bibliotecha/components/select.php");
    include("/var/www/html/bibliotecha/components/select.php");
    $authors = Database::get_all_authors();
    $selected_authors = Database::get_authors($title_id);
    $edit_title_author_multiselect = new SelectComponent(
        "edit-title-author-multiselect",
        ["author-multiselect"],
        "author_id[]",
        $authors,
        $selected_authors,
        "name",
        "id",
        true,
        5
    );

    $genre = Database::get_all_genre();
    $selected_genre = Database::get_genre($title_id);

    $edit_title_genre_multiselect = new SelectComponent("edit-title-genre-multiselect", ["genre-multiselect"], "genre_id[]", $genre, $selected_genre, "genre", "id", true, 5);

    $title = Database::get_title($title_id);

    ?>

    <form id="edit-title-form" method="post" action="/api/update/title">
        <div id="add-settings-1" class="action-settings">
        <input name="title_id" value="<?php echo $title_id ?>" hidden>
            <label for="edit-title-form" class="title-label">Enter book title: </label>
            <input type="text" class="title-field" name="title" value="<?php echo $title; ?>">
            <label for="edit-title-form" class="author-label">Enter author: </label>
            <?php echo $edit_title_author_multiselect->render_component(); ?>
            <label for="edit-title-form" class="genre-label">Enter genre: </label>
            <?php echo $edit_title_genre_multiselect->render_component(); ?>
        </div>
        <input value="update_title" name="action" hidden>
        <button type="submit" class="btn btn-primary">Apply Changes</button>
    </form>
</body>

</html>