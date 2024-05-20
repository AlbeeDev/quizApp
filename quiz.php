<?php
    session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include 'dependencies.php' ?>
</head>
<body class="bg-dark text-light">
    <?php include 'nav.php' ?>
    <div class="container">
        <div class="row mt-3">
            <div class="col">
                <h1 class="text-center">TestQuiz</h1>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col">
                <form action="" method="post">
                    <h2>- Question?</h2>
                    <div class="btn btn-secondary mt-4 p-2"">
                        <input type="checkbox" class=  name="answer" id="answer1" >
                        <label for="answer1"><h5>A. kys</h5></label>
                    </div> <br>
                    <div class="btn btn-secondary mt-4 p-2"">
                        <input type="checkbox" class=  name="answer" id="answer2" >
                        <label for="answer2"><h5>A. kys</h5></label>
                    </div> <br>
                    <div class="btn btn-secondary mt-4 p-2"">
                        <input type="checkbox" class=  name="answer" id="answer3" >
                        <label for="answer3"><h5>A. kys</h5></label>
                    </div> <br>
                    <div class="btn btn-secondary mt-4 p-2"">
                        <input type="checkbox" class=  name="answer" id="answer4" >
                        <label for="answer4"><h5>A. kys</h5></label>
                    </div> <br>
                    <button class="btn btn-lime mt-5">Confirm</button>
                </form>
            </div>
        </div>
        
    </div>
</body>
</html>