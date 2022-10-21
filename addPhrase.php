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
            <h2>Add a Phrase </h2>
        </div>

        <div class="phrase_form_main">
            <form action="" method="POST" enctype="multipart/form-data">

                <div class=name_input>
                    <input type="text" name="name" placeholder="Author name...." required>
                </div>

                <div class=topic_input>
                    <input type="text" name="topic" placeholder="Topic...." required>
                </div>

                <div class="phrase-input">
                    <input type="text" name="phrase" placeholder="phrase...." required>
                </div>

                <div class="submit-button">
                    <button type="submit" name="submit" class="add_phrase_button"> Add phrase</button>
                </div>
            </form>
        </div>

    </section>

    <h2 class=message-output>
        <?php

            //if form is submitted
            if (isset($_POST['submit'])) {

                //get variables from form
                $authorName = $_POST['name'];
                $topic = $_POST['topic'];
                $phrase = $_POST['phrase'];

                //Validating inputs 
                if (!isset($_POST['name']) || empty(trim($_POST['name']))){
                    die("invalid name: ");
                }
                elseif (!isset($_POST['topic']) || empty(trim($_POST['topic']))){
                    die("invalid topic: ");
                }
                elseif (!isset($_POST['phrase']) || empty(trim($_POST['phrase']))){
                    die("invalid phrase: ");
                }
            
                //connect to db
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

                //Select the id, date, and time from the row with the max date and id
                $sql = "SELECT id, quote_date, quote_time 
                        FROM quote_table WHERE quote_date = (
                            SELECT MAX(quote_date) FROM quote_table 
                            )
                            AND id = (
                            SELECT MAX(id) FROM quote_table 
                            )";

                //run the sql query
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);

                $quote_date = $row['quote_date'];
                $quote_time = $row['quote_time'];
                
                // if time is 8am, keep the date the same as the max entry and add 12 hours to the time
                if ($quote_time == "08:00:00"){
                    $new_date = $quote_date;
                    $new_time = "20:00:00";
                }

                // if the time is 8pm add 1 day to the date and set the time as 8am
                if ($quote_time == "20:00:00"){
                    $time_added = strtotime($quote_date) + (3600*36);
                    $new_date = date("Y-m-d", $time_added);
                    $new_time = "08:00:00";
                }

                //insert variables into quotes table
                $sql = "INSERT INTO quote_table
                (author, topic, quote, quote_date, quote_time)
                VALUES ('$authorName', '$topic', '$phrase', '$new_date', '$new_time')";

                //outputs text if record is created
                if ($conn->query($sql) === TRUE){
                    echo "Phrase added to database";
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
