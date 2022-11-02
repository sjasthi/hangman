<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return $_SESSION['loggedIn'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hangman</title>
    <link rel="stylesheet" href="./css/mainStylesheet.css">
</head>

<body>


    <nav>

        <ul class="nav-list">
            <a href="index.php">
                <li class="logo">
                    <img src="./images/app_logo.png" alt="">
                    <br>
                    Hangman
                </li>

            </a>


            <a href="index.php">
                <li class="hangman-logo">
                    <img src="./images/home.png" alt="">
                    <br>
                    Home
                </li>

            </a>

            <?php
            if (!isLoggedIn()) {
            ?>
            <a href="login.php">
                <li>
                    <img src="./images/login.png" alt="">
                    <br>
                    Login
                </li>
            </a>
            <?php
            } else {
            ?>
            <a href="logout.php">
                <li>
                    <img src="./images/login.png" alt="">
                    <br>
                    Logout
                </li>
            </a>

            <?php } ?>


            <a href="phrases.php">
                <li>
                    <img src="./images/login.png" alt="">
                    <br>
                    Phrases
                </li>

            </a>

        </ul>
    </nav>

   

</body>

</html>