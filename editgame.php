<?php 
    //start session so success/failure messages can be sent
    session_start();

    //ID CHECK
    //if there is no id selected, revert back to index.php
    if(!isset($_POST['gameID'])){
      $_SESSION['Message'] = "Can't edit a game without selecting one first!";
      $_SESSION['MessageType'] = "Failure";
      header('location:/index.php');
      exit;
    }

    //GO BACK
    //if "Go Back" was hit, return to index.php
    if(isset($_POST['back'])){
        header('location:/index.php');
        exit;
    }

    //setup database config
    include 'config/config.inc.php';
    $DB_HOST = $config['DB_HOST'];
    $DB_USERNAME = $config['DB_USERNAME'];
    $DB_PASSWORD = $config['DB_PASSWORD'];
    $DB_DATABASE = $config['DB_DATABASE'];
    $DB_GAMETABLE = $config['DB_GAMETABLE'];

    //now, connect to the database
    $connection = mysqli_connect($DB_HOST,$DB_USERNAME,$DB_PASSWORD)
    or die(mysqli_error($connection));
    $db = mysqli_select_db($connection,$DB_DATABASE) or die(mysqli_error($connection));

    //SUBMIT
    //if "submit" was hit, check the number fields and then edit the game data
    if(isset($_POST['submit'])){

      //is the max achievements field greater or equal to the achievements earned field
      if($_POST['gameMaxAchievements'] >= $_POST['gameAchievementsEarned']){
        //everything is all good, prepare the sql statement
        $sql = "UPDATE $DB_GAMETABLE SET gameAchievementCount=?,gameAchievementMax=? WHERE gameID = ?";
        $stmt = mysqli_prepare($connection,$sql);
        mysqli_stmt_bind_param($stmt,"iii",$achievementsEarned,$maxAchievements,$gameID);
        $achievementsEarned = $_POST['gameAchievementsEarned'];
        $maxAchievements = $_POST['gameMaxAchievements'];
        $gameID = $_POST['gameID'];

        //now execute the statement
        mysqli_stmt_execute($stmt);
        //once done, return back to the index page
        $_SESSION['Message'] = "Game has been edited";
        $_SESSION['MessageType'] = "Success";

        header('location:/index.php');
        exit;

      }else{
          $errorMessage = "Might want to double check your numbers, the max number field is smaller than the earned field.";
      }

    }



    //INITIAL PAGE SETUP
    //set up the page for when first being load up
    $id = $_POST['gameID'];

    //get the game data so it can be placed in the fields for editings
    $sql = "SELECT * FROM $DB_GAMETABLE WHERE gameID = ?";
    $stmt = mysqli_prepare($connection,$sql);
    mysqli_stmt_bind_param($stmt,"i",$id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while($row = mysqli_fetch_array($result)){
      $game = array(
        'ID'                 => $row['gameID'],
        'Name'               => $row['gameName'],
        'AchievementsEarned' => $row['gameAchievementCount'],
        'MaxAchievements'    => $row['gameAchievementMax'],
      );
    };  



?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <title>The Achievement Tracker - Edit Game</title>
  </head>
  <body class="mt-3">
    <div class="container">

        <h1>The Achievement Tracker</h1>
        <h3 class="text-muted"><em>Edit Game</em></h3>

        <form method="POST" class="mt-3 p-3 bg-light">
            <input type="hidden" id="gameID" name="gameID" value="<?=$game['ID']?>">
            <div class="mb-3">
                <label for="gameName" class="form-label">Game Name</label>
                <input type="text" class="form-control" value="<?=$game['Name']?>" id="gameName" name="gameName" aria-describedby="gameNameHelp" disabled>
                <div id="gameNameHelp" class="form-text">The name for the game, this can't be edited.</div>
            </div>
            <div class="mb-3">
                <label for="gameAchievementsEarned" class="form-label">Achievements Earned</label>
                <input type="number" min="0" value="<?=$game['AchievementsEarned']?>" class="form-control" id="gameAchievementsEarned" name="gameAchievementsEarned" aria-describedby="gameAchievementsEarnedHelp">
                <div id="gameAchievementsEarnedHelp" class="form-text">The amount of achievements you earned. If you still didn't earn any yet, you are probably doing something wrong.</div>
            </div>
            <div class="mb-3">
                <label for="gameMaxAchievements" class="form-label">Max Achievements</label>
                <input type="number" min="1" value="<?=$game['MaxAchievements']?>" class="form-control" id="gameMaxAchievements" name="gameMaxAchievements" aria-describedby="gameMaxAchievementsHelp">
                <div id="gameMaxAchievementsHelp" class="form-text">The amount of achievements for the game. This one still can't be zero.</div>
            </div>

            <div class="mb-3">
                <?php if(isset($errorMessage)){ ?>
                    <div class="text-danger"><?=$errorMessage?></div>
                <?php } ?>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            <button type="back" name="back" class="btn btn-secondary">Go Back</button>
        </form>

    
    </div>
    
    <!-- JS Scripts needed for Bootstrap-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

  </body>
</html>