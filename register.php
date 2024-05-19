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
        $isAdmin = 0;

        $sql = "select 1 from User where username = ? or email = ?";
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                echo "This user is already registered.";
            } else {
                $sql = "insert into User (username, email, password, is_admin) values (?, ?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("sssi", $username, $email, $password, $isAdmin);
                    if ($stmt->execute()) {
                        echo "success!";
                        //echo '<script>document.location.href=\'home.php\'</script>';
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
<body class="bg-dark text-light">
    <div class="container">
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
                    <button type="submit" class="btn btn-warning mt-4" name="register">Register</button>
                </form>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col col-md-4">
                <h2 class="mt-5">Already have an account?</h2>
                <a href="login.php" class="btn btn-warning text-decoration-none">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>

