
 <?php
    // header
    include('nav.php');
    include('db_configuration.php');

    // connect to db

    $conn = dbConnect();
  

    // if add phrase is set, attempt to add new phrase
    if (isset($_POST["addName"])){

        // set variables to post values
        $name = $_POST["addName"];
        $topic = $_POST["addTopic"];
        $phrase = $_POST["addPhrase"]; 
                             
        $sql = "INSERT INTO custom_quotes_table
        (author, topic, quote)
        VALUES ('$name', '$topic', '$phrase')";  

        // outputs text if record is created
        if ($conn->query($sql) === TRUE){
            echo "<p class='notification_message'>Phrase added to database</p>";            }
                    
        // if connection fails, prints error message
        else{
            echo "error" . $sql . "<br>" . $conn->error;
        }
        
    }

    // updates phrase if set 
    if (isset($_POST["editId"])){
        $id = $_POST["editId"];
        $name = $_POST["editName"];
        $topic = $_POST["editTopic"];
        $phrase = $_POST["editPhrase"];

        $sql = "UPDATE custom_quotes_table
                SET author = '$name',
                topic = '$topic',
                quote = '$phrase'
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
    <table id="example" class="display nowrap" style="width:100%" >
        <thead>
            <tr>
                <th>ID</th>
                <th>Author</th>
                <th>Topic</th>
                <th>Phrase</th>
                <th></th>
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

        <input type="hidden" class ="edit_id_input" name="editId" id="editId"/> 

        <div class="submit-button">
            <button type="submit" name="editEntry" class="edit_entry_button"> Update Phrase</button>
        </div>
    </form>
</div>

<script>
    // get data via ajax request and populate table
    $(document).ready(function() {
        $('#example').dataTable({
            "processing": true,
            "ajax": "getCustomData.php",
            "columns": [
                {data: 'id',}    ,
                {data: 'author'},
                {data: 'topic'},
                {data: 'quote'},
                {
                    data: null,
                    className: "play_entry",
                    defaultContent: '<button class="play_entry"><img src="images/play_icon.png" class="play_icon"/></button>',
                    orderable: false,
                    "width": "5%"
                },
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

        $('#example').on( 'click', 'td.play_entry', function () {
            var data = table.row(this).data();
            window.location.href = "hangman.php?id="+data['id'];
        });

        // when delete button is clicked run the following function
        $('#example').on( 'click', 'td.delete_entry', function () {
            // get this row from data table
            var data = table.row(this).data();

            // run specified function via ajax request to remove entry from db
            jQuery.ajax({
                type: "POST",
                url: 'removeCustomEntry.php',
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
            document.getElementById('editId').setAttribute('value', data['id']);

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