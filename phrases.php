
 <?php
    
    include('nav.php');

    // if page is set set the page to the value
    // keeps track of the page the user is on
    if (isset($_GET["page"])){
        $current_page = $_GET["page"];
    }
    else{
        $current_page = 1;
    }

    // if per_page is set set to the value
    // keeps track of how many rows per page the user wants
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

    // connect to db
    $conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    // if connection fails print error
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // if removeQuote is set remove the selected quote
    if(isset($_POST['removeQuote'])){
        $removeId = $_POST['removeQuote'];

        $sql = "DELETE FROM quote_table WHERE id = $removeId";

        //outputs text if record is removed
        if ($conn->query($sql) === TRUE){
            echo "<p name=removeMsg>Phrase id:". $removeId ." removed from database<p>";
        }
        else{
            echo "error: " . $sql . "<br>" . $conn->error;
        }
    }    
    
    // updates phrase if set 
    if (isset($_GET["updatePhrase"])){
        $phrase = $_GET["updatePhrase"];
        $id = $_GET["updateId"];

        $sql = "UPDATE quote_table
                SET quote = '$phrase'
                WHERE id = $id";

        if ($conn->query($sql) === TRUE){
            echo "<p name=updateMsg>Updated phrase<p>";
        }
        else{
            echo "error: " . $sql . "<br>" . $conn->error;
        }
    }

    // update topic if set
    if (isset($_GET["updateTopic"])){
        $topic = $_GET["updateTopic"];
        $id = $_GET["updateId"];

        $sql = "UPDATE quote_table
                SET topic = '$topic'
                WHERE id = $id";

        if ($conn->query($sql) === TRUE){
            echo "<p name=updateMsg>Updated Topic<p>";
        }
        else{
            echo "error: " . $sql . "<br>" . $conn->error;
        }
    }
    // update name if set
    if (isset($_GET["updateName"])){
        $name = $_GET["updateName"];
        $id = $_GET["updateId"];

        $sql = "UPDATE quote_table
                SET author = '$name'
                WHERE id = $id";

        if ($conn->query($sql) === TRUE){
            echo "<p name=updateMsg>Updated Name<p>";
        }
        else{
            echo "error: " . $sql . "<br>" . $conn->error;
        }
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
    </div>  


    <table class="phrase_table">
        <tr class="table_title">
            <th> ID </th>
            <th> Author </th>
            <th> Topic </th>
            <th> Quote </th>
            <th> Quote Date </th>
            <th> Quote Time </th>
            <th> remove </th>
            <th> edit </th>
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

                // remove phrase button
                echo "<form method='POST' action=''>";
                echo "<td> <button type='submit' name='removeQuote' value= '".$row['id']."'> remove </button> </td>"; 
                echo "</form>";

                // update button
                echo '<td> <button type="button" name="editQuote" onclick="openForm('.$row['id'].')"> edit </button> </td>';

                echo "</tr>";

                // div responsible for dimming the page if the update button is clicked
                echo '<div class="dim" id="dim"> </div>';
                
                // div that contains the update form, hidden until the update button is clicked
                echo '<div id="myForm'.$row['id'].'" class="updateForm">';
                    echo '<button type="button" onclick="closeForm('.$row['id'].')" class="closeFormButton">+</button>';
                    echo "<h1 class='formTitle'>Update " . $row['id'] . "</h1>";
                    ?>
                    
                    <form>
                        <div class=name_input>
                            <input type="text" class ="update_name_input" name="updateName" placeholder="Author name...." required>
                            <input type="hidden" name="updateId" value='<?php echo $row['id']; ?>'>
                        </div>
                        <div class="submit-button">
                            <button type="submit" name="update" class="update_phrase_button"> update author</button>
                        </div>
                    </form>
                    <form>
                        <div class=topic_input>
                            <input type="text" class ="update_topic_input" name="updateTopic" placeholder="Topic...." required>
                            <input type="hidden" name="updateId" value='<?php echo $row['id']; ?>'>
                        </div>
                        <div class="submit-button">
                            <button type="submit" name="submit" class="update_topic_button"> update topic</button>
                        </div>
                    </form>
                    <form>
                        <div class="phrase-input">
                            <input type="text" class ="update_phrase_input" name="updatePhrase" placeholder="phrase...." required>
                            <input type="hidden" name="updateId" value='<?php echo $row['id']; ?>'>
                        </div>
                        <div class="submit-button">
                            <button type="submit" name="submit" class="update_phrase_button"> update phrase</button>
                        </div>
                    </form>
                </div>
            <?php
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

        // pagination page selector
        echo "<div class='page_link_div'>";
            if ($total_pages > 1){
                // if the current page is 3 or higher add three dots after displaying page 1
                if ($current_page > 3){
                    echo "<a href='phrases.php?page=1&per_page=".$rows_per_page."' class=page_link_button> 1 </a>";
                    echo "<p class=dots>";
                    echo " . . . ";
                    echo "</p>";
                }
                // displays the 2 before the current
                if ($current_page-2 > 0 ){
                echo "<a href='phrases.php?page=".$current_page-2 ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page-2 . "</a>";
                }
                // displays the page before the current
                if ($current_page-1 > 0 ){
                    echo "<a href='phrases.php?page=".$current_page-1 ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page-1 . "</a>";
                }
                // displays the current page
                echo "<a href='phrases.php?page=".$current_page ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page . "</a>";
                // displays the page number 1 after the current page
                if ($current_page+1 <= $total_pages){
                    echo "<a href='phrases.php?page=".$current_page+1 ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page+1 . "</a>";
                }
                // displays the page number 2 after the current page
                if ($current_page+2 <= $total_pages){
                    echo "<a href='phrases.php?page=".$current_page+2 ."&per_page=".$rows_per_page."' class=page_link_button>" . $current_page+2 . "</a>";
                }
                // displays 3 dots and the last page number
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

<script>
    // opens the form and dims page when the user selects update
    function openForm(id){
        document.getElementById("myForm"+id).style.display="block";
        document.getElementById("dim").style.display="block";
    }
    // closes the form and un-dims the page 
    function closeForm(id){
        document.getElementById("myForm"+id).style.display="none";
        document.getElementById("dim").style.display="none";
    }
</script>