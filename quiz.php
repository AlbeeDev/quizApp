<?php
    session_start();
    include "db_connect.php";
    
    $quizid = $_SESSION["quizid"];
    $sql="select name, username from quiz
    join user u on u.id = quiz.fk_user
    where quiz.id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $quizid);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($quizname, $creator); 
        $stmt->fetch();
    }

    echo "questions ";

    foreach ($_SESSION["quiz"]["questions"] as $questionId) {
        echo $questionId;
    }
    $cur_question = $_SESSION["quiz"]["questions"][$_SESSION["quiz"]["index"]];
    echo " current question: ".$cur_question;
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
                <h1 class="text-center"><?php echo $quizname ?></h1>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col">
                <form action="" method="post">
                    <?php 
                        //$num_q = $_SESSION["num_q"];
                        $sql="select text from question
                        where id = ?";
                        if($stmt->prepare($sql)){
                            $stmt->bind_param("i",$cur_question);
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($question);
                            $stmt->fetch();
                        }
                    ?>
                    <h2>- <?php echo $question ?></h2>
                    <div class="btn btn-gray text-light mt-4 p-2">
                        <input type="checkbox" class="me-2" name="answer" id="answer1" >
                        <label for="answer2"><h5>A. answer</h5></label>
                    </div> <br>
                    <div class="btn btn-gray text-light mt-4 p-2">
                        <input type="checkbox" class="me-2" name="answer" id="answer2" >
                        <label for="answer2"><h5>B. answer</h5></label>
                    </div> <br>
                    <div class="btn btn-gray text-light mt-4 p-2">
                        <input type="checkbox" class="me-2" name="answer" id="answer3" >
                        <label for="answer3"><h5>C. answer</h5></label>
                    </div> <br>
                    
                    <div class="btn btn-gray text-light mt-4 p-2">
                        <input type="checkbox" class="me-2" name="answer" id="answer4" >
                        <label for="answer4"><h5>D. answer</h5></label>
                    </div> <br>
                    <button class="btn btn-lime mt-5">Confirm</button>
                </form>
            </div>
        </div>
        
    </div>
</body>
</html>