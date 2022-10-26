<?php
include('nav.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hangman Game</title>
    <link rel="stylesheet" href="./css/hangman_style.css" />
</head>

<body>
    <section class="phrase_form">
        <div class="form_title">
            <h2>Remove a Phrase </h2>
        </div>

        <div class="phrase_form_main">
            <form action="" method="POST" enctype="multipart/form-data">

                <div class=name_input>
                    <input type="number" name="id" placeholder="phrase id...." required>
                </div>
                <div class="submit-button">
                    <button type="submit" name="submit" class="add_phrase_button"> Remove phrase</button>
                </div>
                </form>
        </div>

    </section>

    <h2 class=message-output>
        <?php

            //if form is submitted
            if (isset($_POST['submit'])) {

                //get variables from form
                $id = $_POST['id'];

                if (!isset($_POST['id']) || empty(trim($_POST['id']))){
                    die("invalid name: ");
                }

                //Connection to database
                DEFINE('DB_SERVER', 'localhost');
                DEFINE('DB_NAME', 'quotes_db');
                DEFINE('DB_USER', 'root');
                DEFINE('DB_PASS', '');
                    
                $conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

                //checks connection
                if ($conn -> connect_error){
                    echo " connection failed <br>";
                    die("connection failed: " . $conn->connect_error);
                }

                if (!$conn) {
                    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
                }

                //remove quote from the quote tabl with given ID
                $sql = "DELETE FROM quote_table WHERE id = $id";
                
                //outputs text if record is removed
                if ($conn->query($sql) === TRUE){
                    echo "Phrase removed from database";
                }
                
                //if connection fails, prints error message
                else{
                    echo "error" . $sql . "<br>" . $conn->error;
                }

                //close connection
                $conn->close();
                
            }
        ?>
        
    </h2>
</body>