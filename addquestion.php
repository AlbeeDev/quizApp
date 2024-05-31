<?php
session_start();
include "db_connect.php";

$quizid=$_SESSION["quiz"]["id"];
if(isset($_POST["add"])){
    $question = $_POST['question'];

    $imageData=null;
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $imageName = $image['name'];
        $imageType = $image['type'];
        $imageData = file_get_contents($image['tmp_name']);
        
    }

    //insert question and image
    $sql="insert into question(text, image, fk_quiz) 
    values (?, ?, ?)";
    if($stmt=$conn->prepare($sql)){
        $stmt->bind_param("ssi",$question, $imageData, $quizid);
        $stmt->execute();
    }
    $questionId = $stmt->insert_id;
    $stmt->close();

    $answers = $_POST['answers'];
    $correct = $_POST['correct'];
    
    foreach ($answers as $index => $answer) {
        $isCorrect = $correct[$index] === 'true' ? 1 : 0;
        $sql="insert into answer(text, is_correct, fk_question) 
        values (?, ?, ?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("sii",$answer,$isCorrect,$questionId);
        $stmt->execute();
        $stmt->close();
    }

    
}

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
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col col-6">
                    <label for="question">
                        <h4>Insert question:</h4>
                    </label><br>
                    <input type="text" class="form-control " name="question" id="question" required autocomplete="off"> <br>
                </div>
                <div class="col col-6">
                    <h4>Insert image (optional)</h4>
                    <label class="btn btn-purple text-light mt-1" for="image">Select</label>
                    <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png">
                    <img id="image-preview" class="img-fluid rounded-top" style="max-width: 250px;">
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div id="answers-container">
                        <div class="input-group answer-field mt-4">
                            <div class="input-group-append">
                                <select class="form-control btn-primary " name="format">
                                    <option value="false">Text</option>
                                    <option value="true">Formula</option>
                                </select>
                            </div>
                            <input type="text" class="form-control" name="answers[]" placeholder="Enter answer" required autocomplete="off">
                            <div class="input-group-append">
                                <select class="form-control btn-primary " name="correct[]">
                                    <option value="false">False</option>
                                    <option value="true">True</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    <div class="input-group mt-5">
                        <button type="button" class="btn btn-primary" id="add-answer">Add Answer</button>
                        <button type="button" class="btn btn-danger" id="remove-answer">Remove Answer</button>
                    </div>

                    <button type="submit" class="btn btn-purple text-light mt-5" name="add">Confirm</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>