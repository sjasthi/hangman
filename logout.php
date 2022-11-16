<?php
session_start();

$_SESSION['loggedIn'] = false;
$_SESSION['userEmail'] = '';
header("Location: hangman.php");
?>