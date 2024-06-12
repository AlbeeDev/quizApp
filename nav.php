<?php 

if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
    header("Location: logout.php");
    exit();
}


?>
<nav class="navbar navbar-expand-lg sticky-top primary-bg">
        <div class="container-fluid">
            <button class="navbar-toggler shadow-none mb-1 mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="far fa-circle textc1" ></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav mb-2 mb-lg-0">
                    <a class="nav-link textc1 active d-flex flex-row" aria-current="page" href="home.php">
                        <img src="logo.png" class="img-fluid me-2" style="max-width: 45px; max-height: 45;">
                        <h3 class="mt-1">QuizApp</h3>
                    </a>
                </div>
                <div class="navbar-nav ms-auto">
                    <div class="theme-switcher">
                        <span class="nav-link theme-switcher-icon textc1">Themes</span>
                        <div class="theme-switcher-content">
                            <form action="" method="POST" id="theme-form">
                                <button type="submit" name="theme" value="purple-theme" class="btn btn-link text-decoration-none textc1">Purple Theme</button>
                                <button type="submit" name="theme" value="red-theme" class="btn btn-link text-decoration-none textc1">Red Theme</button>
                                <button type="submit" name="theme" value="blue-theme" class="btn btn-link text-decoration-none textc1">Blue Theme</button>
                                <button type="submit" name="theme" value="orange-theme" class="btn btn-link text-decoration-none textc1">Orange Theme</button>
                                <button type="submit" name="theme" value="dark-green-theme" class="btn btn-link text-decoration-none textc1">Green Theme</button>
                                <button type="submit" name="theme" value="dark-theme" class="btn btn-link text-decoration-none textc1">Dark Theme</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="navbar-nav">
                    <a class="nav-link textc1" href="logout.php">Logout <div class="ms-2 fas fa-sign-out-alt"></div></a>
                </div>
                
            </div>
        </div>
    </nav>

    <script>
        const themeSwitcherRed = document.getElementById('themeSwitcherRed');
        const themeSwitcherLimey = document.getElementById('themeSwitcherLimey');
        const themeSwitcherDark = document.getElementById('themeSwitcherDark');

        themeSwitcherRed.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('debug')
            document.documentElement.classList.add('red-theme');
            document.documentElement.classList.remove('limey-theme', 'dark-theme');
        });

        themeSwitcherLimey.addEventListener('click', (e) => {
            e.preventDefault();
            document.documentElement.classList.add('limey-theme');
            document.documentElement.classList.remove('red-theme', 'dark-theme');
        });

        themeSwitcherDark.addEventListener('click', (e) => {
            e.preventDefault();
            document.documentElement.classList.add('dark-theme');
            document.documentElement.classList.remove('red-theme', 'limey-theme');
        });
    </script>