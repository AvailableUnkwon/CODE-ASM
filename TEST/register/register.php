<?php
// Include Database
require '../connect.php';

//Check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $full_name = trim($_POST["fullname"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["phonenumber"]);
    $email = trim($_POST["email"]);

    //Hash password (bật để tránh lỗi)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //Insert data into users table
    $sql = "INSERT INTO users (user_name, password, phone, email) VALUES (?, ?, ?, ?)";
    //Insert data to database
    $stmt = mysqli_prepare($conn, $sql);

    if($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $full_name, $hashed_password, $phone, $email);
        if(mysqli_stmt_execute($stmt)){
            header("refresh:2; url=../login/login.html");
            exit();
        } else{
            echo "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else{
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
?>