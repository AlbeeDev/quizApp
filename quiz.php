<?php
    session_start();
    include "db_connect.php";

    if(isset($_POST["skip"])){
        if($_SESSION["quiz"]["index"]+1<count($_SESSION["quiz"]["questions"])){
            $_SESSION["quiz"]["index"]++;
        }
        else{
            header("Location: home.php");
            exit();
        }
    }
    if(isset($_POST["confirm"])){
        $selected_answers = isset($_POST['answers']) ? $_POST['answers'] : [];
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

    //echo "DEBUG: questions ";

    foreach ($_SESSION["quiz"]["questions"] as $questionId) {
        //echo $questionId;
    }
    $cur_question = $_SESSION["quiz"]["questions"][$_SESSION["quiz"]["index"]];
    //echo " current question: ".$cur_question;

    

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
                <div id="progress-bar-container">
                <div id="progress-bar"></div>
            </div>
                <div class="mt-3">Question <?php echo ($_SESSION["quiz"]["index"]+1)." of ".count($_SESSION["quiz"]["questions"]) ?></div>
                <?php if(!isset($_POST["confirm"])){ ?>
                <form action="" method="post">
                    <?php 
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
                        $sql="select image from question
                        where id = ?";
                        $stmt->prepare($sql);
                        $stmt->bind_param("i",$qid);
                        $stmt->execute();
                        $stmt->bind_result($imageData);
                        $stmt->fetch();

                        if($imageData!=null){
                            $base64ImageData = base64_encode($imageData);
                            ?>
                            <img class="d-flex justify-content-center" src="data:image;base64,<?php echo $base64ImageData ?>" class="img-fluid rounded-top" style="max-width: 250px;"><br>
                            <?php
                        }
                    ?>
                    
                    
                    
                    <?php
                    $alphabet=range('A', 'Z');
                    $sql="select id, text from answer
                    where fk_question = ?
                    order by RAND()";
                    if($stmt->prepare($sql)){
                        $stmt->bind_param("i",$cur_question);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($aid,$atext);
                        $index = 0;
                        while($stmt->fetch()){
                            $_SESSION['quiz']['answer_ids'][$index] = $aid;
                            $_SESSION['quiz']['answers'][$index] = $atext;
                            ?>
                            <div class="btn btn-gray text-light mt-4 p-2">
                                <input type="checkbox" class="me-2" name="answers[]" id="<?php echo $_SESSION['quiz']['answer_ids'][$index] ?>" value="<?php echo $_SESSION['quiz']['answer_ids'][$index] ?>" >
                                <label for="<?php echo $_SESSION['quiz']['answer_ids'][$index] ?>"><h5><?php echo $alphabet[$index] ?>. <?php echo $_SESSION['quiz']['answers'][$index] ?></h5></label>
                            </div> <br>
                            <?php
                            $index++;
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
                        $sql="select image from question
                        where id = ?";
                        $stmt->prepare($sql);
                        $stmt->bind_param("i",$qid);
                        $stmt->execute();
                        $stmt->bind_result($imageData);
                        $stmt->fetch();

                        if($imageData!=null){
                            $base64ImageData = base64_encode($imageData);
                            ?>
                            <img src="data:image;base64,<?php echo $base64ImageData ?>" class="img-fluid rounded-top" style="max-width: 250px;"><br>
                            <?php
                        }
                    ?>
                    <?php
                    $alphabet=range('A', 'Z');
                    $awrongflag=false;
                    for ($index=0; $index < count($_SESSION['quiz']['answer_ids']); $index++) {
                        $aid=$_SESSION['quiz']['answer_ids'][$index];
                        $sql="select is_correct from answer
                        where id = ?";
                        if($stmt->prepare($sql)){
                            $stmt->bind_param("i",$aid);
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($acorrect);
                            $stmt->fetch();
                        } 
                        ?> 
                        <div class="btn btn-<?php 
                            if($acorrect==1 && in_array($aid, $selected_answers)){ 
                                echo "lime"; 
                            }
                            else if(($firstcon=($acorrect==1 && !in_array($aid, $selected_answers))) || ($secondcon=($acorrect==0 && in_array($aid, $selected_answers)))){
                                if($firstcon==true){
                                    echo "primary"; 
                                }
                                else if($secondcon==true){
                                    echo "danger"; 
                                }
                                
                                if($awrongflag==false){
                                    $_SESSION["quiz"]["questions"][]=$qid;
                                    $awrongflag=true;
                                }
                                
                                
                            }
                            else { 
                                echo "gray"; 
                            } 
                            ?> text-light mt-4 p-2" >
                                <h5><?php echo $alphabet[$index] ?>. <?php echo $_SESSION["quiz"]["answers"][$index] ?></h5>
                            </div> <br>
                            <?php

                    }
                    $awrongflag=true;

                    

                    ?>
                    <button class="btn btn-purple text-light mt-5" type="submit" name="skip">Next</button>
                </form>
                <?php } ?>
            </div>
        </div>
        
    </div>
    <script>
    // JavaScript to handle the progress bar
    function updateProgressBar(currentIndex, totalQuestions) {
        if(currentIndex==0) return;
      const progressBar = document.getElementById('progress-bar');
      const progress = (currentIndex / totalQuestions) * 100;
      progressBar.style.width = progress + '%';
      progressBar.textContent = Math.round(progress) + '%';
    }

    // Assume these values come from your session data
    const currentIndex = <?php echo $_SESSION["quiz"]["index"]; ?>;
    const totalQuestions = <?php echo count($_SESSION["quiz"]["questions"]); ?>;

    // Initial update of the progress bar
    updateProgressBar(currentIndex, totalQuestions);
  </script>
</body>
</html>