<?php
    include('db_credentials.php');

    // connect to db
    $conn = dbConnect();

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