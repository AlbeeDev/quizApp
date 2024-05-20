<?php 

if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
    header("Location: logout.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-light sticky-top bg-lime control-bg">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav  mb-2 mb-lg-0">
                <a class="nav-link active" aria-current="page" href="home.php"><h3>QuizApp</h3></a>
            </div>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</nav>