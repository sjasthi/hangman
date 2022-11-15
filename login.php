<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("db_credentials.php");

function processLogin() {
    if (isset($_POST['email']) and isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (checkEmail($email)) {

            if (checkPassword($email, $password)) {
                $_SESSION['loggedIn'] = true;
                header("Location: hangman.php");
            } else {
                echo "<p>Incorrect email/password. Please try again.</p></br>";
            }

        } else {
            echo "<p>Incorrect email/password. Please try again.</p></br>";
        }
    }
}

# checks database to see if email exists
function checkEmail($email) {
    $email_exist = false;
    $apiReturn = file_get_contents('https://wpapi.telugupuzzles.com/api/userExists.php?email=' . $email);

    $parsedApiReturn = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $apiReturn), true );

    if($parsedApiReturn["data"]){
        $email_exist = true;
    }

    return $email_exist;
}

# returns the password given an email
function checkPassword($email, $password) {
    $login_status = false;
    $apiReturn = file_get_contents("https://wpapi.telugupuzzles.com/api/ws_login.php?email=".$email."&password=".$password);
  
    $parsedApiReturn = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $apiReturn), true );

        if($parsedApiReturn["data"]){
        $login_status = true;
        }

    return $login_status;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <script src="jquery/jquery.js"></script>
    <link rel="stylesheet" href="./css/hangman_style.css">

    <link rel="stylesheet" type="text/css" href="DataTables/DataTables-1.12.1/css/jquery.dataTables.css"/>
    <script type="text/javascript" src="DataTables/DataTables-1.12.1/js/jquery.dataTables.js"></script>

</head>



<body>
    <?php
    include('nav.php');
    ?>
    <div class="login">
        <h1>Log In</h1>
        <form action="login.php" method="post" autocomplete="off">
            <div class="field_wrap">
                <input id="email_field" type="email" name="email" placeholder="Email...." required style="width:200px">
                <p class="required">* </p>
            </div>
            <div class="field_wrap">
                <input id="password_field" type="password" name="password" placeholder="Password..." required style="width:200px">
                <p class="required">* </p>
            </div>
            <input id="login_submit_button" type="submit" value="Submit" name="submit">
        </form>
        <div id="login_message">
            <?php
            processLogin();
            ?>
        </div>
    </div>
</body>