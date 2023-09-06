<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Page Title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <?php
    header("Content-Type: text/html");
    include("/var/www/html/bibliotecha/partials/common-lib.php");
    include("/var/www/html/bibliotecha/api/books/books.php");
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    ?>
</head>

<body>

    <?php

    include("/var/www/html/bibliotecha/partials/form.php");

    function render_book_row($book_row)
    {
        return " 
    <tr>
    <th scope='row'>{$book_row["id"]}</th>
    <td>{$book_row["title"]}</td>
    <td>{$book_row["author"]}</td>

</tr>
    ";
    }

    ?>

    <div class="book-master">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Author</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach (($all_books = get_all_books()) as $book_row) {
                    echo render_book_row($book_row);
                }
                ?>
            </tbody>
        </table>
    </div>



</body>

</html>