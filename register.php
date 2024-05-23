<?php
    session_start();
    if (isset($_POST["register"])) {
        include "db_connect.php";
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}	

        $username = $_POST['username'];
        $email = $_POST["email"];
        $password = password_hash($_POST["pwd"], PASSWORD_DEFAULT);

        $sql = "select 1 from user where username = ? or email = ?";
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $error="This user exists already";
            } else {
                $sql = "insert into user (username, email, password) values (?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("sss", $username, $email, $password);
                    if ($stmt->execute()) {
                        $_SESSION["logged_in"] = true;
                        $_SESSION["email"]=$email;
                        $_SESSION["username"]=$username;

                        header("Location: login.php");
                        exit();
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }
		$conn->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <?php include 'dependencies.php' ?>
</head>
<body class="bg-purple text-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col col-1"><h1 class="text-light">QuizApp</h1></div>
            <div class="col col-6"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col col-md-4">
                <h2 class="mt-5">Register</h2>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" placeholder="Choose a username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter your email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="pwd">Password:</label>
                        <input type="password" class="form-control" id="pwd" placeholder="Create a password" name="pwd" required>
                    </div>
                    <button type="submit" class="btn btn-outline-light mt-4" name="register">Register</button>
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
            <div class="col col-md-4">
                <h2 class="mt-5">Already have an account?</h2>
                <a href="login.php" class="btn btn-outline-light text-decoration-none">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>

