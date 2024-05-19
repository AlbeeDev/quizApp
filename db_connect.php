<?php
    $sername="localhost";
    $utetnte="root";
    $password="";
    $dbname = "videofeed";

     // Create connection
    $conn = new mysqli($sername, $utetnte, $password, $dbname);
    if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
    }
?>