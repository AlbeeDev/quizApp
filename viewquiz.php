<?php
session_start();
include "db_connect.php";

$quizid=$_SESSION["quiz"]["id"];

if(isset($_POST["add"])){

    header("Location: addquestion.php");
    exit();
}

if(isset($_POST["remove"])){
    $qid = $_POST["qid"];

    $sql="select a.id from answer a
    join question q on q.id = a.fk_question
    where q.id= ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i",$qid);
        $stmt->execute();
        $stmt->store_result();
        $rows= $stmt->num_rows;
        if ($rows > 0) {
            $stmt->bind_result($aid); 
            $index=1;
            while($stmt->fetch()){
                $sql="delete from answer
                where id=?";
                if ($stmt2 = $conn->prepare($sql)) {
                    $stmt2->bind_param("i",$aid);
                    $stmt2->execute();
                }
            }
        }
    }
    $sql="delete from question
    where id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i",$qid);
        $stmt->execute();
    }

    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'dependencies.php' ?>
</head>
<body class="text-light bg-darkblue">
<?php include 'nav.php' ?>
    <div class="container">
        <div class="row mt-4">
            <form action="" method="post">
                <button class="btn btn-purple text-light" type="submit" name="add">Add question</button>
            </form>
        </div>
        <div class="row mt-4">
            <?php
            $sql="select qe.id from question qe
            join quiz q on q.id = qe.fk_quiz
            where q.id= ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i",$quizid);
                $stmt->execute();
                $stmt->store_result();
                $rows= $stmt->num_rows;
                if ($rows > 0) {
                    $stmt->bind_result($qid); 
                    $index=1;
                    while($stmt->fetch()){
                    ?>
                    <form class="col col-auto" action="" method="post">
                        <input type="hidden" name="qid" value="<?php echo $qid; ?>">
                        <button class="btn btn-lg btn-primary text-light me-2" type="submit" name="display"><?php echo $index; ?></button>
                    </form>
                    <?php
                    $index++;
                    }
                }
            }
            ?>

        </div>
        <?php
        if(isset($_POST["display"])){
            $qid=$_POST["qid"];
        ?>
        <div class="row mt-4">
            <form action="" method="post">
                <input type="hidden" name="qid" value="<?php echo $qid; ?>">
                <button class="btn btn-danger" type="submit" name="remove">Remove question</button>
            </form>
        </div>
        <div class="row mt-5">
            <?php 
                $sql="select text from question
                where id = ?";
                if($stmt->prepare($sql)){
                    $stmt->bind_param("i",$qid);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($qtext);
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
            $sql="select id, text, image from answer
            where fk_question = ?";
            if($stmt->prepare($sql)){
                $stmt->bind_param("i",$qid);
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
                    <div class="text-light mt-4 p-2">
                        
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
        </div>
        <?php
        }
        ?>
    </div>
</body>
</html>