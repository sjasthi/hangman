<?php
if (empty(session_id()) && !headers_sent()) {
    session_start();
}

$_SESSION['loggedIn'] = false;
$_SESSION['userEmail'] = '';
header("Location: hangman.php");
?>