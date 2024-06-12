<?php 
    session_start();
    include 'db_connect.php';
    if ($conn->connect_error) {
        echo "helo";
        die("Connection failed: " . $conn->connect_error);
    }

    unset($_SESSION["quiz"]);

    $userid=$_SESSION["userid"];
    

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

    if(isset($_POST["view"])){
        $quizid = $_POST["id"];
        $_SESSION["quiz"]["id"] = $quizid;

        header("Location: viewquiz.php");
        exit();
    }

    if(isset($_POST["remove"])){
        $quizid = $_POST["id"];

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
                while($stmt->fetch()){
                    $sql="select a.id from answer a
                    join question q on q.id = a.fk_question
                    where q.id= ?";
                    if ($stmt2 = $conn->prepare($sql)) {
                        $stmt2->bind_param("i",$qid);
                        $stmt2->execute();
                        $stmt2->store_result();
                        $rows= $stmt2->num_rows;
                        if ($rows > 0) {
                            $stmt2->bind_result($aid); 
                            while($stmt2->fetch()){
                                $sql="delete from answer
                                where id=?";
                                if ($stmt3 = $conn->prepare($sql)) {
                                    $stmt3->bind_param("i",$aid);
                                    $stmt3->execute();
                                }
                            }
                        }
                    }
                    $sql="delete from question
                    where id=?";
                    if ($stmt2 = $conn->prepare($sql)) {
                        $stmt2->bind_param("i",$qid);
                        $stmt2->execute();
                    }
                }
                
            }
            $sql="delete from quiz
            where id=?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i",$quizid);
                $stmt->execute();
            }
        }
        
    }
    if (isset($_POST['theme'])) {
        $theme = $_POST['theme'];
        setcookie('theme', $theme, time() + (86400 * 30), "/");
        $_SESSION['theme'] = $theme;
    }
?>
<!DOCTYPE html>
<html lang="en" class="<?php if(isset($_SESSION["theme"])) echo $_SESSION["theme"] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'dependencies.php' ?>
</head>
<body class="textc1 background-bg">
    <?php include 'nav.php' ?>
    <div class="container  ">
        <div class="row">
            <div class="col mt-4">
                <div class="modal fade" id="quizmodal" >
                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-dark">
                                <h5 class="modal-title">Create Quiz</h5>
                                <button type="button" class="btn-close accent-bg" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-dark">
                                <form action="" method="post">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Insert quiz name</label>
                                        <input type="text" class="form-control" name="name" id="name" required autocomplete="off">
                                    </div>
                                    <div class="mb-3">
                                        <label for="language" class="form-label">Insert language of the questions</label>
                                        <input type="text" class="form-control" name="language" id="language" required autocomplete="off">
                                    </div>
                                    <button class="primary-btn mt-4 w-100" type="submit" name="create">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 p-2 ">
            <div class="col col-12 col-xxl-3 col-xl-4 col-lg-5 col-md-8 col-sm-12 mt-3 card  me-5 p-2 secondary-bg border primary-border" style="border-radius: 8%;">
                <div class="card-body m-auto d-flex justify-content-center">
                    <div class="row m-auto">
                        <div class="col col-12">
                        <h4 class="text-center">Create New Quiz</h4>
                        </div>
                        <div class="col col-12 d-flex justify-content-center">
                            <button class="btn btn-lg textc1 shadow-none" data-bs-toggle="modal" data-bs-target="#quizmodal"><h1 class="fas fa-plus-circle mt-1"></h1></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            
            $sql="select q.id, q.name, q.language, u.username, null from quiz q
            join user u on u.id = q.fk_user
            where q.fk_user= ?
            union
            select q.id, q.name, q.language, u.username, u2.username from collaborator co
            join quiz q on q.id = co.fk_quiz
            join user u on u.id = q.fk_user
            join user u2 on u2.id = co.fk_user";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i",$userid);
                $stmt->execute();
                $stmt->store_result();
                $rows= $stmt->num_rows;
                if ($rows > 0) {
                    $stmt->bind_result($id,$name,$language,$creator, $collaborator); 
                    while($stmt->fetch()){
                        if (strlen($name) > 20) {
                            $name=substr($name, 0, 20) . "...";
                        }
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
                        <div class="col col-12 col-xxl-3 col-xl-4 col-lg-5 col-md-8 col-sm-12 mt-3 card justify-content-center me-5 p-2 secondary-bg" style="border-radius: 8%;">
                            <form action="" method="post">
                                <div class="card-body">
                                    <input type="hidden" name="id" value="<?php echo $id ?>">
                                    <h4><?php echo $name ?></h4>
                                    <h5>Language: <?php echo $language ?></h5>
                                    <?php
                                    if($_SESSION["username"]==$creator){
                                        ?>
                                        <p>By <?php echo $creator ?></p>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <p>Shared By <?php echo $creator ?></p>
                                        <?php
                                    }
                                    ?>
                                    
                                    <div class="row mt-4">
                                        <div class="col">
                                            <button class="primary-btn-lg w-100 <?php if($max_index==0) echo "disabled" ?>" type="submit" name="start">Start Quiz</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col col-auto">
                                            <div class="accent-btn mt-2 w-100" data-bs-toggle="modal" data-bs-target="#sharemodal"><div class="fas fas fa-user-plus"></div></div>
                                        </div>
                                        <div class="col col-max ">
                                            <button class="accent-btn mt-2 w-100" type="submit" name="view">View Quiz</button>
                                        </div>
                                        <div class="col col-auto">
                                            <div class="btn btn-danger mt-2 w-100" data-bs-toggle="modal" data-bs-target="#deletemodal<?php echo $id ?>" ><div class=" fas fa-trash-alt"></div></div>
                                        </div>
                                        <div class="modal fade" id="deletemodal<?php echo $id ?>" >
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-dark">
                                                        <h5 class="modal-title">Delete Quiz</h5>
                                                        <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body bg-dark">
                                                        <div class="row">
                                                            <h5>This action will delete the quiz and all its questions and answers immediately</h5>
                                                            <h5>Are you sure?</h5>
                                                        </div>
                                                        <div class="row d-flex justify-content-end">
                                                            <div class="col col-auto">
                                                                <div class="btn btn-light bg-light" data-bs-dismiss="modal" aria-label="Close">Cancel</div>
                                                                <button class="btn btn-danger text-light w-30" type="submit" name="remove">Delete</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </form>
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>
    </div>
</body>
</html>