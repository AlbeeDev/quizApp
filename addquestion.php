<?php
session_start();
include "db_connect.php";

$quizid=$_SESSION["quiz"]["id"];
if(isset($_POST["add"])){
    $question = $_POST['question'];
    $qformat = $_POST['qformat'];

    $imageData=null;

    if($qformat=="formula"){
        $question = preg_replace_callback('/\d+/', function($matches) {
            $number = $matches[0];
            return "<sub>$number</sub>";
        }, $question);
    }
    
    if (isset($_FILES['question-image']) && $_FILES['question-image']['error'] == 0) {
        $image = $_FILES['question-image'];
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

    $format = $_POST['format'];
    $answers_text = $_POST['answers_text'];
    $answers_image = $_FILES['answers_image'];
    $correct = $_POST['correct'];
    
    foreach ($answers_text as $index => $answer_text) {
        $isCorrect = $correct[$index] === 'true' ? 1 : 0;
        $imageData=null;
        
        

        switch($format[$index]) {
            case 'formula':
                $answer_text = preg_replace_callback('/\d+/', function($matches) {
                    $number = $matches[0];
                    return "<sub>$number</sub>";
                }, $answer_text);
                break;
            case 'image':
    
                if (isset($answers_image['tmp_name'][$index]) && $answers_image['error'][$index] == 0) {
                    $imageData = file_get_contents($answers_image['tmp_name'][$index]);
                }
                break;
            
            default:
                break;
        }


        $sql="insert into answer(text, image, is_correct, fk_question) 
        values (?, ?, ?, ?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("ssii",$answer_text, $imageData,$isCorrect,$questionId);
        $stmt->execute();
        $stmt->close();
    }

    
}

?>
<!DOCTYPE html>
<html lang="en"  class="<?php if(isset($_SESSION["theme"])) echo $_SESSION["theme"] ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'dependencies.php' ?>
    <script src="app.js"></script>
</head>

<body class="background-bg textc1">
    <?php include 'nav.php' ?>
    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row mt-4">
                <div class="col col-12 col-md-6">
                    
                    <label for="question">
                        <h4>Insert question:</h4>
                    </label>
                    <div class="input-group answer-field">
                        <div class="input-group-append">
                            <select class=" primary-btn-lg shadow-none" name="qformat" required>
                                <option value="text">Text</option>
                                <option value="formula">Formula</option>
                            </select>
                        </div>
                        <input type="text" class="form-control " name="question" id="question" required autocomplete="off">
                    </div>
                    
                </div>
                <div class="col col-12 col-md-6 image-container">
                    <h4>Insert image (optional)</h4>
                    <label class="primary-btn mt-1" for="question-image">Select</label>
                    <input type="file" name="question-image" id="question-image" accept=".jpg, .jpeg, .png">
                    <img id="question-preview" class="img-fluid rounded-top" style="max-width: 250px;">
                </div>
            </div>

            <div class="row mt-5">
                <div class="col">
                    <h4>Insert Answers:</h4>
                    <div id="answers-container">
                        
                        <div class="input-group answer-field mt-4">
                            <div class="input-group-append">
                                <select class="primary-btn-lg shadow-none" name="format[]" id="format0" required>
                                    <option value="text">Text</option>
                                    <option value="formula">Formula</option>
                                    <option value="image">Image</option>
                                </select>
                            </div>
                            <input type="text" class="form-control col-12" name="answers_text[]" id="answer0" placeholder="Enter answer" autocomplete="off">
                            <div class="col col-6 col-md-6" id="image-container0" style="display: none;">
                                <label class="accent-btn-lg col-12" for="image-input0">Select Image</label>
                                <input type="file" name="answers_image[]" id="image-input0" accept=".jpg, .jpeg, .png">
                                <img id="image-preview0" class="img-fluid rounded-top m-auto col-12" style="max-width: 250px;">
                            </div>
                            <div class="input-group-append">
                                <select class="form-control btn-lg btn-success shadow-none" name="correct[]" id="correct0">
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    <div class="input-group mt-5">
                        <button type="button" class="btn btn-primary" id="add-answer">Add Answer</button>
                        <button type="button" class="btn btn-danger" id="remove-answer">Remove Answer</button>
                    </div>

                    <button type="submit" class="primary-btn mt-5" name="add">Confirm</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>