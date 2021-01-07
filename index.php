<?php 

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

  $sql = "SELECT * FROM $DB_GAMETABLE ORDER BY gameName ASC";
  $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));

  //put all the game info into an array
  while($row = mysqli_fetch_array($result)){
    //there's an additional item that will be added to the array
    //it's the completed percentage of games and will be calculated based on the count and max
    $count = $row['gameAchievementCount'];
    $max = $row['gameAchievementMax'];

    $percentage = round(($count / $max) * 100, 2) .'%';

    $games[] = array(
      'ID'                 => $row['gameID'],
      'Name'               => $row['gameName'],
      'AchievementsEarned' => $row['gameAchievementCount'],
      'MaxAchievements'    => $row['gameAchievementMax'],
      'PercentageEarned'   => $percentage,
    );
  }

  if(isset($games)){
    print_r($games);
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

      <h1>The Achievement Tracker</h1>

      <button class="btn btn-primary">Add Game</button>
      <button class="btn btn-warning">Delete Game</button>

      <?php
        if(isset($games)){ ?> 
          <table class="mt-3 table table-striped table-hover">
            <thead class="thead-dark">
              <tr>
                <th>Game</th>
                <th>Achievements Earned</th>
                <th>Achievement Total</th>
                <th>Percentage Earned</th>
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
        <?php } else { ?>
          <h3 class="mt-5 text-muted"><em>There are currently no games in your tracker. Click the "Add Game" button to add more and start tracking. </em></h3>
        <?php } ?>


    </div>
    
    <!-- JS Scripts needed for Bootstrap-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

  </body>
</html>