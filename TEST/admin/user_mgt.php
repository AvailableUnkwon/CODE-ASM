<?php
session_start();
require '../connect.php';

// check if user is admin
if (!isset($_SESSION["username"]) || $_SESSION["username"] !== "Admin") {
    echo "<p style='color:red; text-align:center;'>You do not have permission to access this page!</p>";
    exit();
}


// Handle adding and updating users
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_user'])) {
    $id = $_POST['user_id'] ?? '';
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    if ($id) {
        // Update User 
        $stmt = $conn->prepare("UPDATE users SET user_name=?, phone=?, email=? WHERE user_id=?");
        $stmt->bind_param("sssi", $username, $phone, $email, $id);
    } else {
        // Add new user (password hash)
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (user_name, password, phone, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $phone, $email);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Save successfully!'); window.location='user_mgt.php';</script>";
    } else {
        echo "<script>alert('Eror: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Delete successfully!'); window.location='user_mgt.php';</script>";
    } else {
        echo "<script>alert('Eror: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// get user list
$result = $conn->query("SELECT user_id, user_name, phone, email FROM users");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="container">
    <h2>User Management</h2>

    <!-- Form thêm / sửa người dùng -->
    <form method="POST">
        <input type="hidden" name="user_id" id="user_id">
        <input type="text" name="username" id="username" placeholder="Username" required>
        <input type="text" name="phone" id="phone" placeholder="Phone number" required>
        <input type="text" name="email" id="email" placeholder="Email" required>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <button type="submit" name="save_user" class="btn">Save</button>
    </form>

    <!-- Bảng danh sách người dùng -->
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Phone number</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= $row['user_name'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['email'] ?></td>
            <td>
                <button class="btn edit-btn" onclick="editUser('<?= $row['user_id'] ?>', '<?= $row['user_name'] ?>', '<?= $row['phone'] ?>', '<?= $row['email'] ?>')">✏ Edit</button>
                <a href="?delete=<?= $row['user_id'] ?>" class="btn delete-btn" onclick="return confirm('Confirm to delete?');">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="buttons">
        <a href="admin.php">Back</a>
    </div>
</div>

<script>
    function editUser(id, username, phone, address) {
        document.getElementById("user_id").value = id;
        document.getElementById("username").value = username;
        document.getElementById("phone").value = phone;
        document.getElementById("email").value = email;
        document.getElementById("password").removeAttribute("required");
    }
</script>

</body>
</html>

<?php $conn->close(); ?>
