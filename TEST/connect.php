<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toysstroedata";

//tao ket noi
$conn = new mysqli($servername, $username, $password, $dbname);

//kiem tra ket noi
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// else{
//     echo("Welcome!");
// }

?>