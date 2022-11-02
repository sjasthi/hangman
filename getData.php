
<?php
// db settings
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'quotes_db';

// db connection
$con = mysqli_connect($hostname, $username, $password, $database) or die("Error " . mysqli_error($con));

// fetch data
$sql = "select * from quote_table";
$result = mysqli_query($con, $sql);

// add each row as an array entry
while($row = mysqli_fetch_assoc($result)) {
    $array[] = $row;
}

$dataset = array(
    "echo" => 1,
    "totalrecords" => count($array),
    "totaldisplayrecords" => count($array),
    "data" => $array
);

// close connection
mysqli_close($con);

// return data
echo json_encode($dataset);
?>