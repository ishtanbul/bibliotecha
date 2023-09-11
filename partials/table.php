<?php



function render_book_row($book_row)
{
    $id = $book_row["id"];
    $title = $book_row["title"];
    $author = $book_row["name"];
    $authors = Database::get_all_authors();
    $authors_multiselect_component = new SelectComponent(
        "author-multiselect-$id",
        ["author-multiselect", "hidden"],
        "authors",
        $authors,
        array("id" => $id, "name" => $author),
        "name",
        "id",
        true,
        2
    );
    //
    return " 
            <tr>
                <th scope='row'>$id</th>
                <td>
                    <span class='title-text' id='title-text-$id'>$title</span>
                    <input type='text' class='title-field hidden' id='title-field-$id' name='title' placeholder='Book title goes here...' value='{$book_row["title"]}'>
                </td>
                <td>
                    <span class='author-text' id='author-text-$id'>$author</span>
                    {$authors_multiselect_component->render_component()}
                </td>
                <td> 
                    <a id='delete-$id' class='btn' href='#' onclick='openDeleteModal($id); return false;'> 
                        <i class='fa fa-trash trash-can-color' aria-hidden='true'></i>
                    </a>
                    <a id='edit-$id'class='btn'> 
                        <i class='fa fa-pen-to-square edit-color' aria-hidden='true' onclick='switchToEdit($id)'></i>
                    </a>
                    <a id='confirm-edit-$id' class='btn hidden'> 
                        <i class='fa fa-check edit-color' aria-hidden='true' onclick='confirmChanges($id)'></i>
                    </a>
                    <a  id='cancel-edit-$id' class='btn hidden'> 
                        <i class='fa fa-xmark edit-color' aria-hidden='true' onclick='switchOutOfEdit($id)'></i>
                    </a>
                </td>
            </tr>
    ";
}



?>



<script>
    let openDeleteModal = function(id) {}
    let deleteBookRow = function() {}
    let switchToEdit = function(id) {}
    let switchOutOfEdit = function(id) {}

    $(document).ready(function() {

        let deleteId = -1

        $("#delete-book-modal").modal({
            keyboard: true,
            show: false
        })

        deleteBookRow = function() {
            location.assign(`/submit?action=delete_title&title_id=${deleteId}`);
        }

        openDeleteModal = function(id) {
            deleteId = id
            $("#delete-book-modal").modal("show")

        }

        function hideElements(...els) {
            for(let el of els) {
                el.addClass("hidden")
            }
        }

        function showElements(...els) {
            for(let el of els) {
                el.removeClass("hidden")
            }
        }

        confirmChanges = function(id) {
            let title_id = id
            let author_id = $(`#author-multiselect-${id}`).find(":selected").val()
            let title = $(`#title-field-${id}`).val()
           // console.log(JSON.stringify(author_id))
            location.assign(`/submit?action=update_title&title_id=${id}&author_id=${author_id}&title=${title}`)
        }

        switchToEdit = function(id) {
            let title_field_el = $(`#title-field-${id}`)
            let title_text_el = $(`#title-text-${id}`)

            let author_text_el = $(`#author-text-${id}`)
            let author_multiselect_el = $(`#author-multiselect-${id}`)

            let edit_button_el = $(`#edit-${id}`);
            let confirm_edit_button_el = $(`#confirm-edit-${id}`);
            let cancel_edit_button_el = $(`#cancel-edit-${id}`);
            let delete_button_el = $(`#delete-${id}`);


            hideElements(title_text_el, author_text_el, edit_button_el, delete_button_el)
            showElements(title_field_el, author_multiselect_el, confirm_edit_button_el, cancel_edit_button_el)
        }

        switchOutOfEdit = function(id) {
            let title_field_el = $(`#title-field-${id}`)
            let title_text_el = $(`#title-text-${id}`)

            let author_text_el = $(`#author-text-${id}`)
            let author_multiselect_el = $(`#author-multiselect-${id}`)

            let edit_button_el = $(`#edit-${id}`);
            let confirm_edit_button_el = $(`#confirm-edit-${id}`);
            let cancel_edit_button_el = $(`#cancel-edit-${id}`);
            let delete_button_el = $(`#delete-${id}`);

            showElements(title_text_el, author_text_el, edit_button_el, delete_button_el)
            hideElements(title_field_el, author_multiselect_el, confirm_edit_button_el, cancel_edit_button_el)
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
                <th scope="col">Options</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach (($all_books = Database::get_all_books()) as $book_row) {
                echo render_book_row($book_row);
                // echo json_encode($book_row);
                
            }
        
            ?>

        </tbody>

    </table>
</div>



<?php


?>