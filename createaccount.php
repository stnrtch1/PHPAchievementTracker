<?php
    session_start();

    //CREATE
    //user hits "create account", check credentials and make the account if they work
    if(isset($_POST['create'])){
        //check if username and password fields were filled
        if ( !(ctype_space($_POST['userName'])) && !(ctype_space($_POST['userPassword'])) && !(ctype_space($_POST['userPasswordCheck'])) && !(empty($_POST['userName'])) && !(empty($_POST['userPassword'])) && !(empty($_POST['userPasswordCheck'])) ){
            //check if password field passes the minimum length of 8
            if (strlen($_POST['userPassword']) >= 8){
                //check if both the password and passwordCheck fields are the same
                if($_POST['userPassword'] == $_POST['userPasswordCheck']){
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

                    //check if there are any users in the data base that already have the username
                    //if they do, tell the user this
                    if($result->num_rows == 0){
                        //if we reach here, then everything is good!
                        //insert the user into the database
                        $sql = "INSERT INTO $DB_USERTABLE VALUES (null,?,?)";
                        $stmt = mysqli_prepare($connection,$sql);
                        mysqli_stmt_bind_param($stmt,'ss',$username,$passwordHashed);
                        $passwordHashed = password_hash($_POST['userPassword'],PASSWORD_DEFAULT);
                        mysqli_stmt_execute($stmt);

                        //now get the user from the table
                        $sql = "SELECT * FROM $DB_USERTABLE WHERE userName = ?";

                        //prepare the sql statement
                        $stmt = mysqli_prepare($connection,$sql);
                        mysqli_stmt_bind_param($stmt,'s',$username);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        while($row = mysqli_fetch_array($result)) {
                            //output data of each row
                            $userID = $row['userID'];
                        }

                        //set the userID in the session and send the user to the index page
                        $_SESSION['loggedID'] = $userID;
                        $_SESSION['loggedUsername'] = $username;
                        $_SESSION['Message'] = "Account Creation Successful! Welcome, " . $username . "!";
                        $_SESSION['MessageType'] = "Success";

                        //close the connection
                        mysqli_close($connection);

                        header("Location:/index.php");
                        exit;
        

                    }else{
                        $errorMessage = "This username is already taken!";
                    }
                }else{
                    $errorMessage = "Your password fields don't match! Did you forget it already?";
                }
            }else{
                $errorMessage = "Password isn't long enough! Add a couple more characters to it.";
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

    <title>The Achievement Tracker - Create Account</title>
  </head>
  <body class="mt-3">
    <div class="container">

        <h1>The Achievement Tracker</h1>
        <h3 class="text-muted"><em>Create Account</em></h3>

        <form method="POST" class="mt-3 p-3 bg-light">
            <div class="mb-3">
                <label for="userName" class="form-label">Username</label>
                <input type="text" class="form-control" id="userName" name="userName" aria-describedby="userNameHelp">
                <div id="userNameHelp" class="form-text">Your username for the app, make it unique. Make it you.</div>
            </div>
            <div class="mb-3">
                <label for="userPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="userPassword" name="userPassword" aria-describedby="userPasswordHelp">
                <div id="userPasswordHelp" class="form-text">Your password, don't share this with anyone. I mean it, you don't want someone ruining your stuff.</div>
            </div>
            <div class="mb-3">
                <label for="userPasswordCheck" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="userPasswordCheck" name="userPasswordCheck" aria-describedby="userPasswordCheckHelp">
                <div id="userPasswordCheckHelp" class="form-text">This field has got to be the same as the other field. Have to make sure you know your own password.</div>
            </div>

            <ul>
                <label>Password Requirements:</label>
                <li>Needs to be at least 8 characters long</li>
                <li>That's kind of it now that I think about it. Up to you on how complex you want your own password to be.</li>
            </ul>

            <div class="mb-3">
                <?php if(isset($errorMessage)){ ?>
                    <div class="text-danger"><?=$errorMessage?></div>
                <?php } ?>
            </div>

            <button type="submit" name="create" class="btn btn-primary">Create Account</button>
        </form>

        <form method="POST" action="login.php" >
            <div class="form-text mt-4">Already have an account? Log in now</div>
            <button type="submit" class="btn btn-primary mt-2">Log In</button>
        </form>


    </div>
    
    <!-- JS Scripts needed for Bootstrap-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome Icons-->
    <script src="https://kit.fontawesome.com/fa43b4ba7b.js" crossorigin="anonymous"></script>

  </body>
</html>