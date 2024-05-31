<?php

    $host="localhost";
    $utente="root";
    $password="";
    $dbname = "quizapp";

     // Create connection
    $conn = new mysqli($host, $utente, $password, $dbname);
    if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
    }
?>