<?php
session_start(); 
require '../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    // Check if user exists
    $sql = "SELECT user_id, user_name, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt,$user_id, $db_username, $hashed_password);
            mysqli_stmt_fetch($stmt);

            // Check password
            if (password_verify($password, $hashed_password)) {
                // Save data to session
                $_SESSION["user_id"] = $user_id;
                $_SESSION["username"] = $db_username;

                // check admin account
                if ($db_username === 'Admin') {
                    $_SESSION["is_admin"] = true;
                    header("Location: ../admin/user_mgt.php"); 
                } else {
                    $_SESSION["is_admin"] = false;
                    header("Location: ../main/main.html"); 
                }
                exit();
            } else {
                echo "<p style='color:red;'>Invalid password!</p>";
            }
        } else {
            echo "<p style='color:red;'>User not found!</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p style='color:red;'>Error preparing statement: " . mysqli_error($conn) . "</p>";
    }
}

mysqli_close($conn);
?>