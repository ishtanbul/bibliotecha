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
    // include("/var/www/html/bibliotecha/components/select.php");
    include("/var/www/html/bibliotecha/components/select.php");
    $authors = Database::get_all_authors();
   
    $add_title_author_multiselect = new SelectComponent(
        "add-title-author-multiselect",
        ["author-multiselect"],
        "author_id",
        $authors,
        null,
        "name",
        "id",
        true,
        5
    );

    ?>

    <form id="add-title-form" action="/submit">
        <div id="add-settings-1" class="action-settings">
            <label for="add-title-form" class="title-label">Enter book title: </label>
            <input type="text" class="title-field" name="title">
            <label for="add-title-form" class="author-label">Enter author: </label>
            <?php echo $add_title_author_multiselect->render_component(); ?>
        </div>
        <input value="insert_title" name="action" hidden>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</body>

</html>