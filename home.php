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
                    class="btn btn-lime btn-lg"
                    data-bs-toggle="modal"
                    data-bs-target="#modalId"
                >
                    Create New Quiz
                </button>
                <div
                    class="modal fade"
                    id="modalId"
                    tabindex="-1"
                    data-bs-backdrop="static"
                    data-bs-keyboard="false"
                    
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
                                <form action="">
                                    <label for="name" class="form-label ">insert quiz name</label>
                                    <input type="text" class="form-control " name="name" id="name">
                                    <label for="language" class="form-label mt-2">insert language of the questions</label>
                                    <input type="text" class="form-control " name="language" id="language">
                                </form>
                            </div>
                            <div class="modal-footer bg-dark">
                                <button
                                    type="button"
                                    class="btn btn-light"
                                    data-bs-dismiss="modal"
                                >
                                    Close
                                </button>
                                <button type="button" class="btn btn-lime">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Optional: Place to the bottom of scripts -->
                <script>
                    const myModal = new bootstrap.Modal(
                        document.getElementById("modalId"),
                        options,
                    );
                </script>
                
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
                        //echo $name;
                        //$data[$linkid] = $username;
                        ?>
                        <div class="col col-2 p-4 bg-dark border border-lime ">
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <h2><?php echo $name ?></h2>
                                <h5>Language: <?php echo $language ?></h5>
                                <p>By <?php echo $username ?></p>
                                <button class="btn btn-lime btn-lg" type="submit" name="start">Start Quiz</button>
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