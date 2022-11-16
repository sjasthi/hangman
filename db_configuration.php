<?php
DEFINE('DB_SERVER', 'localhost');
DEFINE('DB_NAME', 'quotes_db');
DEFINE('DB_USER', 'root');
DEFINE('DB_PASS', '');

# connects to the mysql database
function dbConnect(){
    
    $conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>