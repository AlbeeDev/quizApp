<?php 

if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
    header("Location: logout.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-light sticky-top bg-purple control-bg">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav  mb-2 mb-lg-0">
                <a class="nav-link active text-light d-flex flex-row" aria-current="page" href="home.php"><img src="logo.png" class="img-fluid me-2" style="max-width: 45px; max-height: 45;"><h3 class="mt-1">QuizApp</h3></a>
            </div>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-light" href="logout.php">Logout <div class="ms-2 fas fa-sign-out-alt"></div></a>
            </div>
        </div>
    </div>
</nav>