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
    $games[] = array(
      'ID'   =>               $row['gameID'],
      'Name' =>               $row['gameName'],
      'AchievementsEarned' => $row['gameAchievementCount'],
      'MaxAchievments' =>     $row['gameAchievementMax'],
    );
  }

  print_r($games);

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
          <tr>
            <td>Crash Bandicoot 3</td>
            <td>26</td>
            <td>26</td>
            <td>100%</td>
          </tr>
          <tr>
            <td>Spyro 3</td>
            <td>5</td>
            <td>50</td>
            <td>10%</td>
          </tr>
          <tr>
            <td>Payday 2</td>
            <td>305</td>
            <td>1000</td>
            <td>30.5%</td>
          </tr>
        </tbody>
      </table>



    </div>
    
    <!-- JS Scripts needed for Bootstrap-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

  </body>
</html>