<?php 
if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
    header("Location: logout.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-light sticky-top bg-warning control-bg">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav  mb-2 mb-lg-0">
                <a class="nav-link active" aria-current="page" href="home.php"><h5>Home</h5></a>
            </div>
            <form class="navbar-nav me-auto mb-2 mb-lg-0" role="search">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                        Dropdown button
                    </button>
                    <ul class="dropdown-menu" id="theme-selector" aria-labelledby="dropdownMenuButton">
                        <li><button class="dropdown-item bg-danger" type="button" name="bg-danger">Red</button></li>
                        <li><button class="dropdown-item bg-warning" type="button" name="bg-warning">Yellow</button></li>
                        <li><button class="dropdown-item bg-success" type="button" name="bg-success">Green</button></li>
                        <li><button class="dropdown-item bg-primary" type="button" name="bg-primary">Primary</button></li>
                        <li><button class="dropdown-item bg-info" type="button" name="bg-info">Info</button></li>
                        <li><button class="dropdown-item bg-light" type="button" name="bg-light">Light</button></li>
                        <li><button class="dropdown-item bg-dark" type="button" name="bg-dark">Dark</button></li>
                    </ul>
                </div>
            </form>
            
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-dark" type="submit">Search</button>
            </form>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin.php" <?php if($_SESSION["is_admin"]==0) echo "hidden" ?>>Admin</a>
                <a class="nav-link" href="profile.php">Profile</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</nav>