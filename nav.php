<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
<script src="jquery/jquery.js"></script>
<script src="js/user.js"></script>
<link rel="stylesheet" href="./css/hangman_style.css" />


<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userEmail'])) {
    $_SESSION['userEmail'] = '';
}

function isLoggedIn()
{
    if (isset($_SESSION['loggedIn'])) {
        return $_SESSION['loggedIn'];
    }
    return false;
}
?>
<nav>
    <link rel="stylesheet" href="./css/mainStylesheet.css">

    <ul class="nav-list">

        <li>
            <a href="index.php">

                <img src="./images/app_logo.png" alt="">

            </a>
        </li>

        <li id="title">
            <h1>Hangman</h1>
        </li>


        <li>
            <?php include('stats.php') ?>

        </li>

        <li id="user">
            <img src="./images/user.png" alt="">


            <ul class="dropdown">
                <?php
                if (!isLoggedIn()) {
                ?>
                    <a href="login.php">
                        <li>
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
                <?php
                // if user roll is "USER" show phrases tab (this needs to change to admin later)
                $apiReturn = file_get_contents('https://wpapi.telugupuzzles.com/api/getRole.php?email=' . $_SESSION['userEmail']);
                $parsedApiReturn = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $apiReturn), true );
                if($parsedApiReturn["data"]=="USER"){
                ?>
                    <a href="phrases.php">
                        <li>
                            Phrases
                        </li>
                    </a>

                <?php
                }
                ?>

                <a href="customPhrases.php">
                    <li>
                        Custom Phrases
                    </li>
                </a>


            </ul>





        </li>

    </ul>
</nav>