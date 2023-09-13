<!DOCTYPE html>
<html>


<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Page Title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
   <?php 
   include("/var/www/html/bibliotecha/partials/common-lib.php");
   include("/var/www/html/bibliotecha/components/select.php");
   ?>
</head>

<body>
    <a class="btn btn-primary" href="/new%20title">Insert Books</a>
    <a class="btn btn-primary" href="/filter%20titles">Filter Titles </a>
<?php

    include("/var/www/html/bibliotecha/partials/modal.php");
    include("/var/www/html/bibliotecha/partials/table.php");
    ?>


</body>

</html>