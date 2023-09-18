<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Error 404</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <?php include("/var/www/html/bibliotecha/partials/common-lib.php") ?>

</head>

<body>
    <h1><?php echo getErrorHeading(getStatusCode()) ?></h1>
    <h5 class="message"><?php echo getMessage() ?></h5>
    <a class="btn btn-primary" href="/">Return to home page</a>

</body>

</html>


<?php
function getStatusCode(): int
{
    if (array_key_exists("status", $_GET) && is_numeric($status = $_GET["status"])) {
        return intval($status);
    }
    return 404;
}
function getMessage(): string
{
    if (array_key_exists("message", $_GET) && !empty($message = $_GET["message"])) {
        return $message;
    }
    return "The requested page does not exist. Please return to home page";
}

function getErrorHeading($status)
{
    switch ($status) {
        case 400:
            return "400: Bad Request";

        case 404:
            return "404: Page not found";

        case 500:
            return "500: Internal Server Error";
        default:
            return "Unidentified Error";
    }
}

?>