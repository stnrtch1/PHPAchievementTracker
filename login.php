<?php 
    session_start();

    //LOGOUT
    //listen for a log out from another page and if so, clear the logged in user
    if(isset($_POST['logout'])){
        session_unset();
        $errorMessage = "You have successfully logged out";
    }

    //LOGIN
    //user hits "login", see if their credientials are correct
    if (isset($_POST['login'])){
        //check if username and password fields were filled
        if ( !(ctype_space($_POST['userName'])) && !(ctype_space($_POST['userPassword'])) && !(empty($_POST['userName'])) && !(empty($_POST['userPassword'])) ){
            //setup database config
            include 'config/config.inc.php';
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

            $username = $_POST['userName'];

            $sql = "SELECT * FROM $DB_USERTABLE WHERE userName = ?";

            //prepare the sql statement
            $stmt = mysqli_prepare($connection,$sql);
            mysqli_stmt_bind_param($stmt,'s',$username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            //check if there is any users that have the username
            if ($result->num_rows == 1){
                //get the user's info from the database
                while($row = mysqli_fetch_array($result)) {
                    //output data of each row
                    $userID = $row['userID'];
                    $password = $row['userPassword'];
                }

                //now check if the two passwords match
                if (password_verify($_POST['userPassword'],$password)){
                    //set the userID in the session and send the user to the index page
                    $_SESSION['loggedID'] = $userID;
                    $_SESSION['loggedUsername'] = $username;
                    $_SESSION['Message'] = "Login Successful. Hello, " . $username;
                    $_SESSION['MessageType'] = "Success";

                    //close the connection
                    mysqli_close($connection);

                    header("Location:/index.php");
                    exit;
                }else{
                    $errorMessage = "Username and/or Password is incorrect!";
                }
            }else{
                $errorMessage = "Username and/or Password is incorrect!";
            }
        }else{
            $errorMessage = "Username and/or Password Field was not filled in!";
        }
        
        //close the connection
        mysqli_close($connection);

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

    <title>The Achievement Tracker - Login</title>
  </head>
  <body class="mt-3">
    <div class="container">

        <h1>The Achievement Tracker</h1>
        <h3 class="text-muted"><em>Login</em></h3>

        <form method="POST" class="mt-3 p-3 bg-light">
            <div class="mb-3">
                <label for="userName" class="form-label">Username</label>
                <input type="text" class="form-control" id="userName" name="userName">
            </div>
            <div class="mb-3">
                <label for="userPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="userPassword" name="userPassword">
            </div>

            <div class="mb-3">
                <?php if(isset($errorMessage)){ ?>
                    <div class="text-danger"><?=$errorMessage?></div>
                <?php } ?>
            </div>

            <button type="submit" name="login" class="btn btn-primary">Login</button>
        </form>

        <form method="POST" action="createaccount.php" >
            <div class="form-text mt-4">Don't have an account? Create one now</div>
            <button type="submit" class="btn btn-primary mt-2">Create Account</button>
        </form>


    </div>
    
    <!-- JS Scripts needed for Bootstrap-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome Icons-->
    <script src="https://kit.fontawesome.com/fa43b4ba7b.js" crossorigin="anonymous"></script>

  </body>
</html>