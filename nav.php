<?php
if (empty(session_id()) && !headers_sent()) {
    session_start();
}

if (!isset($_SESSION['userPrivelege'])) {
    $_SESSION['userPrivelege'] = '';
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

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
<!--- <script src="jquery/jquery.js"></script> -->
<script src="js/user.js"></script> 
<link rel="stylesheet" href="./css/hangman_style.css" />

<nav>
    <link rel="stylesheet" href="./css/mainStylesheet.css">

    <ul class="nav-list">

        <li>
            <a href="hangman.php">

                <img src="./images/app_logo.png" alt="">

            </a>
        </li>

        <li id="title">
            <a href="hangman.php"><h1>Hangman</h1></a>
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
                //$apiReturn = file_get_contents('https://wpapi.telugupuzzles.com/api/getRole.php?email=' . $_SESSION['userEmail']);
                //$parsedApiReturn = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $apiReturn), true );
                if($_SESSION['userPrivelege']=='ADMIN'){
                ?>
                    <a href="phrases.php">
                        <li>
                            Phrases
                        </li>
                    </a>

                <?php
                }
                ?>

                <?php
                if ($_SESSION['userPrivelege']=='USER' or $_SESSION['userPrivelege']=='ADMIN') {
                ?>
                    <a href="customPhrases.php">
                        <li>
                            Custom Phrases
                        </li>
                    </a>
                <?php
                }
                ?>
            </ul>
        </li>
    </ul>
</nav>