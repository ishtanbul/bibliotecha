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

    include("/var/www/html/bibliotecha/components/select.php");
    $authors = Database::get_all_authors();

    $filter_title_author_multiselect = new SelectComponent(
        "filter-title-author-multiselect",
        ["author-multiselect"],
        "author_id",
        $authors,
        null,
        "name",
        "id",
        false,
        5
    );

    $filter_genre_select = new SelectComponent("filter-genre-select", null, "genre_id", Database::get_all_genre(), null, "genre", "id", false, 5);

    ?>

    <form id="filter-title-form" action="/submit">
        <div id="filter-settings" class="action-settings">
            <label for="filter-title-form" class="author-label">Enter author: </label>
            <input type="checkbox" id="author-required" name="author_required" unchecked />
            <?php echo $filter_title_author_multiselect->render_component(); ?>
            <label for="filter-title-form" class="genre-label">Enter genre: </label>
            <select id="filter-rule-option" name="option">
                <option value="default" default></option>
                <option value="and">AND</option>
                <option value="or">OR</option>
            </select>
            <input type="checkbox" id="genre-required" name="genre_required" unchecked />
            <?php echo $filter_genre_select->render_component(); ?>
        </div>
        <input value="filter_titles" name="action" hidden>
        <button type="submit" class="btn btn-primary">Apply Filter</button>
    </form>

    <a class="btn btn-primary" href="/">Reset Filter</a>
</body>

</html>