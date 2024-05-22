<?php
    session_start();
    include "db_connect.php";

    if(isset($_POST["skip"])){
        if($_SESSION["quiz"]["index"]+1<$_SESSION["quiz"]["max_index"]){
            $_SESSION["quiz"]["index"]++;
        }
        else{
            header("Location: home.php");
            exit();
        }

        echo "updated index ";
    }
    if(isset($_POST["confirm"])){
        $selected_answers = isset($_POST['answers']) ? $_POST['answers'] : [];
        foreach($selected_answers as $answer){
            echo $answer;
        }
    }
    
    $quizid = $_SESSION["quiz"]["id"];
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
                <div>Question <?php echo ($_SESSION["quiz"]["index"]+1)." of ".$_SESSION["quiz"]["max_index"] ?></div>
                <?php if(!isset($_POST["confirm"])){ ?>
                <form action="" method="post">
                    <?php 
                        //$num_q = $_SESSION["num_q"];
                        $sql="select id, text from question
                        where id = ?";
                        if($stmt->prepare($sql)){
                            $stmt->bind_param("i",$cur_question);
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($qid,$qtext);
                            $stmt->fetch();
                        }
                    ?>
                    <h2>- <?php echo $qtext ?></h2>
                    <?php
                    $alphabet=range('A', 'Z');
                    $sql="select id, text from answer
                    where fk_question = ?";
                    if($stmt->prepare($sql)){
                        $stmt->bind_param("i",$cur_question);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($aid,$atext);
                        $alphaidx = 0;
                        while($stmt->fetch()){
                            ?>
                            <div class="btn btn-gray text-light mt-4 p-2">
                                <input type="checkbox" class="me-2" name="answers[]" id="<?php echo $aid ?>" value="<?php echo $aid ?>" >
                                <label for="<?php echo $aid ?>"><h5><?php echo $alphabet[$alphaidx] ?>. <?php echo $atext ?></h5></label>
                            </div> <br>
                            <?php
                            $alphaidx++;
                        }
                    }   

                    ?>
                    <button class="btn btn-purple text-light mt-5" type="submit" name="confirm">Confirm</button>
                    <button class="btn btn-light mt-5 ms-3" type="submit" name="skip">Skip question</button>
                </form>
                <?php }
                else { ?>
                <form action="" method="post">
                    <?php 
                        //$num_q = $_SESSION["num_q"];
                        $sql="select id, text from question
                        where id = ?";
                        if($stmt->prepare($sql)){
                            $stmt->bind_param("i",$cur_question);
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($qid,$qtext);
                            $stmt->fetch();
                        }
                    ?>
                    <h2>- <?php echo $qtext ?></h2>
                    <?php
                    $alphabet=range('A', 'Z');
                    $sql="select id, text, is_correct from answer
                    where fk_question = ?";
                    if($stmt->prepare($sql)){
                        $stmt->bind_param("i",$cur_question);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($aid,$atext, $acorrect);
                        $alphaidx = 0;
                        while($stmt->fetch()){
                            echo isset($_POST["answers"][$aid]);
                            ?>
                            <div class="btn btn-<?php 
                            if($acorrect==1 && in_array($aid, $selected_answers)){ 
                                echo "lime"; 
                            }
                            else if($acorrect==1 && !in_array($aid, $selected_answers)){
                                echo "primary"; 
                            }
                            else if($acorrect==0 && in_array($aid, $selected_answers)){
                                echo "danger"; 
                            }
                            else { 
                                echo "gray"; 
                            } 
                            ?> text-light mt-4 p-2" >
                                <h5><?php echo $alphabet[$alphaidx] ?>. <?php echo $atext ?></h5>
                            </div> <br>
                            <?php
                            $alphaidx++;
                        }
                    }   

                    ?>
                    <button class="btn btn-purple text-light mt-5" type="submit" name="skip">Next</button>
                </form>
                <?php } ?>
            </div>
        </div>
        
    </div>
</body>
</html>