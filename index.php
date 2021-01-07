<?php 

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