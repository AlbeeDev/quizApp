<?php
session_start();
include "db_connect.php";

$quizid=$_SESSION["quiz"]["id"];



if(isset($_POST["add"])){

    header("Location: addquestion.php");
    exit();
}

if(isset($_POST["remove"])){
    $qid = $_POST["id"];

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

if(isset($_POST["display"])){
    $_SESSION["quiz"]["lastqid"]=$_POST["qid"];
}
?>
<!DOCTYPE html>
<html lang="en"  class="<?php if(isset($_SESSION["theme"])) echo $_SESSION["theme"] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'dependencies.php' ?>
</head>
<body class="textc1 background-bg">
<?php include 'nav.php' ?>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="modal fade" id="deletemodal" >
                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-dark">
                                <h5 class="modal-title">Delete Question</h5>
                                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-dark">
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="">
                                    <div class="row">
                                        <h5>This action will delete the question and all of its answers immediately</h5>
                                        <h5>Are you sure?</h5>
                                    </div>
                                    <div class="row d-flex justify-content-end">
                                        <div class="col col-auto">
                                            <div class="btn btn-light bg-light" data-bs-dismiss="modal" aria-label="Close">Cancel</div>
                                            <button class="btn btn-danger text-light w-30" type="submit" name="remove">Delete</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <form action="" method="post">
                <button class="primary-btn" type="submit" name="add">Add question</button>
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
                        <button class="primary-btn-lg me-2 qbtn <?php 
                        if (isset($_SESSION["quiz"]["lastqid"]) && $_SESSION["quiz"]["lastqid"]==$qid){
                            echo "accent-btn-lg";
                        }
                        else{
                            echo "primary-btn-lg";
                        }
                        ?>" type="submit" name="display"><?php echo $index; ?></button>
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
            <div class="col col-auto">
                <div class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletemodal" data-id="<?php echo $qid ?>">Remove question</div>
            </div>
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
            $sql="select id, text, image, is_correct from answer
            where fk_question = ?";
            if($stmt->prepare($sql)){
                $stmt->bind_param("i",$qid);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($aid,$atext,$aimage,$acorrect);
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
                    <div class="<?php if($acorrect==1){ echo 'text-lime'; } ?>  mt-4 p-2">
                        
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
    <script>
     var myModal = document.getElementById('deletemodal');
    myModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var data = button.getAttribute('data-id'); 
        var modalBodyInput = myModal.querySelector('input[name="id"]');
        console.log(data);
        modalBodyInput.value = data;
    });

</script>
</body>
</html>