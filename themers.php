<?php
if (isset($_POST['theme'])) {
    $theme = $_POST['theme'];
    setcookie('theme', $theme, time() + (86400 * 30), "/");
    $_SESSION['theme'] = $theme;
}
?>