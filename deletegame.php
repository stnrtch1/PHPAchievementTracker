<?php 
  //start session so success/failure messages can be sent
  session_start();

  //if there is no loggedID, send the user to the login page
  if(!isset($_SESSION['loggedID'])){
    header('location:/login.php');
    exit;
  }else{
    $userID = $_SESSION['loggedID'];
    $username = $_SESSION['loggedUsername'];
  }

  //ID CHECK
  //if there is no id selected, revert back to index.php
  if(!isset($_POST['gameID'])){
    $_SESSION['Message'] = "Can't delete a game without selecting one first!";
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

  //DELETE
  //if "delete" was hit, delete the game from the database
  if(isset($_POST['delete'])){
    $sql = "DELETE FROM $DB_GAMETABLE where gameID = ?";

    //prepare the sql statement
    $stmt = mysqli_prepare($connection,$sql);
    mysqli_stmt_bind_param($stmt,'i',$gameID);
    $gameID = $_POST['gameID'];
    mysqli_stmt_execute($stmt);

    //close the database connection
    mysqli_stmt_close($stmt);

    //create a session message and redirect back to the home page
    $_SESSION['Message'] = "The Game has been deleted!";
    $_SESSION['MessageType'] = "Success";

    header("Location:/index.php");
    exit;
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

    <title>The Achievement Tracker - Delete Game</title>
  </head>
  <body class="mt-3">
    <div class="container">

        <div class="row">
          <div class="col-8">
            <h1>The Achievement Tracker</h1>
          </div>
          <div class="col-4">
            <div class="row-logout">            
              <p class="text-muted text-end text-logout"><em>Hello, <?=$username?></em></p>
              <form method="POST" action="login.php">
                <button name="logout" class="btn btn-primary">Log Out</button>
              </form>
            </div>
          </div>
        </div>
        <h3 class="text-muted"><em>Delete Game</em></h3>
      
        <h3 class="mt-3 display-5 text-danger">Are you sure you want to delete this game?</h3>

        <h2 class="mt-3"><?=$game['Name']?></h2>

        <form method="POST" class="mt-4">
          <input type="hidden" name="gameID" value="<?=$game['ID']?>">
          <button type="submit" name="delete" class="btn btn-danger">Yes, Delete It</button>
          <button type="back" name="back" class="btn btn-secondary">No, Go Back</button>
        </form>


    </div>
    
    <!-- JS Scripts needed for Bootstrap-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

  </body>
</html>