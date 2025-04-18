<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "project";


$conn = mysqli_connect($servername, $username, $password, $database);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connected successfully to the database: " . $database;
}


mysqli_close($conn);
?>