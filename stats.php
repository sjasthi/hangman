<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/hangman_style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="jquery/jquery.js"></script>
  <script src="js/player_stats.js"></script>

</head>

<body>


  <?php

  function showWinPercentage()
  {

    if (isset($_COOKIE["numberOfGamesPlayed"]) && isset($_COOKIE["numberOfGamesWon"])) {
      if ($_COOKIE["numberOfGamesPlayed"] > 0) {
        echo number_format(($_COOKIE["numberOfGamesWon"] / $_COOKIE["numberOfGamesPlayed"]) * 100);
      } else {
        echo 0;
      }
    } else {
      echo 0;
    }
  }





  ?>


  <div class="stats">
    <?php
    include('nav.php');
    ?>

    <div>
      <input type="image" id="stats-btn" src="./images/leaderboard.png">
    </div>



  </div>



  <div class="modal-container">

    <div class="close-modal"><span id="close-btn" class="material-symbols-outlined">
        close
      </span></div>
    <div class="player-stats">

      <h1 class="label">STATISTICS</h1>



      <ul>

        <li>
          <h3><?php if (isset($_COOKIE["numberOfGamesPlayed"])) {
                echo $_COOKIE["numberOfGamesPlayed"];
              } else echo 0 ?></h3>
          <h4>Played</h4>

        </li>

        <li>
          <h3><?php if (isset($_COOKIE["numberOfGamesWon"])) {
                echo $_COOKIE["numberOfGamesWon"];
              } else echo 0 ?></h3>

          <h4>Won</h4>

        </li>


        <li>
          <h3><?php showWinPercentage(); ?></h3>

          <h4>Win %</h4>

        </li>


        <li>
          <h3><?php if (isset($_COOKIE["currentWinStreak"])) {
                echo $_COOKIE["currentWinStreak"];
              } else echo 0 ?></h3>

          <h4>Current Streak</h4>

        </li>


        <li>
          <h3><?php if (isset($_COOKIE["maxWinStreak"])) {
            echo $_COOKIE["maxWinStreak"];
              } else echo 0 ?></h3>

          <h4>Max Streak</h4>

        </li>



      </ul>

    </div>

  </div>
</body>

</html>