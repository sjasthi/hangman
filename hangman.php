<?php
include("db_credentials.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
define("letters", "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
date_default_timezone_set('America/Chicago');

if (!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
}

// Check if the reset button was pressed.
if (isset($_POST['button1'])) {
    resetGame();
    header("Location: hangman.php");
    return;
}

// const quotes = array("App", "Television", "Hungry", "Basketball", "Hangman", "గోధుమరంగునక్క", "Hi there", "మిమ్ములని కలసినందుకు సంతోషం", "For What its Worth", "నేను దుకాణానికి వెళ్తున్నాను");

// set cookie

if (isset($_GET['id'])) {
    resetGame();
}



if (empty($_SESSION["test"])) {
    setInitialCookies();
    resetGame();
}

setState();

// Bandaid fix so cookies immediately update when a gameover happens.
if ($_SESSION["gameOver"] == true && $_SESSION["flag"] == true) {
    $_SESSION["flag"] = false;
    header("Location: hangman.php");
    return;
}

// Testing only. Remove or comment out later
// echo "QUOTE: " . $_SESSION['quote'] . "<br>";
// $current_day = date("Y-m-d");
// echo "Current Date: " . $current_day . "<br>";
// echo "Yesterday: " . date("Y-m-d", strtotime('-1 day', strtotime($current_day))) . "<br>";
// echo "Current Time: " . date("H:i:s") . "<br>";


// set the initial cookies to zero

function setInitialCookies(){
    setcookie("numberOfGamesPlayed", 0, time()+3600);
    setcookie("numberOfGamesWon", 0, time()+3600);
    setcookie("currentWinStreak", 0, time()+3600);
    setcookie("maxWinStreak", 0, time()+3600);
}

// Creates HTML for the buttons.
function createButtons()
{
    $attribute_letter = "enabled";
    $attribute_phrase = "disabled";

    if ($_SESSION["remainingChars"] == 0) {
        $attribute_phrase = "enabled";
        $attribute_letter = "disabled";
    }

    if ($_SESSION["gameOver"]) {
       
        $attribute_phrase = "disabled";
        $attribute_letter = "disabled";
    }
   

    echo "<label for='single-char-input'>Enter Letter </label>";
    echo "<input type='text' name='letter-guess' id='single-char-input' maxlength ='4' $attribute_letter>";
    echo "<input type='submit' value='Submit' $attribute_letter>";

    echo "<br>";
    echo "<label for='single-char-input'>Guess the Phrase </label>";
    echo "<input type='text' name='phrase-guess' id='phrase-input' $attribute_phrase>";

    echo "<input type='submit' value='Submit' $attribute_phrase>";
}

// Creates HTML for the inputs.
function createInputs()
{
//var_dump($_SESSION["fullMatch"]); // remove this to fix ui

    $quote_length = getLength($_SESSION['quote']);
    echo "<ul class='input-list'>";
    for ($i = 0; $i <  $quote_length;  $i++) {
        #echo "<span>" . "&nbsp;&nbsp" .$_SESSION["test"][$i] . "</span>";

        # if the letter is in the word and has been guessed create a list item
        # with the class to turn the tile green
        #if the letter is fill and it is a full logical match, then highlight the color in green background Color

        
        # if the quote letter is a space, make it blank
        if ($_SESSION["test"][$i] == " ") {
            echo "<li class='spaceChar'> " . $_SESSION["test"][$i] . "</li>";
        }
        else if ( ($_SESSION["test"][$i] != "_") &&  ($_SESSION["fullMatch"][$i] == true)) {
            # else create list item with no class name
            echo "<li class='correctLetter'> " . $_SESSION["test"][$i] . "</li>";
        } 
        # if the letter is fill but it is a not full logical match, then highlight the color in yellow background Color (means it is base match)
        else  if ($_SESSION["test"][$i] != "_") {
            echo "<li class='baseCharMatch'> " . $_SESSION["test"][$i] . "</li>";
        } else {
            echo "<li>" . $_SESSION["test"][$i] . "</li>";
        }
    }

    echo "</ul>";
}

function getCurrentQuote()
{
}

// Updates the 'test' array and guesses.
function validateInputs()
{
    if (isset($_GET['letter-guess'])) { // If letter guess is set.

        $guess_letter = $_GET['letter-guess']; // Get the letter from the URL.
        if (!in_array($guess_letter, $_SESSION["test"])) {
            if (in_array($guess_letter, $_SESSION["baseChars"]) || in_array($guess_letter, $_SESSION["logicalChars"]) ) { // If the letter is correct
                updateArray($guess_letter);
            } else {
                $_SESSION["guesses"] = $_SESSION["guesses"] + 1; // If the letter is incorrect
                if ($_SESSION["guesses"] >= 6) {
                    $_SESSION["gameOver"] = true;

                    if ($_SESSION['countStats']) {
                        setcookie("numberOfGamesPlayed", $_COOKIE["numberOfGamesPlayed"] + 1 , time()+3600);
                        setcookie("currentWinStreak", 0 , time()+3600);
                    }
                }
            }
        }
    }
}

// Checks the guess phrase
function validatePhrase() {
    if (isset($_GET['phrase-guess'])) {
        
        $guess_phrase = trim($_GET['phrase-guess']);
        $logical_chars = getLogicalChars($guess_phrase);
        $currentWinStreak = 0;

        if ($_SESSION["logicalChars"] === $logical_chars) {
            $_SESSION["test"] = $_SESSION["logicalChars"];
            $_SESSION["fullMatch"] = array_fill(0, $_SESSION["quoteLength"], true);
            $_SESSION["guesses"] = 7;
            $_SESSION["gameOver"] = true;
            
            if ($_SESSION['countStats']) {
                $currentWinStreak = $_COOKIE["numberOfGamesPlayed"] + 1;
                setcookie("numberOfGamesPlayed", $currentWinStreak , time()+3600);
                setcookie("numberOfGamesWon", $_COOKIE["numberOfGamesWon"] + 1 , time()+3600);
                setcookie("currentWinStreak", $_COOKIE["currentWinStreak"] + 1 , time()+3600);

                // set max winstreak
                
                if($_COOKIE["maxWinStreak"] <= $currentWinStreak){
                    setcookie("maxWinStreak", $currentWinStreak, time()+3600);
                }
            }
        } else {
            $_SESSION["guesses"] = 6;
            $_SESSION["gameOver"] = true;
            if ($_SESSION['countStats']) {
                setcookie("numberOfGamesPlayed", $_COOKIE["numberOfGamesPlayed"] + 1 , time()+3600);
                setcookie("currentWinStreak", 0 , time()+3600);
            }
        }
    }
}

// Use wpapi api to get base characters
function getBaseChars($quote)
{
    $quote_array = explode(" ", $quote); // Breaks the quote into an array of words.
    $result = [];

    for ($i = 0; $i < count($quote_array); $i++) {
        $data = file_get_contents('https://wpapi.telugupuzzles.com/api/getBaseCharacters.php?input1=' . $quote_array[$i] . '&input2=English');
        $sanitized_data = substr($data, stripos($data, "{"));
        $decoded_data = json_decode($sanitized_data);
        // var_dump($decoded_data->data);

        if ($i == 0) { // Reassembles the quote with spaces.
            $result = $decoded_data->data;
        }
        else {
            array_push($result, " ");
            $result = array_merge($result, $decoded_data->data);
        }
    }
    echo "<br>";
    return $result;
}

// Use wpapi api to get logical characters
function getLogicalChars($quote)
{
    $new_quote = str_replace(" ", "%20", $quote); // Converts spaces into a compatable charachter for the API.
    $data = file_get_contents('https://wpapi.telugupuzzles.com/api/getLogicalChars.php?string=' . $new_quote . '&language=English');
    $sanitized_data = substr($data, stripos($data, "{"));
    $decoded_data = json_decode($sanitized_data);
    // var_dump($decodedData->data);
    return $decoded_data->data;
}

// Use wpapi api to get the length of string
function getLength($quote)
{
    $new_quote = str_replace(" ", "%20", $quote); // Converts spaces into a compatable charachter for the API.
    $data = file_get_contents('https://wpapi.telugupuzzles.com/api/getLength.php?input1=' . $new_quote . '&input2=English');
    $santitize_data = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data);
    $decoded_data = json_decode($santitize_data);
    return $decoded_data->data;
}

//Updates session array for instance of the guess letter
function updateArray($letter)
{

    for ($index = 0; $index < count($_SESSION["baseChars"]); $index++) {

        // set full match to true if logical characters match
        if (strcmp($letter, $_SESSION["logicalChars"][$index]) == 0) {
            if ($_SESSION["test"][$index] == "_") {
                $_SESSION["remainingChars"]--;
            }
            $_SESSION["fullMatch"][$index] = true;
            $_SESSION["test"][$index] = $letter;
            
        }
        else if (strcmp($letter, $_SESSION["baseChars"][$index]) == 0 && $_SESSION["test"][$index] == "_") {
            $_SESSION["test"][$index] = $letter;
            $_SESSION["remainingChars"]--;
        }
    }
}

// Calls validateInput and then sets the hangman image.
function setState()
{

    validateInputs();
    validatePhrase();  
}

// HTML to set the hangman image.
function setImage()
{
    switch ($_SESSION["guesses"]) { // Checks how many bad guesses have been made and sets the image.
        case 0:
            echo "./css/images/gallow0.png";
            break;
        case 1:
            echo "./css/images/gallow1.png";
            break;
        case 2:
            echo "./css/images/gallow2.png";
            break;
        case 3:
            echo "./css/images/gallow3.png";
            break;
        case 4:
            echo "./css/images/gallow4.png";
            break;
        case 5:
            echo "./css/images/gallow5.png";
            break;
        case 6:
            echo "./css/images/gallow6.png";
            break;
        case 7:
            echo "./css/images/gallow7.png";
            break;
        default:
            echo "Whoops! Looks like somethings broken.";
            break;
    }
}

// Resets the session variables.
function resetGame()
{
    // Temporary testing variables 
    $testdate = "2022-10-18";
    $testtime = "07:00:00";
    $testtime2 = "08:00:00";
    $testtime3 = "20:00:00";

    // $_SESSION['quote'] = getQuote($testdate, $testtime);
    // $_SESSION['quote'] = getQuote($testdate, $testtime2);
    // $_SESSION['quote'] = getQuote($testdate, $testtime3);



    $current_date = date("Y-m-d");
    $current_time = date("H:i:s");

    if (isset($_GET['id'])) {
        $_SESSION['quote'] = getCustomQuote($_GET['id']);
        $_SESSION['countStats'] = false;
    }
    else {
        $_SESSION['quote'] = getQuote($current_date, $current_time);
        $_SESSION['countStats'] = true;
    }
    // $_SESSION['quote'] = getQuote($current_date, $current_time);

    $_SESSION["baseChars"] = getBaseChars($_SESSION['quote']);
    $_SESSION["logicalChars"] = getLogicalChars($_SESSION['quote']);
    $_SESSION["guesses"] = 0;
    $_SESSION["quoteLength"] = getLength($_SESSION['quote']);
    $_SESSION["remainingChars"] = $_SESSION["quoteLength"];
    $_SESSION["quoteLength"] = $_SESSION["remainingChars"];
    $_SESSION["gameOver"] = false;
    $_SESSION["flag"] = true;
    $_SESSION["test"] = [];
    
    // initialize and dynamically fill both arrays base on the quote length
    $_SESSION["fullMatch"] = array_fill(0, $_SESSION["quoteLength"], false);
    // $_SESSION["test"] = array_fill(0, $_SESSION["quoteLength"], "_");

    // Brought this back to fill test with spaces and underscores.
     for($i = 0; $i < $_SESSION["quoteLength"]; $i++){

        if ($_SESSION["baseChars"][$i] == " ") {
            array_push($_SESSION["test"], " ");
            $_SESSION["fullMatch"][$i] = true;
            $_SESSION["remainingChars"]--;
        }
        else {
            array_push($_SESSION["test"], "_");
        }
    }

    // set cookies to zero
}

# generates a quote based on date and time from the mysql 'quote_db' database
# $date format: "YYYY-MM-DD"
# $time format: "hh:mm:ss" 24-hour time
function getQuote($date, $time){
    $active_date = $date;
    $active_time = "20:00:00";

    if ((strtotime($time) < strtotime("08:00:00"))) {
        $active_date = date("Y-m-d", strtotime('-1 day', strtotime($date)));
    } elseif ((strtotime($time) >= strtotime("08:00:00")) and ((strtotime($time) < strtotime("20:00:00")))) {
        $active_time = "08:00:00";
    }

    $conn = dbConnect();

    $resultlog = mysqli_query($conn,"SELECT * FROM quote_table WHERE quote_date = '$active_date' AND quote_time = '$active_time'") or die(mysqli_error($conn));
    while($row = mysqli_fetch_array($resultlog)){
        $quote = $row['quote'];
    }

    return $quote;
}

function getCustomQuote($id){
    $conn = dbConnect();

    $resultlog = mysqli_query($conn,"SELECT * FROM custom_quotes_table WHERE id = '$id'") or die(mysqli_error($conn));
    while($row = mysqli_fetch_array($resultlog)){
        $quote = $row['quote'];
    }

    return $quote;
}

function printGameVars() {
    echo "QUOTE: " . $_SESSION['quote'] . "<br>";
    echo $_SESSION["remainingChars"];
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
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

</head>

<body>
    <?php
    include("stats.php");
    printGameVars();
    ?>


    <div class="container">


        <div class="hangman-container">

            <div>
                <img src="<?php setImage() ?>" alt="Hangman full">
            </div>

        </div>


        <div class="input-container">


            <?php

            createInputs();

            ?>
        </div>


        <div class="button-container">
            <form action="hangman.php" method="get">

                <?php
                createButtons();

                ?>
            </form>

        </div>

        <div class="stats-container">
            <div>
                Longest Streak: 0
            </div>


            <div>Current Streak: 0</div>

        </div>

        <!--- THIS IS TEMPORARY, USED TO RESET SESSION */ --->
        <form method="post">
            <input type="submit" name="button1" value="reset session" />
        </form>

        
        <!--- end of temporary section --->

    </div>


</body>

</html> 
