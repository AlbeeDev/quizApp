<?php 
    session_start();
    include 'db_connect.php';
    if ($conn->connect_error) {
        echo "helo";
        die("Connection failed: " . $conn->connect_error);
    }

    unset($_SESSION["quiz"]);
    

    if(isset($_POST["start"])){
        $quizid = $_POST["id"];

        $sql="select COUNT(question.id) from question
        join quiz q on q.id = question.fk_quiz
        where q.id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i",$quizid);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($max_index); 
            $stmt->fetch();
            if($max_index==0){
                return;
            }
            $_SESSION["quiz"]["max_index"] = $max_index;
        }

        
        
        $sql="select question.id from question
        join quiz q on q.id = question.fk_quiz
        where q.id = ?
        order by rand()";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i",$quizid);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id); 
            while($stmt->fetch()){
                $_SESSION["quiz"]["questions"][] = $id;
            }
        }
        $_SESSION["quiz"]["id"] = $quizid;
        $_SESSION["quiz"]["index"] = 0;
        header("Location: quiz.php");
        exit();
    }

    if(isset($_POST["add"])){
        $quizid = $_POST["id"];
        $_SESSION["quiz"]["id"] = $quizid;

        header("Location: addquestion.php");
        exit();
    }

    if(isset($_POST["create"])){
        $quizname=$_POST["name"];
        $quizlanguage=$_POST["language"];
        $userid=$_SESSION["userid"];
        $sql="insert into quiz(name, language, fk_user)
        values (?, ?, ?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("ssi",$quizname,$quizlanguage,$userid);
        $stmt->execute();
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
        <div class="row">
            <div class="col mt-4">
                <!-- Modal trigger button -->
                <button
                    type="button"
                    class="btn btn-purple text-light btn-lg"
                    data-bs-toggle="modal"
                    data-bs-target="#modalId"
                >
                    Create New Quiz
                </button>
                <div
                    class="modal fade"
                    id="modalId"
                    tabindex="-1"
                    
                    role="dialog"
                    aria-labelledby="modalTitleId"
                    aria-hidden="true"
                >
                    <div
                        class="modal-dialog modal-dialog-scrollable modal-dialog-centered"
                        role="document"
                    >
                        <div class="modal-content">
                            <div class="modal-header bg-dark">
                                <h5 class="modal-title" id="modalTitleId">
                                    Create Quiz
                                </h5>
                                <button
                                    type="button"
                                    class="btn-close bg-light"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>
                            </div>
                            <div class="modal-body bg-dark">
                                <form action="" method="post">
                                    <label for="name" class="form-label ">insert quiz name</label>
                                    <input type="text" class="form-control " name="name" id="name" required>
                                    <label for="language" class="form-label mt-2">insert language of the questions</label>
                                    <input type="text" class="form-control " name="language" id="language" required>
                                    <button class="btn btn-purple text-light mt-4 w-100 border " type="submit" name="create">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <?php 
            
            $sql="select quiz.id, name, language, username from quiz
            join user u on u.id = quiz.fk_user";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->execute();
                $stmt->store_result();
                $rows= $stmt->num_rows;
                if ($rows > 0) {
                    $stmt->bind_result($id,$name,$language,$username); 
                    while($stmt->fetch()){
                        $sql="select COUNT(question.id) from question
                        join quiz q on q.id = question.fk_quiz
                        where q.id = ?";
                        if ($stmt2 = $conn->prepare($sql)) {
                            $stmt2->bind_param("i",$id);
                            $stmt2->execute();
                            $stmt2->store_result();
                            $stmt2->bind_result($max_index); 
                            $stmt2->fetch();
                        }
                        ?>
                        <div class="col col-2 me-5  p-4 bg-dark border border-lime ">
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <h2><?php echo $name ?></h2>
                                <h5>Language: <?php echo $language ?></h5>
                                <p>By <?php echo $username ?></p>
                                <button class="btn btn-purple text-light btn-lg w-100 <?php if($max_index==0) echo "disabled" ?>" type="submit" name="start">Start Quiz</button>
                                <button class="btn btn-purple text-light mt-3 w-100" type="submit" name="add">Add question</button>
                            </form>
                        </div>
                        <?php
                    }
                }
            }

            for ($i=0; $i < 2; $i++) { 
                
            }
            ?>
            <div class="col"></div>
        </div>
    </div>
</body>
</html>