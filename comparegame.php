<?php 
  session_start();
  include 'config/config.inc.php';

  print_r($_POST);

  //if there is no loggedID, send the user to the login page
  if(!isset($_SESSION['loggedID'])){
    header('location:/login.php');
    exit;
  }else{
    $userID = $_SESSION['loggedID'];
    $username = $_SESSION['loggedUsername'];
  }

  //SORT PATTERN CHANGE
  //if there is a post changing the sort pattern, declare it here
  if(isset($_POST['sortPattern'])){
    //set the pattern as the session data
    $_SESSION['SortPattern'] = $_POST['sortPattern'];
    $sortDirection = getSortKey($_SESSION["SortPattern"]);
  }else{
    //if there is no POST data, then use the session data
    //create a SortPattern session variable if one does not already exist
    if(!isset($_SESSION["SortPattern"])){
      $_SESSION["SortPattern"] = "nameA";
      $sortDirection = getSortKey($_SESSION["SortPattern"]);
    }else{
      $sortDirection = getSortKey($_SESSION["SortPattern"]);
    }
  }


  //setup database config
  $DB_HOST = $config['DB_HOST'];
  $DB_USERNAME = $config['DB_USERNAME'];
  $DB_PASSWORD = $config['DB_PASSWORD'];
  $DB_DATABASE = $config['DB_DATABASE'];
  $DB_GAMETABLE = $config['DB_GAMETABLE'];

  //now, connect to the database
  $connection = mysqli_connect($DB_HOST,$DB_USERNAME,$DB_PASSWORD)
  or die(mysqli_error($connection));
  $db = mysqli_select_db($connection,$DB_DATABASE) or die(mysqli_error($connection));

  $sql = "SELECT * FROM $DB_GAMETABLE WHERE userID = $userID ORDER BY $sortDirection";
  $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));

  //if the user has no games currently, ignore all game gathering
  if ($result->num_rows != 0){
    //put all the game info into an array
    while($row = mysqli_fetch_array($result)){
      $games[] = array(
        'ID'                 => $row['gameID'],
        'Name'               => $row['gameName'],
        'AchievementsEarned' => $row['gameAchievementCount'],
        'MaxAchievements'    => $row['gameAchievementMax'],
        'PercentageEarned'   => $row['gamePercentageEarned'] . '%',
      );
    }

    //total game values
    $totalAchievementsEarned = 0;
    $totalMaxAchievements = 0;
    $totalAveragePercent;

    foreach ($games as $game){
      $totalAchievementsEarned += $game['AchievementsEarned'];
      $totalMaxAchievements += $game['MaxAchievements'];
    }

    //calculate the average percentage earned
    $totalAveragePercent = round(($totalAchievementsEarned / $totalMaxAchievements) * 100, 2) .'%';
  }

  //now get the other table's games
  //SELECT `games`.*, `users`.`userName` FROM `games` LEFT JOIN `users` ON games.userID = users.userID
  $sql = "SELECT * FROM $DB_GAMETABLE ORDER BY $sortDirection";
  $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));

  //if the user has no games currently, ignore all game gathering
  if ($result->num_rows != 0){
    //put all the game info into an array
    while($row = mysqli_fetch_array($result)){
      $theirGames[] = array(
        'ID'                 => $row['gameID'],
        'Name'               => $row['gameName'],
        'AchievementsEarned' => $row['gameAchievementCount'],
        'MaxAchievements'    => $row['gameAchievementMax'],
        'PercentageEarned'   => $row['gamePercentageEarned'] . '%',
      );
    }

    $theirTotalAchievementsEarned = 0;
    $theirTotalMaxAchievements = 0;
    $theirTotalAveragePercent = 0;

    foreach ($theirGames as $theirGame){
        $theirTotalAchievementsEarned += $theirGame['AchievementsEarned'];
        $theirTotalMaxAchievements += $theirGame['MaxAchievements'];
      }
  
      //calculate the average percentage earned
      $theirTotalAveragePercent = round(($theirTotalAchievementsEarned / $theirTotalMaxAchievements) * 100, 2) .'%';
    }

  //SESSION DATA
  //If any session data is available, show it for the user
  if(isset($_SESSION["Message"])){
    $message = $_SESSION["Message"];
    $status = $_SESSION["MessageType"];
    //once the session data is set, clear it so on refresh it does not show again
    $_SESSION["Message"] = "";
    $_SESSION["MessageType"] = "";
  }

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <title>The Achievement Tracker</title>
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

      <?php if(isset($message)){ ?>
        <div class="mt-2 mb-2 <?php if($status=="Success"){print "text-success";}else{print "text-danger";}?>"><?=$message?></div>
      <?php } ?>
      
      <div class="row">
        <form method="POST" action="index.php" class="col-2">
          <button class="btn btn-primary">Back to Table</button>
        </form>
        <form method="POST" class="col-2">
            <select name="selectedID" onchange="this.form.submit()">
                <option value="1">First</option>
                <option value="2">Second</option>
                <option value="3">Third</option>
                <option value="4">Fourth</option>
                <option value="5">Fifths</option>
            </select>
        </form>
      </div>

        <div class="row mt-3">
            <?php
            if(isset($games)){ ?>
                <div class="col-6">
                    <h3 class="text-muted"><em>Your Achievements</em></h3>
                    <div class="table-limitheight"> 
                        <table class="mt-3 table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>
                                        <form method="POST">
                                            <button class="table-button">Game</button>
                                            <?php if($_SESSION["SortPattern"] == "nameA") { ?>
                                                <input type="hidden" name="sortPattern" value="nameD">
                                                <i class="fas fa-angle-down"></i> 
                                            <?php }else if($_SESSION["SortPattern"] == "nameD"){ ?>
                                                <input type="hidden" name="sortPattern" value="nameA">
                                                <i class="fas fa-angle-up"></i>
                                            <?php }else{ ?>
                                                <input type="hidden" name="sortPattern" value="nameA">
                                            <?php } ?>
                                        </form>
                                    </th>
                                    <th>
                                        <form method="POST">
                                            <button class="table-button">Achievements Earned</button>
                                            <?php if($_SESSION["SortPattern"] == "countA") { ?>
                                                <input type="hidden" name="sortPattern" value="countD">
                                                <i class="fas fa-angle-up"></i> 
                                            <?php }else if($_SESSION["SortPattern"] == "countD"){ ?>
                                                <input type="hidden" name="sortPattern" value="countA">
                                                <i class="fas fa-angle-down"></i>
                                            <?php }else{ ?>
                                                <input type="hidden" name="sortPattern" value="countD">
                                            <?php } ?>
                                        </form>
                                    </th>
                                    <th>
                                        <form method="POST">
                                            <button class="table-button">Achievement Total</button>
                                            <?php if($_SESSION["SortPattern"] == "maxA") { ?>
                                                <input type="hidden" name="sortPattern" value="maxD">
                                                <i class="fas fa-angle-up"></i> 
                                            <?php }else if($_SESSION["SortPattern"] == "maxD"){ ?>
                                                <input type="hidden" name="sortPattern" value="maxA">
                                                <i class="fas fa-angle-down"></i>
                                            <?php }else{ ?>
                                                <input type="hidden" name="sortPattern" value="maxD">
                                            <?php } ?>
                                        </form>
                                    </th>
                                    <th>
                                        <form method="POST">
                                            <button class="table-button">Percentage Earned</button>
                                            <?php if($_SESSION["SortPattern"] == "percentA") { ?>
                                                <input type="hidden" name="sortPattern" value="percentD">
                                                <i class="fas fa-angle-up"></i> 
                                            <?php }else if($_SESSION["SortPattern"] == "percentD"){ ?>
                                                <input type="hidden" name="sortPattern" value="percentA">
                                                <i class="fas fa-angle-down"></i>
                                            <?php }else{ ?>
                                                <input type="hidden" name="sortPattern" value="percentD">
                                            <?php } ?>
                                        </form>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($games as $game){ ?>
                                    <tr>
                                        <td><?=$game['Name']?></td>
                                        <td><?=$game['AchievementsEarned']?></td>
                                        <td><?=$game['MaxAchievements']?></td>
                                        <td><?=$game['PercentageEarned']?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <h3 class="mt-4 text-muted">Total Achievements Earned: <?=$totalAchievementsEarned?> / <?=$totalMaxAchievements?></h3>
                    <h3 class="text-muted">Average Game Completion: <?=$totalAveragePercent?></h3>
                </div>
            <?php } else { ?>
                <div class="col-6 text-muted">
                    <h3><em>You currently have no games right now.</em></h3>
                </div>
            <?php }if(isset($theirGames)){ ?>
                <div class="col-6">
                    <h3 class="text-muted"><em>Their Achievements</em></h3>
                    <div class="table-limitheight"> 
                        <table class="mt-3 table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>
                                        <form method="POST">
                                            <button class="table-button">Game</button>
                                            <?php if($_SESSION["SortPattern"] == "nameA") { ?>
                                                <input type="hidden" name="sortPattern" value="nameD">
                                                <i class="fas fa-angle-down"></i> 
                                            <?php }else if($_SESSION["SortPattern"] == "nameD"){ ?>
                                                <input type="hidden" name="sortPattern" value="nameA">
                                                <i class="fas fa-angle-up"></i>
                                            <?php }else{ ?>
                                                <input type="hidden" name="sortPattern" value="nameA">
                                            <?php } ?>
                                        </form>
                                    </th>
                                    <th>
                                        <form method="POST">
                                            <button class="table-button">Achievements Earned</button>
                                            <?php if($_SESSION["SortPattern"] == "countA") { ?>
                                                <input type="hidden" name="sortPattern" value="countD">
                                                <i class="fas fa-angle-up"></i> 
                                            <?php }else if($_SESSION["SortPattern"] == "countD"){ ?>
                                                <input type="hidden" name="sortPattern" value="countA">
                                                <i class="fas fa-angle-down"></i>
                                            <?php }else{ ?>
                                                <input type="hidden" name="sortPattern" value="countD">
                                            <?php } ?>
                                        </form>
                                    </th>
                                    <th>
                                        <form method="POST">
                                            <button class="table-button">Achievement Total</button>
                                            <?php if($_SESSION["SortPattern"] == "maxA") { ?>
                                                <input type="hidden" name="sortPattern" value="maxD">
                                                <i class="fas fa-angle-up"></i> 
                                            <?php }else if($_SESSION["SortPattern"] == "maxD"){ ?>
                                                <input type="hidden" name="sortPattern" value="maxA">
                                                <i class="fas fa-angle-down"></i>
                                            <?php }else{ ?>
                                                <input type="hidden" name="sortPattern" value="maxD">
                                            <?php } ?>
                                        </form>
                                    </th>
                                    <th>
                                        <form method="POST">
                                            <button class="table-button">Percentage Earned</button>
                                            <?php if($_SESSION["SortPattern"] == "percentA") { ?>
                                                <input type="hidden" name="sortPattern" value="percentD">
                                                <i class="fas fa-angle-up"></i> 
                                            <?php }else if($_SESSION["SortPattern"] == "percentD"){ ?>
                                                <input type="hidden" name="sortPattern" value="percentA">
                                                <i class="fas fa-angle-down"></i>
                                            <?php }else{ ?>
                                                <input type="hidden" name="sortPattern" value="percentD">
                                            <?php } ?>
                                        </form>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($theirGames as $theirGame){ ?>
                                    <tr>
                                        <td><?=$theirGame['Name']?></td>
                                        <td><?=$theirGame['AchievementsEarned']?></td>
                                        <td><?=$theirGame['MaxAchievements']?></td>
                                        <td><?=$theirGame['PercentageEarned']?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <h3 class="mt-4 text-muted">Total Achievements Earned: <?=$theirTotalAchievementsEarned?> / <?=$theirTotalMaxAchievements?></h3>
                    <h3 class="text-muted">Average Game Completion: <?=$theirTotalAveragePercent?></h3>
                </div> 
            <?php } ?>
        </div>   


    </div>
    
    <!-- JS Scripts needed for Bootstrap-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

  </body>
</html>