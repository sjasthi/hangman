<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('nav.php');
include("db_credentials.php");

function processLogin() {
    if (isset($_POST['email']) and isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (checkEmail($email)) {
            $db_pass = getPassword($email);

            if (strcmp($password, $db_pass) == 0) {
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
    $conn = dbConnect();

    $resultlog = mysqli_query($conn,"SELECT * FROM user_info WHERE user_email = '$email'") or die(mysqli_error($conn));
    if (mysqli_num_rows($resultlog) > 0){
        $email_exist = true;
    }

    return $email_exist;
}

# returns the password given an email
function getPassword($email) {
    $conn = dbConnect();

    $resultlog = mysqli_query($conn,"SELECT * FROM user_info WHERE user_email = '$email'") or die(mysqli_error($conn));
    while($row = mysqli_fetch_array($resultlog)){
        $password = $row['user_password'];
    }

    return $password;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hangman Game</title>

    <script src="jquery/jquery.js"></script>
    <link rel="stylesheet" href="./css/hangman_style.css">

    <link rel="stylesheet" type="text/css" href="DataTables/DataTables-1.12.1/css/jquery.dataTables.css"/>
    <script type="text/javascript" src="DataTables/DataTables-1.12.1/js/jquery.dataTables.js"></script>

</head>



<body>
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