
 <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // if user roll is "USER" show phrases tab (this needs to change to admin later)
    $apiReturn = file_get_contents('https://wpapi.telugupuzzles.com/api/getRole.php?email=' . $_SESSION['userEmail']);
    $parsedApiReturn = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $apiReturn), true );

    if($parsedApiReturn["data"]=="USER"){

        include('db_configuration.php');

        // connect to db
        $conn = dbConnect();
    
        // select max date then select max time from the max dates
        $sql = "SELECT id, quote_date, MAX(quote_time) as quote_time 
            FROM quote_table WHERE quote_date = (
            SELECT MAX(quote_date) FROM quote_table 
            )";

        //run the sql query
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $quote_date = $row[ 'quote_date'];
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

        // if add phrase is set, attempt to add new phrase
        if (isset($_POST["addName"])){

            // set variables to post values
            $name = $_POST["addName"];
            $topic = $_POST["addTopic"];
            $phrase = $_POST["addPhrase"];
            if (isset($_POST["addDate"])){      
                $date = $_POST["addDate"];
            }
            if (isset($_POST["addTime"])){      
                $time = $_POST["addTime"];
            }   

            // if date and time are set
            if ($date != '' && isset($_POST["addTime"])){    

                // convert time to proper format
                if ($time == "morning"){
                    $time = "08:00:00";
                }
                if ($time == "evening"){
                    $time = "20:00:00";
                }

                // check to see if date/time combo exists already in db
                $sql="SELECT 1
                    FROM quote_table
                    WHERE quote_date='$date' AND quote_time='$time'";

                $result = mysqli_query($conn, $sql);

                // if date/time combo are unique, add the entry to the db
                if($result !== false && $result->num_rows == 0){

                    $sql = "INSERT INTO quote_table
                    (author, topic, quote, quote_date, quote_time)
                    VALUES ('$name', '$topic', '$phrase', '$date', '$time')";  
        
                    //outputs text if record is created
                    if ($conn->query($sql) === TRUE){
                        echo "<p class='notification_message'>Phrase added to database</p>";
                    }
                    //if connection fails, prints error message
                    else{
                        echo "<p class='notification_message'>error: " . $sql . "</p><br>" . $conn->error;
                    }
                } 

                // else date and time combo already exist
                else {
                    echo "<p class='notification_message'>Date & Time combo already exist.</p>";
                }
            }

            // if only date is set
            else if ($_POST["addDate"] != ''){                          
                echo "<p class='notification_message'>If you set date, you must set the time.</p>";
            }

            // if only time is set
            else if (isset($_POST["addTime"])){                           
                echo "<p class='notification_message'>If you set time, you must set the date.</p>";
            }

            // if neither date or time are set, set date/time automatically
            else{                                                               
                $sql = "INSERT INTO quote_table
                (author, topic, quote, quote_date, quote_time)
                VALUES ('$name', '$topic', '$phrase', '$new_date', '$new_time')";  

                // outputs text if record is created
                if ($conn->query($sql) === TRUE){
                    echo "<p class='notification_message'>Phrase added to database</p>";
                }
                        
                // if connection fails, prints error message
                else{
                    echo "error" . $sql . "<br>" . $conn->error;
                }
            }
        }

        // updates phrase if set 
        if (isset($_POST["editId"])){
            $id = $_POST["editId"];
            $name = $_POST["editName"];
            $topic = $_POST["editTopic"];
            $phrase = $_POST["editPhrase"];
            $date = $_POST["editDate"];
            $time = $_POST["editTime"];

            // convert time to proper format
            if ($time == "morning"){
                $time = "08:00:00";
            }
            if ($time == "evening"){
                $time = "20:00:00";
            }

            // check to see if date/time combo exists already in db
            $sql="SELECT 1
                FROM quote_table
                WHERE quote_date='$date' AND quote_time='$time' AND id!='$id'";

            $result = mysqli_query($conn, $sql);

            // if date/time combo are unique, add the entry to the db
            if($result !== false && $result->num_rows == 0){
                $sql = "UPDATE quote_table
                SET author = '$name',
                    topic = '$topic',
                    quote = '$phrase',
                    quote_date = '$date',
                    quote_time = '$time'
                    WHERE id = $id";

                // outputs text if record is created
                if ($conn->query($sql) === TRUE){
                    echo "<p class='notification_message'>Updated entry<p>";
                }

                // if connection fails, prints error message
                else{
                    echo "error: " . $sql . "<br>" . $conn->error;
                }
            }
            // else don't update because date/time combo exist already
            else{
                echo "<p class='notification_message'>Date & Time combo already exist.</p>";
            } 
        }
        // close connection to db
        $conn -> close();
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
    <?php
    include('nav.php');
    ?>
    <table id="example" class="display nowrap" style="width:100%" >
        <thead>
            <tr>
                <th>ID</th>
                <th>Author</th>
                <th>Topic</th>
                <th>Phrase</th>
                <th>Date</th>
                <th>Time</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    </table>
    <button type="button" onclick="openForm('addEntryForm')" class="add_phrase_button">Add Phrase</button>
</body>


<div class="dim" id="dim"> </div>

<!-- ADD ENTRY FORM -->
<div id="addEntryForm" name="addEntryForm" class="addEntryForm">
    <button type="button" onclick="closeForm('addEntryForm')" class="closeFormButton">+</button>
    <form method="POST">
        <h1 class='formTitle'>Add Phrase</h1>
        <div class=name_input>
            Name
            <input type="text" class ="add_name_input" name="addName" placeholder="Author name...." required>
            <p class="required">* </p>
        </div>

        <div class="topic_input">
            Topic
            <input type="text" class ="add_topic_input" name="addTopic" placeholder="Topic...." required>
            <p class="required">* </p>
        </div>

        <div class="Phrase_input">
            Phrase
            <input type="text" class ="add_phrase_input" name="addPhrase" placeholder="Phrase...." required>
            <p class="required">* </p>
        </div>

        <div class="date_input">
            Date
            <input type="date" class ="add_date_input" name="addDate" placeholder="Date....">
        </div>

        <div class="time_input">
                <input type="radio" name="addTime" id="morning" value="morning"/> 
                <label for="morning"> Morning </label>
                <input type="radio" name="addTime" id="evening" value="evening"/> 
                <label for="morning"> Evening </label>
        </div>

        <div class="submit-button">
            <button type="submit" name="addEntry" class="add_entry_button"> Add Phrase</button>
        </div>
    </form>
</div>

<!-- EDIT ENTRY FORM -->
<div id="editEntryForm" name="editEntryForm" class="editEntryForm">
    <button type="button" onclick="closeForm('editEntryForm')" class="closeFormButton">+</button>
    <form method="POST">
        <h1 class='formTitle'>Edit Phrase</h1>
        <div class=name_input>
            Name
            <input type="text" class ="edit_name_input" name="editName" id="editName" placeholder="Author name...." required>
            <p class="required">* </p>
        </div>

        <div class="topic_input">
            Topic
            <input type="text" class ="edit_topic_input" name="editTopic" id="editTopic" placeholder="Topic...." required>
            <p class="required">* </p>
        </div>

        <div class="Phrase_input">
            Phrase
            <input type="text" class ="edit_phrase_input" name="editPhrase" id="editPhrase" placeholder="Phrase...." required>
            <p class="required">* </p>
        </div>

        <div class="date_input">
            Date
            <input type="date" class ="edit_date_input" name="editDate" id="editDate" placeholder="Date....">
        </div>

        <div class="time_input">
                <input type="radio" name="editTime" id="morning" value="morning"/> 
                <label for="morning"> Morning </label>
                <input type="radio" name="editTime" id="evening" value="evening"/> 
                <label for="morning"> Evening </label>
        </div>

        <input type="hidden" class ="edit_id_input" name="editId" id="editId"/> 


        <div class="submit-button">
            <button type="submit" name="editEntry" class="edit_entry_button"> Update Phrase</button>
        </div>
    </form>
</div>

<?php
    } // end of initial if statement
?>
<script>
    // get data via ajax request and populate table
    $(document).ready(function() {
        $('#example').dataTable({
            "processing": true,
            "ajax": "getData.php",
            "columns": [
                {data: 'id',}    ,
                {data: 'author'},
                {data: 'topic'},
                {data: 'quote'},
                {data: 'quote_date'},
                {data: 'quote_time'},
                {
                    data: null,
                    className: "delete_entry",
                    defaultContent: '<button class="delete_entry"><img src="images/trash_icon.png" class="delete_icon"/></button>',
                    orderable: false,
                    "width": "5%"
                },
                {   
                    data: null,
                    className: "edit_entry",
                    defaultContent: '<button type="button" class="edit_entry" onclick="openForm(\'editEntryForm\')"><img src="images/pencil_icon.png" class="edit_icon"/></button>',
                    orderable: false,
                    "width": "5%"
                }
            ]
        });

        // set datatable to variable
        var table = $('#example').DataTable();

        // when delete button is clicked run the following function
        $('#example').on( 'click', 'td.delete_entry', function () {
            // get this row from data table
            var data = table.row(this).data();

            // run specified function via ajax request to remove entry from db
            jQuery.ajax({
                type: "POST",
                url: 'removeEntry.php',
                dataType: 'json',
                data: {functionname: 'removeQuote', arguments: [data['id']]},
            });

            // remove entry from ui
            table
			.row($(this).parents('tr'))
			.remove()
		    .draw();
        });

        // when edit button is clicked run the following function
        $('#example').on( 'click', 'td.edit_entry', function () {
            // get this row from data table
            var data = table.row(this).data();

            // populate form fields with data
            document.getElementById('editName').setAttribute('value', data['author']);
            document.getElementById('editTopic').setAttribute('value', data['topic']);
            document.getElementById('editPhrase').setAttribute('value', data['quote']);
            document.getElementById('editDate').setAttribute('value', data['quote_date']);
            document.getElementById('editId').setAttribute('value', data['id']);

            if (data['quote_time'] == "08:00:00"){
                $('input[id=morning]').prop('checked', true);

            }
            if (data['quote_time'] == '20:00:00'){
                $('input[id=evening]').prop('checked', true);
            }
        });
    
    });
    
    // opens the form and dims page 
    function openForm(id){
        document.getElementById(id).style.display="block";
        document.getElementById("dim").style.display="block";
    }
    
    // closes the form and un-dims the page 
    function closeForm(id){
        document.getElementById(id).style.display="none";
        document.getElementById("dim").style.display="none";
    }

</script>