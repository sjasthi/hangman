<?php

    DEFINE('DB_SERVER', 'localhost');
    DEFINE('DB_NAME', 'quotes_db');
    DEFINE('DB_USER', 'root');
    DEFINE('DB_PASS', '');
    // connect to db
    $conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    // if connection fails print error
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // if removeQuote is set remove the selected quote
    if(isset($_POST['functionname'])){

        $removeId = $_POST['arguments'][0];

        $sql = "DELETE FROM quote_table WHERE id = $removeId";

        //outputs text if record is removed
        if ($conn->query($sql) != TRUE){
            echo "error: " . $sql . "<br>" . $conn->error;
        }
    }    
    $conn -> close();
?>