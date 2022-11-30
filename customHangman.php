<?php
include("db_configuration.php");

if (empty(session_id()) && !headers_sent()) {
    session_start();
}


if (!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
}

if (isset($_GET['id'])) {
    setGame();
}
setState();

// Resets the session variables.
function setGame()
{
    $_SESSION['customeQuote'] = getCustomQuote($_GET['id']);

    $_SESSION["customBaseChars"] = getBaseChars($_SESSION['customeQuote']);
    $_SESSION["customLogicalChars"] = getLogicalChars($_SESSION['customeQuote']);
    $_SESSION["customGuesses"] = 0;
    $_SESSION["customQuoteLength"] = getLength($_SESSION['customeQuote']);
    $_SESSION["customRemainingChars"] = $_SESSION["customQuoteLength"];
    $_SESSION["customQuoteLength"] = $_SESSION["customRemainingChars"];
    $_SESSION["customGameOver"] = false;
    $_SESSION["customInput"] = [];
    
    // initialize and dynamically fill both arrays base on the quote length
    $_SESSION["customFullMatch"] = array_fill(0, $_SESSION["customQuoteLength"], false);

    // Brought this back to fill customInput with spaces and underscores.
     for($i = 0; $i < $_SESSION["customQuoteLength"]; $i++){

        if(isset($_SESSION["customBaseChars"][$i])) {
            if ($_SESSION["customBaseChars"][$i] == " ") {
                array_push($_SESSION["customInput"], " ");
                $_SESSION["customFullMatch"][$i] = true;
                $_SESSION["customRemainingChars"]--;
            }
            else {
                array_push($_SESSION["customInput"], "_");
            }
        }
    }
}

// Creates HTML for the buttons.
function createButtons()
{
    $attribute_letter = "enabled";
    $attribute_phrase = "disabled";

    if ($_SESSION["customRemainingChars"] == 0) {
        $attribute_phrase = "enabled";
        $attribute_letter = "disabled";
    }

    if ($_SESSION["customGameOver"]) {
       
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
//var_dump($_SESSION["customFullMatch"]); // remove this to fix ui

    $quote_length = getLength($_SESSION['customeQuote']);
    echo "<ul class='input-list'>";
    for ($i = 0; $i <  $quote_length;  $i++) {
        #echo "<span>" . "&nbsp;&nbsp" .$_SESSION["customInput"][$i] . "</span>";

        # if the letter is in the word and has been guessed create a list item
        # with the class to turn the tile green
        #if the letter is fill and it is a full logical match, then highlight the color in green background Color

        
        # if the quote letter is a space, make it blank
        if (isset($_SESSION["customInput"][$i])) {
            if ($_SESSION["customInput"][$i] == " ") {
                echo "<li class='spaceChar'> " . $_SESSION["customInput"][$i] . "</li>";
            }
            else if ( ($_SESSION["customInput"][$i] != "_") &&  ($_SESSION["customFullMatch"][$i] == true)) {
                # else create list item with no class name
                echo "<li class='correctLetter'> " . $_SESSION["customInput"][$i] . "</li>";
            } 
            # if the letter is fill but it is a not full logical match, then highlight the color in yellow background Color (means it is base match)
            else  if ($_SESSION["customInput"][$i] != "_") {
                echo "<li class='baseCharMatch'> " . $_SESSION["customInput"][$i] . "</li>";
            } else {
                echo "<li>" . $_SESSION["customInput"][$i] . "</li>";
            }
        }
    }

    echo "</ul>";
}

// Updates the 'customInput' array and guesses.
function validateInputs()
{
    if (isset($_GET['letter-guess'])) { // If letter guess is set.

        $guess_letter = $_GET['letter-guess']; // Get the letter from the URL.
        if (!in_array($guess_letter, $_SESSION["customInput"])) {
            if (in_array($guess_letter, $_SESSION["customBaseChars"]) || in_array($guess_letter, $_SESSION["customLogicalChars"]) ) { // If the letter is correct
                updateArray($guess_letter);
            } else {
                $_SESSION["customGuesses"] = $_SESSION["customGuesses"] + 1; // If the letter is incorrect
                if ($_SESSION["customGuesses"] >= 6) {
                    $_SESSION["customGameOver"] = true;
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

        if ($_SESSION["customLogicalChars"] === $logical_chars) {
            $_SESSION["customInput"] = $_SESSION["customLogicalChars"];
            $_SESSION["customFullMatch"] = array_fill(0, $_SESSION["customQuoteLength"], true);
            $_SESSION["customGuesses"] = 7;
            $_SESSION["customGameOver"] = true;
        } else {
            $_SESSION["customGuesses"] = 6;
            $_SESSION["customGameOver"] = true;
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
            if(!is_null($decoded_data->data)) {
                array_push($result, " ");
                $result = array_merge($result, $decoded_data->data);
            }
        }
    }
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

    for ($index = 0; $index < count($_SESSION["customBaseChars"]); $index++) {

        // set full match to true if logical characters match
        if (strcmp($letter, $_SESSION["customLogicalChars"][$index]) == 0) {
            if ($_SESSION["customInput"][$index] == "_") {
                $_SESSION["customRemainingChars"]--;
            }
            $_SESSION["customFullMatch"][$index] = true;
            $_SESSION["customInput"][$index] = $letter;
            
        }
        else if (strcmp($letter, $_SESSION["customBaseChars"][$index]) == 0 && $_SESSION["customInput"][$index] == "_") {
            $_SESSION["customInput"][$index] = $letter;
            $_SESSION["customRemainingChars"]--;
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
    switch ($_SESSION["customGuesses"]) { // Checks how many bad guesses have been made and sets the image.
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

function getCustomQuote($id){
    $conn = dbConnect();

    $resultlog = mysqli_query($conn,"SELECT * FROM custom_quotes_table WHERE id = '$id'") or die(mysqli_error($conn));
    while($row = mysqli_fetch_array($resultlog)){
        $quote = $row['quote'];
    }

    return $quote;
}

function printGameVars() {
    echo "QUOTE: " . $_SESSION['customQuote'] . "<br>";
    echo $_SESSION["customRemainingChars"];
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
    include("nav.php");
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
            <form action="customHangman.php" method="get">

                <?php
                createButtons();

                ?>
            </form>

        </div>

    </div>


</body>

</html> 