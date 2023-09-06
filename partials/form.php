<?php

function helloworld()
{
    echo "hello world";
}
?>

<form id="bibliotecha-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

    <label for="bibliotecha-form" class="choose-action-label">Choose Action</label>
    <select name="chooseAction" form="bibliotecha-form" onselect="helloworld()">
        <option value="VIEW">View Book Title</option>
        <option value="ADD">Add Book Title</option>
        <option value="EDIT">Edit Book Title</option>
        <option value="DELETE">Delete Book Title</option>
    </select>

    <?php
    include(__DIR__ . "/form-partials/add.php");
    include(__DIR__ . "/form-partials/delete.php");
    ?>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST["chooseAction"] == "ADD") {
        $author = $_POST["author"];
        $title = $_POST["title"];
        insert_book($author, $title);
    }

    if ($_POST["chooseAction"] == "DELETE") {
        $id = $_POST["id"];
        delete_book($id);
    }
   
}
include("/var/www/html/bibliotecha/api/authors.php");
echo json_encode(get_all_authors());
?>

<script>
    let choose_action_select = document.getElementById("bibliotecha-form").chooseAction
    choose_action_select.onchange = () => {

    }
</script>