<?php
    if(isset($_POST)){
        print_r($_POST);
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

    <title>The Achievement Tracker - Add Game</title>
  </head>
  <body class="mt-3">
    <div class="container">

        <h1>The Achievement Tracker</h1>
        <h3 class="text-muted"><em>Add Game</em></h3>

        <form method="POST" class="mt-3 p-3 bg-light">
            <div class="mb-3">
                <label for="gameName" class="form-label">Game Name</label>
                <input type="text" class="form-control" id="gameName" name="gameName" aria-describedby="gameNameHelp">
                <div id="gameNameHelp" class="form-text">The name for the game, can't have a game without a name... maybe.</div>
            </div>
            <div class="mb-3">
                <label for="gameAchievementsEarned" class="form-label">Achievements Earned</label>
                <input type="number" min="0" value="0" class="form-control" id="gameAchievementsEarned" name="gameAchievementsEarned" aria-describedby="gameAchievementsEarnedHelp">
                <div id="gameAchievementsEarnedHelp" class="form-text">The amount of achievements you earned. If you didn't earn any yet, that's okay. Just keep it at zero.</div>
            </div>
            <div class="mb-3">
                <label for="gameMaxAchievements" class="form-label">Achievements Earned</label>
                <input type="number" min="1" value="1" class="form-control" id="gameMaxAchievements" name="gameMaxAchievements" aria-describedby="gameMaxAchievementsHelp">
                <div id="gameMaxAchievementsHelp" class="form-text">The amount of achievements for the game. Unlike the other section, this one can't be zero.</div>
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