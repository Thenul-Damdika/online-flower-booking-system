<?php
session_start();

require_once "../config/database.php";

$message = "";
$success = "";

// Set maximum allowed admin accounts
$max_admin_limit = 3;

// Process registration form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $email === "" || $password === "") {
        $message = "All fields are required!";
    } else {
        // Check current admin count
        $count_query = $conn->query("SELECT COUNT(*) AS total_admins FROM admins");
        $count_data  = $count_query->fetch_assoc();
        $total_admins = (int)($count_data["total_admins"] ?? 0);

        if ($total_admins >= $max_admin_limit) {
            $message = "Registration closed! Maximum limit of {$max_admin_limit} admins reached.";
        } else {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ? LIMIT 1");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "Username or Email already exists!";
            } else {
                // Hash password and insert new admin record
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("sss", $username, $email, $hashed_password);

                if ($insert_stmt->execute()) {
                    $success = "Admin account created successfully! You can now login.";
                } else {
                    $message = "Something went wrong. Please try again!";
                }
            }
        }
    }
}

require_once "../includes/header.php";
?>


<link rel="stylesheet" href="../css/style.css">


<style>
.admin-login-page {
    min-height: calc(100vh - 160px);
    padding: 60px 20px;
    background: var(--bg-light, #fffaf7);
    display: flex;
    align-items: center;
    justify-content: center;
}

.admin-login-container {
    width: 100%;
    max-width: 1050px;
    margin: 0 auto;
}

.admin-auth-wrapper {
    width: 100%;
    min-height: 580px;
    display: flex;
    flex-wrap: wrap;
    background: var(--white, #ffffff);
    border-radius: var(--radius-large, 22px);
    overflow: hidden;
    box-shadow: var(--shadow-medium, 0 15px 50px rgba(70, 45, 45, 0.12));
}

.admin-auth-image {
    flex: 1 1 45%;
    min-width: 300px;
    min-height: 280px;
    position: relative;
    background: linear-gradient(rgba(60, 35, 40, 0.35), rgba(60, 35, 40, 0.55)), url("../images/login-flower.jpg");
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: flex-end;
    padding: 45px;
    box-sizing: border-box;
}

.admin-auth-image-content {
    position: relative;
    z-index: 2;
    color: #ffffff;
}

.admin-auth-image-content h2 {
    margin: 0 0 15px;
    font-family: "Playfair Display", serif;
    font-size: 38px;
    color: #ffffff;
}

.admin-auth-image-content p {
    margin: 0;
    font-size: 15px;
    line-height: 1.8;
    color: rgba(255, 255, 255, 0.92);
}

.admin-auth-form {
    flex: 1 1 55%;
    min-width: 300px;
    padding: 60px 65px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: var(--white, #ffffff);
    box-sizing: border-box;
}

.admin-auth-logo {
    text-align: center;
    margin-bottom: 30px;
}

.admin-auth-logo .flower-icon {
    display: block;
    font-size: 42px;
    margin-bottom: 8px;
}

.admin-auth-logo h1 {
    margin: 0 0 8px;
    font-family: "Playfair Display", serif;
    font-size: 32px;
    color: var(--text, #294535);
}

.admin-auth-logo p {
    margin: 0;
    font-size: 14px;
    color: var(--text-light, #706b68);
}

.admin-form-group {
    margin-bottom: 20px;
    width: 100%;
}

.admin-form-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #292624;
}

.admin-input-control {
    display: block !important;
    width: 100% !important;
    height: 50px !important;
    padding: 0 15px !important;
    border: 1px solid var(--border, #e5ddd9) !important;
    border-radius: var(--radius-small, 10px) !important;
    background: var(--white, #ffffff) !important;
    color: var(--text, #292624) !important;
    font-size: 14px !important;
    outline: none !important;
    box-sizing: border-box !important;
}

.admin-input-control:focus {
    border-color: var(--primary, #b85c70) !important;
    box-shadow: 0 0 0 3px rgba(184, 92, 112, 0.12) !important;
}

.admin-btn-submit {
    display: block !important;
    width: 100% !important;
    height: 51px !important;
    border: none !important;
    border-radius: var(--radius-small, 10px) !important;
    background: var(--primary, #b85c70) !important;
    color: var(--white, #ffffff) !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: 0.3s ease !important;
}

.admin-btn-submit:hover {
    background: #963f55 !important;
}

.admin-alert-error {
    background: var(--primary-light, #fde8e8);
    color: var(--primary-dark, #a43f4f);
    border: 1px solid var(--primary, #b85b6c);
    padding: 12px;
    border-radius: var(--radius-small, 8px);
    font-size: 13px;
    margin-bottom: 20px;
    text-align: center;
    font-weight: 600;
}

.admin-alert-success {
    background: #eafaf1;
    color: #27ae60;
    border: 1px solid #27ae60;
    padding: 12px;
    border-radius: var(--radius-small, 8px);
    font-size: 13px;
    margin-bottom: 20px;
    text-align: center;
    font-weight: 600;
}

.admin-auth-bottom {
    margin-top: 25px;
    text-align: center;
    font-size: 14px;
    color: var(--text-light, #706b68);
}

.admin-auth-bottom a {
    margin-left: 4px;
    color: var(--primary, #b85c70);
    font-weight: 700;
    text-decoration: none;
}
</style>

<main class="admin-login-page">
    <div class="admin-login-container">
        <div class="admin-auth-wrapper">

            <div class="admin-auth-image">
                <div class="admin-auth-image-content">
                    <h2>Join the Team</h2>
                    <p>Create an admin account to manage orders, products, and customer inquiries effortlessly.</p>
                </div>
            </div>

            <div class="admin-auth-form">
                
                <div class="admin-auth-logo">
                    <span class="flower-icon">🌸</span>
                    <h1>Bloom Heaven</h1>
                    <p>Create a new Admin Account</p>
                </div>

                <?php if ($message !== ""): ?>
                    <div class="admin-alert-error">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success !== ""): ?>
                    <div class="admin-alert-success">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="admin-form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="admin-input-control" placeholder="Enter username" required autocomplete="off">
                    </div>

                    <div class="admin-form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="admin-input-control" placeholder="admin@bloomheaven.com" required autocomplete="off">
                    </div>

                    <div class="admin-form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="admin-input-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="admin-btn-submit">Register Account</button>
                </form>

                <div class="admin-auth-bottom">
                    Already have an account?
                    <a href="login.php">Login Here</a>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>