<?php
define("letters", "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
session_start();
const quotes = array("App", "Television", "Hungry", "Basketball", "Hangman", "గోధుమరంగునక్క", "Hi there", "మిమ్ములని కలసినందుకు సంతోషం", "For What its Worth", "నేను దుకాణానికి వెళ్తున్నాను");


echo "QUOTE: " . $_SESSION['quote'] . "<br>";


if (empty($_SESSION["test"])) {
    resetGame();
}

// Creates HTML for the buttons.
function createButtons()
{
    $attributeLetter = "enabled";
    $attributePhrase = "disabled";

    if ($_SESSION["remainingChars"] == 0) {
        $attributePhrase = "enabled";
        $attributeLetter = "disabled";
    }

    if ($_SESSION["gameOver"]) {
        $attributePhrase = "disabled";
        $attributeLetter = "disabled";
    }

    echo "<label for='single-char-input'>Enter Letter </label>";
    echo "<input type='text' name='letter-guess' id='single-char-input' maxlength ='1' $attributeLetter>";
    echo "<input type='submit' value='Submit' $attributeLetter>";

    echo "<br>";
    echo "<label for='single-char-input'>Guess the Phrase </label>";
    echo "<input type='text' name='phrase-guess' id='phrase-input' $attributePhrase>";

    echo "<input type='submit' value='Submit' $attributePhrase>";
}

// Creates HTML for the inputs.
function createInputs()
{
//var_dump($_SESSION["fullMatch"]); // remove this to fix ui

    $quote_length = getLength($_SESSION['quote']);
    echo "quote length: ";
    echo $quote_length;
    echo "<ul>";
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
            if (in_array($guess_letter, $_SESSION["baseChars"]) || in_array($guess_letter, $_SESSION["logicalChars"]) ) { // If the letter is a correct guess, update 'test' array.
                updateArray($guess_letter);
            } else {
                $_SESSION["guesses"] = $_SESSION["guesses"] + 1; // If the letter is incorrect, add one to guesses.
                if ($_SESSION["guesses"] >= 6) {
                    $_SESSION["gameOver"] = true;
                }
            }
        }
    }
}

// Checks the guess phrase
function validatePhrase() {
    if (isset($_GET['phrase-guess'])) {
        
        $guess_phrase = trim($_GET['phrase-guess']);
        $logicalChars = getLogicalChars($guess_phrase);

        if ($_SESSION["logicalChars"] === $logicalChars) {
            $_SESSION["test"] = $_SESSION["logicalChars"];
            $_SESSION["fullMatch"] = array_fill(0, $_SESSION["quoteLength"], true);
            $_SESSION["guesses"] = 7;
            $_SESSION["gameOver"] = true;
        } else {
            $_SESSION["guesses"] = 6;
            $_SESSION["gameOver"] = true;
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
        $sanitizedData = substr($data, stripos($data, "{"));
        $decodedData = json_decode($sanitizedData);
        var_dump($decodedData->data);

        if ($i == 0) { // Reassembles the quote with spaces.
            $result = $decodedData->data;
        }
        else {
            array_push($result, " ");
            $result = array_merge($result, $decodedData->data);
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
    $sanitizedData = substr($data, stripos($data, "{"));
    $decodedData = json_decode($sanitizedData);
    // var_dump($decodedData->data);
    return $decodedData->data;
}

// Use wpapi api to get the length of string
function getLength($quote)
{
    $new_quote = str_replace(" ", "%20", $quote); // Converts spaces into a compatable charachter for the API.
    $data = file_get_contents('https://wpapi.telugupuzzles.com/api/getLength.php?input1=' . $new_quote . '&input2=English');
    $santitizeData = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data);
    $decodedData = json_decode($santitizeData);
    return $decodedData->data;
}

//Updates session array for instance of the guess letter
function updateArray($letter)
{

    for ($index = 0; $index < count($_SESSION["baseChars"]); $index++) {

        // set full match to true if logical characters match
        if (strcmp($letter, $_SESSION["logicalChars"][$index]) == 0) {
            $_SESSION["test"][$index] = $letter;
            $_SESSION["fullMatch"][$index] = true;
            $_SESSION["remainingChars"]--;
        }
        else if (strcmp($letter, $_SESSION["baseChars"][$index]) == 0) {
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
    $_SESSION['quote'] = getQuote();
    $_SESSION["baseChars"] = getBaseChars($_SESSION['quote']);
    $_SESSION["logicalChars"] = getLogicalChars($_SESSION['quote']);
    $_SESSION["guesses"] = 0;
    $_SESSION["quoteLength"] = getLength($_SESSION['quote']);
    $_SESSION["remainingChars"] = $_SESSION["quoteLength"];
    $_SESSION["quoteLength"] = $_SESSION["remainingChars"];
    $_SESSION["gameOver"] = false;
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
 
}

# connects to the mysql database
function dbConnect(){
    DEFINE('DB_SERVER', 'localhost');
    DEFINE('DB_NAME', 'quotes_db');
    DEFINE('DB_USER', 'root');
    DEFINE('DB_PASS', '');
    
    $conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

# generates a new random quote from the mysql 'quote_db' database
function getQuote(){
    $conn = dbConnect();

    $resultlog = mysqli_query($conn,"SELECT * FROM quote_table ORDER BY RAND() LIMIT 1;") or die(mysqli_error($conn));
    while($row = mysqli_fetch_array($resultlog)){
        $quote = $row['quote'];
    }

    return $quote;
}

echo $_SESSION["remainingChars"];

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



    <div class="container">


        <div class="hangman-container">

            <div>
                <img src="<?php setState() ?>" alt="Hangman full">
            </div>

        </div>


        <div class="input-container">


            <?php

            createInputs($_SESSION['start']);

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

        <?php
        if (isset($_POST['button1'])) {
            resetGame();
             header("Refresh:0; url=hangman.php");
        }
        ?>
        <!--- end of temporary section --->

    </div>


</body>

</html>