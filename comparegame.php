<?php 
  session_start();
  include 'config/config.inc.php';


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

    //SELECTED ID CHANGE
    //check if the drop down has changed and then apply it to the dropdown
    if(isset($_POST["selectedID"])){
        //if the post is set, then show the dropdown change
        $_SESSION["selectedID"] = $_POST['selectedID'];
        $selectedID = $_SESSION["selectedID"];
    }else{
        //if the post is not set, check if there is a session value and use that
        if(isset($_SESSION['selectedID'])){
            $selectedID = $_SESSION["selectedID"]; 
        }else{
            //if the session value is not set, then set to default value
            $selectedID = 0;
        }
    }


  //setup database config
  $DB_HOST = $config['DB_HOST'];
  $DB_USERNAME = $config['DB_USERNAME'];
  $DB_PASSWORD = $config['DB_PASSWORD'];
  $DB_DATABASE = $config['DB_DATABASE'];
  $DB_GAMETABLE = $config['DB_GAMETABLE'];
  $DB_USERTABLE = $config['DB_USERTABLE'];

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
        'PercentageEarned'   => $row['gamePercentageEarned'],
      );
    }

    //total game values
    $totalAchievementsEarned = 0;
    $totalMaxAchievements = 0;
    $totalEarnedPercent = 0;
    $totalAveragePercent = 0;

    foreach ($games as $game){
      $totalAchievementsEarned += $game['AchievementsEarned'];
      $totalMaxAchievements += $game['MaxAchievements'];
      $totalAveragePercent += $game['PercentageEarned'];
    }

    //calculate the average and total percentage earned
    $totalEarnedPercent = round(($totalAchievementsEarned / $totalMaxAchievements) * 100, 2) .'%';

    $totalAveragePercent = ( round(($totalAveragePercent / count($games)),2 ) ) . '%';
  }


  //now get the other table's games
  //SELECT `games`.*, `users`.`userName` FROM `games` LEFT JOIN `users` ON games.userID = users.userID

  //if the selected ID is equal to 0, then grab all games.
  //if not, then grab the games from the user
  if($selectedID != 0){
    $sql = "SELECT $DB_GAMETABLE.*, $DB_USERTABLE.`userName` FROM $DB_GAMETABLE LEFT JOIN $DB_USERTABLE ON $DB_GAMETABLE.userID = $DB_USERTABLE.userID WHERE $DB_USERTABLE.userID = $selectedID ORDER BY $sortDirection";
  }else{
    $sql = "SELECT $DB_GAMETABLE.*, $DB_USERTABLE.`userName` FROM $DB_GAMETABLE LEFT JOIN $DB_USERTABLE ON $DB_GAMETABLE.userID = $DB_USERTABLE.userID ORDER BY $sortDirection";
  }
  
  $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));

    //if the user has no games currently, ignore all game gathering
    if ($result->num_rows != 0){
        //put all the game info into an array
        while($row = mysqli_fetch_array($result)){
            $theirGames[] = array(
                'ID'                 => $row['gameID'],
                'Name'               => $row['gameName'],
                'UserName'           => $row['userName'],
                'AchievementsEarned' => $row['gameAchievementCount'],
                'MaxAchievements'    => $row['gameAchievementMax'],
                'PercentageEarned'   => $row['gamePercentageEarned'],
            );
        }

        $theirTotalAchievementsEarned = 0;
        $theirTotalMaxAchievements = 0;
        $theirTotalEarnedPercent = 0;
        $theirTotalAveragePercent = 0;

        foreach ($theirGames as $theirGame){
            $theirTotalAchievementsEarned += $theirGame['AchievementsEarned'];
            $theirTotalMaxAchievements += $theirGame['MaxAchievements'];
            $theirTotalAveragePercent += $theirGame['PercentageEarned'];
        }
  
        $theirTotalEarnedPercent = round(($theirTotalAchievementsEarned / $theirTotalMaxAchievements) * 100, 2) .'%';

        $theirTotalAveragePercent = ( round(($theirTotalAveragePercent / count($theirGames)),2 ) ) . '%';
    }

    //get the users for the dropdown
    $sql= "SELECT DISTINCT $DB_USERTABLE.`userID`, $DB_USERTABLE.`userName` FROM $DB_USERTABLE LEFT JOIN $DB_GAMETABLE ON $DB_USERTABLE.`userID` = $DB_GAMETABLE.`userID` WHERE $DB_GAMETABLE.`userID` IS NOT NULL";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));

    if ($result->num_rows != 0){
        //put the user info into an array
        while($row = mysqli_fetch_array($result)){
            $users[] = array(
                'ID'       => $row['userID'],
                'UserName' => $row['userName'],
            );
        }
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

    <title>The Achievement Tracker - Compare Games</title>
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
            Select User:
            <select name="selectedID" onchange="this.form.submit()">
                <option value="0">All Users</option>
                <?php 
                    if(isset($users)){ 
                        foreach($users as $user) { ?>
                            <option value=<?=$user['ID']?> <?php if($selectedID == $user['ID']){ print "selected" ;}?> ><?=$user['UserName']?></option>
                <?php   } 
                    }  ?>
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
                                        <td><?=$game['PercentageEarned'] . '%'?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <h3 class="mt-4 text-muted">Total Achievements Earned: <?=$totalAchievementsEarned?> / <?=$totalMaxAchievements?> <?='('. $totalEarnedPercent . ')'?></h3>
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
                                        User
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
                                        <td><?=$theirGame['UserName']?></td>
                                        <td><?=$theirGame['AchievementsEarned']?></td>
                                        <td><?=$theirGame['MaxAchievements']?></td>
                                        <td><?=$theirGame['PercentageEarned'] . '%'?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <h3 class="mt-4 text-muted">Total Achievements Earned: <?=$theirTotalAchievementsEarned?> / <?=$theirTotalMaxAchievements?> <?='('. $theirTotalEarnedPercent . ')'?></h3>
                    <h3 class="text-muted">Average Game Completion: <?=$theirTotalAveragePercent?></h3>
                </div> 
            <?php } ?>
        </div>   


    </div>
    
    <!-- JS Scripts needed for Bootstrap-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome Icons-->
    <script src="https://kit.fontawesome.com/fa43b4ba7b.js" crossorigin="anonymous"></script>

  </body>
</html>