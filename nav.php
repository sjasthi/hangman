<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    if (isset($_SESSION['loggedIn'])) {
        return $_SESSION['loggedIn'];
    }
    return false;
}
?>
<nav>
    <link rel="stylesheet" href="./css/mainStylesheet.css">

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


    <?php     
        // if user roll is "USER" show phrases tab (this needs to change to admin later)
        $apiReturn = file_get_contents('https://wpapi.telugupuzzles.com/api/getRole.php?email=' . $_SESSION['userEmail']);
        $parsedApiReturn = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $apiReturn), true );

        if($parsedApiReturn["data"]=="USER"){
            echo'<a href="phrases.php">';
                echo'<li>';
                    echo'<img src="./images/key.png" alt="">';
                    echo'<br>';
                    echo'Phrases';
                echo'</li>';
            echo'</a>';
        }
    ?>


        <a href="customPhrases.php">
            <li>
                <img src="./images/key.png" alt="">
                <br>
                Custom Phrases
            </li>
        </a>
    </ul>
</nav>
