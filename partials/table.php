<?php

function join_author_names(array $authors)
{
    $author_names = [];
    foreach ($authors as $author) {
        $author_names[] = $author["name"];
    }
    return implode(", ", $author_names);
}

function join_genre(array $genre)
{
    $genre_list = [];
    foreach ($genre as $genre_item) {
        $genre_list[] = $genre_item["genre"];
    }
    return implode(", ", $genre_list);
}

function render_book_row($book_row)
{
    $id = $book_row["id"];
    $title = $book_row["title"];

    $authors = $book_row["authors"];
    $author_names = join_author_names($authors);

    $genre = $book_row["genre"];
    $selected_genre = join_genre($genre);





    return " 
            <tr>
                <th scope='row'>$id</th>
                <td>
                    <span class='title-text' id='title-text-$id'>$title</span>
                    <input type='text' class='title-field hidden' id='title-field-$id' name='title' placeholder='Book title goes here...' value='$title'>
                </td>
                <td>
                    <span class='author-text' id='author-text-$id'>$author_names
                    </span>
                   
                </td>
                <td>
                    <span class='text' id='genre-text-$id'>$selected_genre</span>
               
                </td>
                <td> 
                    <a id='delete-$id' class='btn' href='#' onclick='openDeleteModal($id); return false;'> 
                        <i class='fa fa-trash trash-can-color' aria-hidden='true'></i>
                    </a>
                    <a id='edit-$id'class='btn' href='/edit-title?id=$id'> 
                        <i class='fa fa-pen-to-square edit-color' aria-hidden='true'></i>
                    </a>
               
                   
                </td>
            </tr>
    ";
}



?>



<script>
    let openDeleteModal = function(id) {}
    let deleteBookRow = function() {}


    $(document).ready(function() {

        let deleteId = -1

        $("#delete-book-modal").modal({
            keyboard: true,
            show: false
        })

        deleteBookRow = function() {
            location.assign(`/api/delete/title/${deleteId}`);
        }

        openDeleteModal = function(id) {
            deleteId = id
            $("#delete-book-modal").modal("show")

        }

    });
</script>

<div class="book-master">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Author</th>
                <th scope="col">Genre</th>
                <th scope="col">Options</th>
            </tr>
        </thead>
        <tbody>
            <?php
       
            foreach ($all_books = $data as $book_row) {
                echo render_book_row($book_row);
                // echo json_encode($book_row);

            }


            ?>

        </tbody>

    </table>
</div>



<?php



function validate_in_get_req(string $index)
{
    if (array_key_exists($index, $_GET) && !empty($_GET[$index]))
        return $_GET[$index];
    return false;
}
?>