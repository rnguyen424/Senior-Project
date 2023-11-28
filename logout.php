<?php
session_start();
if (isset($_POST['logout'])) {
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        unset($_SESSION['username']);
        session_destroy();
        header("refresh:0 url=index.php");
    }
    else {
        header("refresh:0 url=index.php");
    }
}
?>