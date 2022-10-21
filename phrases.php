
 <?php

include('nav.php');

if (isset($_GET["page"])){
    $current_page = $_GET["page"];
}
else{
    $current_page = 1;
}

if (isset($_GET["per_page"])){
    $rows_per_page = $_GET["per_page"];
}
else{
    $rows_per_page = 5;
}


DEFINE('DB_SERVER', 'localhost');
DEFINE('DB_NAME', 'quotes_db');
DEFINE('DB_USER', 'root');
DEFINE('DB_PASS', '');
    
$conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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


    <div class="button-div">
    <button class="add-phrase-button"><a href='addPhrase.php'> Add phrase </a></button>
    <button class="remove-phrase-button"><a href='removePhrase.php'> Remove phrase </a></button>
    </div>  


    <table class="phrase_table">
        <tr class="table_title">
            <th> ID </th>
            <th> Author </th>
            <th> Topic </th>
            <th> Quote </th>
            <th> Quote Date </th>
            <th> Quote Time </th>
        </tr>


        <?php
            
            // from where to start selecting from database
            $start_from = ($current_page-1) * $rows_per_page;

            //run sql query and obtain results
            $sql = "select * from quote_table limit $start_from,$rows_per_page";
            $resultlog = mysqli_query($conn, $sql);

            //display each phrase as a row in the table
            while($row = mysqli_fetch_array($resultlog))
            {
                echo "<tr class='table_data'>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['author'] . "</td>";
                echo "<td>" . $row['topic'] . "</td>";
                echo "<td>" . $row['quote'] . "</td>";
                echo "<td>" . $row['quote_date'] . "</td>";
                echo "<td>" . $row['quote_time'] . "</td>";
                echo "</tr>";
            }
        ?>
    </table>
    
    Rows per page 
    <select onchange="location = value;">
        <option value = "none" selected disabled hidden> <?php $rows_per_page ?> </option> 
        <option value="phrases.php?per_page=5"> 5 </value>
        <option value="phrases.php?per_page=10"> 10 </value>
        <option value="phrases.php?per_page=20"> 20 </value>
        <option value="phrases.php?per_page=30"> 30 </value>
    </select>
    
    <?php 
        $sql = "select * from quote_table";
        $resultlog = mysqli_query($conn, $sql);

        $total_rows = mysqli_num_rows($resultlog);
        $total_pages = ceil($total_rows/$rows_per_page);

        echo "<div class='page_link_div'>";

            if ($total_pages > 1){
                //if the current page is 3 or higher add three dots after displaying page 1
                if ($current_page > 3){
                    echo "<a href='phrases.php?page=1&per_page=".$rows_per_page."' class=page_link_button> 1 </a>";
                    echo "<p class=dots>";
                    echo " . . . ";
                    echo "</p>";
                }
                //displays the 2 before the current
                if ($current_page-2 > 0 ){
                echo "<a href='phrases.php?page=".$current_page-2 ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page-2 . "</a>";
                }
                //displays the page before the current
                if ($current_page-1 > 0 ){
                    echo "<a href='phrases.php?page=".$current_page-1 ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page-1 . "</a>";
                }
                //displays the current page
                echo "<a href='phrases.php?page=".$current_page ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page . "</a>";
                //displays the page number 1 after the current page
                if ($current_page+1 <= $total_pages){
                    echo "<a href='phrases.php?page=".$current_page+1 ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page+1 . "</a>";
                }
                //displays the page number 2 after the current page
                if ($current_page+2 <= $total_pages){
                    echo "<a href='phrases.php?page=".$current_page+2 ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page+2 . "</a>";
                }
                //displays 3 dots and the last page number
                if ($current_page < ceil($total_rows/$rows_per_page)-2){
                    echo "<p class=dots>";
                    echo " . . . ";
                    echo "</p>";
                    echo "<a href='phrases.php?page=".ceil($total_rows/$rows_per_page) ."&per_page=".$rows_per_page."' class=page_link_button>" . ceil($total_rows/$rows_per_page) . "</a>";
                }
            }
        echo "</div>";

    ?>
</body>

