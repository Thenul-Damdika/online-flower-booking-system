<?php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT * FROM users WHERE username = ? AND role = 'admin' LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Admin user not found!";
        }
    } else {
        $error = "Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Online Flower Booking</title>
</head>
<body>

<div>
    <h2>Admin Login</h2>
    <?php if ($error): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <div>
            <label>Username</label><br>
            <input type="text" name="username" required>
        </div><br>
        <div>
            <label>Password</label><br>
            <input type="password" name="password" required>
        </div><br>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>