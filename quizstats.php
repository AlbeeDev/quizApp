<?php
session_start();

$totaltime = $_SESSION['quiz']['time'];
$minutes = floor($totaltime / 60);
$seconds = $totaltime % 60;

$totalmistakes = $_SESSION["quiz"]["mistakes"];

include "themers.php";
?>
<!DOCTYPE html>
<html lang="en" class="<?php if(isset($_SESSION["theme"])) echo $_SESSION["theme"] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include 'dependencies.php' ?>
</head>
<body class="background-bg textc1">
    <?php include 'nav.php' ?>
    <div class="container">
        <form action="" method="post">
            <div class="row mt-3">
                <div class="col">
                    <h1 class="text-center">End of quiz stats</h1>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <h2 class="text-center mt-5">Time: </h2>
                    <h2 class="text-center">Mistakes: </h2>
                </div>
                <div class="col">
                    <h2 class="text-center mt-5"><?php 
                    if($seconds<10) {
                        echo $minutes . ":0" . $seconds;
                    } 
                    else {
                        echo $minutes . ":" . $seconds;
                    } 
                    ?></h2>
                    <h2 class="text-center text-danger"><?php echo $totalmistakes ?></h2>
                </div>
            </div>
            <div class="row mt-5 d-flex justify-content-center">
                <div class="col-auto">
                    <h1 class="text-center border-dark primary-bg " style="border-radius: 8px;"><a href="home.php" class="text-decoration-none textc1 p-2" >Back to home</a></h1>
                </div>
            </div>
        </form>
    </div>
</body>
</html>