<?php
    session_start();
    if (isset($_POST["login"])) {
        include "db_connect.php";
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}	

        $email = $_POST["email"];
        $password = $_POST["pwd"];

        $sql = "SELECT username, password, is_admin FROM User WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($username, $storedPassword, $isAdmin); 
                $stmt->fetch();
                echo $password." ". $storedPassword;
                if (password_verify($password, $storedPassword)) {
                    $_SESSION["logged_in"] = true;
                    $_SESSION["email"]=$email;
                    $_SESSION["username"]=$username;
                    $_SESSION["is_admin"]=$isAdmin;

                    header("Location: home.php");
                    exit();

                } else {
                    echo "Invalid password!";
                }
            } else {
                echo "No user found with that email address.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-dark text-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col col-1"><h1 class="text-lime">QuizApp</h1></div>
            <div class="col col-6"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col col-3">
                <h2 class="mt-5">Login</h2>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter your email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="pwd">Password:</label>
                        <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pwd" required>
                    </div>
                    <button class="btn btn-lime text-light mt-4" id="" type="submit" name="login">Submit</button>
                    <?php 
                    if (isset($error)) {
                        ?>
                        <div class="alert alert-danger mt-3" role="alert">
                            <?php echo $error;?>
                        </div>
                        <?php 
                    }
                    ?>
                </form>
            </div>
            
        </div>
        <div class="row justify-content-center">
            <div class="col col-3">
                <h2 class="mt-5">Dont have an account yet?</h2>
                <a href="register.php" class="btn btn-lime text-light">Register here</a>
            </div>
        </div>
        
    </div>
</body>
</html>
