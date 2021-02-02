<?php
    //start session so success/failure messages can be sent
    session_start();

    //GO BACK
    //if "Go Back" was hit, return to index.php
    if(isset($_POST['back'])){
        header('location:/index.php');
        exit;
    }

    //if there is no loggedID, send the user to the login page
    if(!isset($_SESSION['loggedID'])){
        header('location:/login.php');
        exit;
    }else{
        $userID = $_SESSION['loggedID'];
        $username = $_SESSION['loggedUsername'];
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
    //if "Submit" was hit, check all fields and then proceed to add the game
    if(isset($_POST['submit'])){
        //check all inputs if they are good

        //is game name empty or not?
        if($_POST['gameName'] != ""){
            //is the max achievements field greater or equal to the achievements earned field
            if($_POST['gameMaxAchievements'] >= $_POST['gameAchievementsEarned']){
                //everything is all good, prepare the sql statement
                $sql = "INSERT INTO $DB_GAMETABLE VALUES (null,?,?,?,?,?)";
                $stmt = mysqli_prepare($connection,$sql);
                mysqli_stmt_bind_param($stmt,"isiid",$userID,$gameName,$achievementsEarned,$maxAchievements,$percentageEarned);
                $gameName = $_POST['gameName'];
                $achievementsEarned = $_POST['gameAchievementsEarned'];
                $maxAchievements = $_POST['gameMaxAchievements'];
                $percentageEarned = round(($achievementsEarned / $maxAchievements) * 100, 2);

                //now execute the statement
                mysqli_stmt_execute($stmt);
                //close the database connection
                mysqli_stmt_close($stmt);
                //once done, return back to the index page
                $_SESSION['Message'] = "Game has been added";
                $_SESSION['MessageType'] = "Success";

                header('location:/index.php');
                exit;


            }else{
                $errorMessage = "Might want to double check your numbers, the max number field is smaller than the earned field.";
            }
        }else{
            $errorMessage = "Game Name Field is empty!";
        }
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
        <h3 class="text-muted"><em>Add Game</em></h3>

        <form method="POST" class="mt-3 p-3 bg-light">
            <div class="mb-3">
                <label for="gameName" class="form-label">Game Name</label>
                <input type="text" class="form-control" value="<?php if(isset($_POST['gameName'])){print $_POST['gameName'];}?>" id="gameName" name="gameName" aria-describedby="gameNameHelp">
                <div id="gameNameHelp" class="form-text">The name for the game, can't have a game without a name... maybe.</div>
            </div>
            <div class="mb-3">
                <label for="gameAchievementsEarned" class="form-label">Achievements Earned</label>
                <input type="number" min="0" value="<?php if(isset($_POST['gameAchievementsEarned'])){print $_POST['gameAchievementsEarned'];}else{print 0;}?>" class="form-control" id="gameAchievementsEarned" name="gameAchievementsEarned" aria-describedby="gameAchievementsEarnedHelp">
                <div id="gameAchievementsEarnedHelp" class="form-text">The amount of achievements you earned. If you didn't earn any yet, that's okay. Just keep it at zero.</div>
            </div>
            <div class="mb-3">
                <label for="gameMaxAchievements" class="form-label">Max Achievements</label>
                <input type="number" min="1" value="<?php if(isset($_POST['gameMaxAchievements'])){print $_POST['gameMaxAchievements'];}else{print 1;}?>" class="form-control" id="gameMaxAchievements" name="gameMaxAchievements" aria-describedby="gameMaxAchievementsHelp">
                <div id="gameMaxAchievementsHelp" class="form-text">The amount of achievements for the game. Unlike the other section, this one can't be zero.</div>
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