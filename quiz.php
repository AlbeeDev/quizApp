<?php
    session_start();
    include "db_connect.php";

    $userid=$_SESSION["userid"];
    $quizid = $_SESSION["quiz"]["id"];

    if(isset($_POST["continue"])){
        unset($_SESSION['quiz']['answer_ids']);
        unset($_SESSION['quiz']['answer']);
        if($_SESSION["quiz"]["index"]+1<count($_SESSION["quiz"]["questions"])){
            $_SESSION["quiz"]["index"]++;
        }
        else{

            $startTime = $_SESSION['quiz']['start_time'];
            $currentTime = time();
            $quiztime = $currentTime - $startTime;

            $sql="update quizstats
            set completed = (select completed from quizstats where fk_quiz = ? and fk_user=?) + 1
            where fk_quiz = ? and fk_user= ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("iiii",$quizid,$userid,$quizid,$userid);
                $stmt->execute();
            }

            $sql="select completed, avg_time, best_time from quizstats 
            where fk_quiz = ? and fk_user= ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ii",$quizid, $userid);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($completed,$avg_time, $best_time); 
                $stmt->fetch();
            }
            $new_avgtime = 0;
            if($avg_time==0){
                $new_avgtime = $quiztime;
            }
            else{
                $new_avgtime = ($avg_time + $quiztime) / 2;
            }

            
            
            $sql="update quizstats
            set avg_time = ?
            where fk_quiz = ? and fk_user= ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("iii",$new_avgtime,$quizid,$userid);
                $stmt->execute();
            }

            if ($best_time == 0 || $quiztime < $best_time) {
                $sql = "update quizstats
                set best_time = ?
                where fk_quiz = ? and fk_user= ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("iii", $quiztime, $quizid, $userid);
                    $stmt->execute();
                }
            }

            

            $_SESSION['quiz']['time'] = $quiztime;
            header("Location: quizstats.php");
            exit();
        }
    }

    if(isset($_POST["skip"])){
        unset($_SESSION['quiz']['answer_ids']);
        unset($_SESSION['quiz']['answer']);
        $_SESSION["quiz"]["questions"][]=$_POST["qid"];
        $_SESSION["quiz"]["index"]++;
    }
    if(isset($_POST["confirm"])){
        $selected_answers = isset($_POST['answers']) ? $_POST['answers'] : [];
    }
    
    
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

    /*
    echo "DEBUG: questions ";
    foreach ($_SESSION["quiz"]["questions"] as $questionId) {
        echo $questionId;
    }
    */
    $cur_question = $_SESSION["quiz"]["questions"][$_SESSION["quiz"]["index"]];
    //echo " current question: ".$cur_question;


    include "themers.php";
?>
<!DOCTYPE html>
<html lang="en"  class="<?php if(isset($_SESSION["theme"])) echo $_SESSION["theme"] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include 'dependencies.php' ?>
</head>
<body class="background-bg textc1">
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
                    <input type="hidden" name="qid" value="<?php echo $qid ?>">
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
                    $sql="select id, text, image from answer
                    where fk_question = ?
                    order by RAND()";
                    if($stmt->prepare($sql)){
                        $stmt->bind_param("i",$cur_question);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($aid,$atext,$aimage);
                        $index = 0;
                        while($stmt->fetch()){
                            $_SESSION['quiz']['answer_ids'][$index] = $aid;
                            if(!$aimage){
                                $_SESSION['quiz']['answers'][$index] = [
                                    'type' => 'text',
                                    'data' => $atext
                                ];
                            }
                            else{
                                $_SESSION['quiz']['answers'][$index] = [
                                    'type' => 'image',
                                    'data' => $aimage
                                ];
                            }
                            
                            ?>
                            <div class="secondary-btn-lg border mt-4 p-2">
                                <input type="checkbox" class="me-2" name="answers[]" id="<?php echo $_SESSION['quiz']['answer_ids'][$index] ?>" value="<?php echo $_SESSION['quiz']['answer_ids'][$index] ?>" >
                                <label for="<?php echo $_SESSION['quiz']['answer_ids'][$index] ?>">
                                <?php
                                if($_SESSION['quiz']['answers'][$index]['type']==='image'){
                                  
                                ?>
                                <h5><?php echo $alphabet[$index] ?>.</h5><br>
                                <img class="d-flex justify-content-center" src="data:image;base64,<?php echo base64_encode($_SESSION['quiz']['answers'][$index]['data']); ?>" class="img-fluid rounded-top" style="max-width: 350px;min-width: 250px; width: auto;height: auto;">
                                <?php
                                }
                                else{
                                ?>
                                <h5><?php echo $alphabet[$index] ?>. <?php echo $_SESSION['quiz']['answers'][$index]['data'] ?></h5>
                                <?php
                                }
                                ?>
                                </label>
                            </div> <br>
                            <?php
                            $index++;
                        }
                    }   
                    ?>
                    <button class="primary-btn textc1 mt-5" type="submit" name="confirm">Confirm</button>
                    <button class="accent-btn  mt-5 ms-3" type="submit" name="skip">Skip question</button>
                </form>
                <?php }
                else { ?>
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
                            <img src="data:image;base64,<?php echo $base64ImageData ?>" class="img-fluid rounded-top" style="max-width: 250px;"><br>
                            <?php
                        }
                    ?>
                    <?php
                    $alphabet=range('A', 'Z');
                    $awrongflag=false;
                    $acorrecttotal=0;
                    $acorrectcounter=0;
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
                            $acorrecttotal += $acorrect;
                        } 
                        ?> 
                        
                        <div class="btn btn-<?php 
                            if($acorrect==1 && in_array($aid, $selected_answers)){ 
                                echo "lime";
                                $acorrectcounter++; 

                                
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
                                    $_SESSION["quiz"]["mistakes"]+=1;
                                    $awrongflag=true;

                                    $sql="update quizstats
                                    set a_wrong = (select a_wrong from quizstats where fk_quiz = ? and fk_user=?) + 1
                                    where fk_quiz = ? and fk_user= ?";
                                    if ($stmt2 = $conn->prepare($sql)) {
                                        $stmt2->bind_param("iiii",$quizid,$userid,$quizid,$userid);
                                        $stmt2->execute();
                                    }
                                }
                                
                                
                            }
                            else { 
                                echo "gray"; 
                            } 
                            ?> text-light mt-4 p-2" >
                                <?php
                                if($_SESSION['quiz']['answers'][$index]['type']==='image'){
                                  
                                ?>
                                <h5><?php echo $alphabet[$index] ?>.</h5><br>
                                <img class="d-flex justify-content-center" src="data:image;base64,<?php echo base64_encode($_SESSION['quiz']['answers'][$index]['data']); ?>" class="img-fluid rounded-top" style="max-width: 350px;min-width: 250px; width: auto;height: auto;">
                                <?php
                                }
                                else{
                                ?>
                                <h5><?php echo $alphabet[$index] ?>. <?php echo $_SESSION['quiz']['answers'][$index]['data'] ?></h5>
                                <?php
                                }
                                ?>
                            </div> <br>
                            <?php

                    }
                    $awrongflag=true;

                    if($acorrectcounter==$acorrecttotal){
                        $sql="update quizstats
                        set a_right = (select a_right from quizstats where fk_quiz = ? and fk_user=?) + 1
                        where fk_quiz = ? and fk_user= ?";
                        if ($stmt = $conn->prepare($sql)) {
                            $stmt->bind_param("iiii",$quizid,$userid,$quizid,$userid);
                            $stmt->execute();
                        }
                    }
                    

                    ?>
                    <button class="primary-btn textc1 mt-5" type="submit" name="continue">Next</button>
                </form>
                <?php } ?>
            </div>
        </div>
        
    </div>
    <script>
    function updateProgressBar(currentIndex, totalQuestions) {
        if(currentIndex==0) return;
      const progressBar = document.getElementById('progress-bar');
      const progress = (currentIndex / totalQuestions) * 100;
      progressBar.style.width = progress + '%';
      progressBar.textContent = Math.round(progress) + '%';
    }

    const currentIndex = <?php echo $_SESSION["quiz"]["index"]; ?>;
    const totalQuestions = <?php echo count($_SESSION["quiz"]["questions"]); ?>;

    updateProgressBar(currentIndex, totalQuestions);
  </script>
</body>
</html>