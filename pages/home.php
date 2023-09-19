<!DOCTYPE html>
<html>


<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Page Title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <?php
    header("Content-Type: text/html");
    header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE");
    include("/var/www/html/bibliotecha/partials/common-lib.php");
    include("/var/www/html/bibliotecha/components/select.php");

    ?>
</head>

<body>
    <a class="btn btn-primary" href="/new-title">Insert Books</a>
    <a class="btn btn-primary" href="/filter-titles">Filter Titles </a>
    <?php

    include("/var/www/html/bibliotecha/partials/modal.php");
    include("/var/www/html/bibliotecha/partials/table.php");
    ?>


</body>

</html>