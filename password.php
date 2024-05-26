<?php
session_start();

if (!isset($_SESSION['mail'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "book_bridge";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mail = $_SESSION['lgn'];
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } else {
        $sql = "SELECT motdepasse FROM BookBridges_utilisateur WHERE mail = '$mail'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($current_password, $user['motdepasse'])) {
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql_update = "UPDATE BookBridges_utilisateur SET motdepasse = '$hashed_new_password' WHERE mail = '$mail'";
                if (mysqli_query($conn, $sql_update)) {
                    $success_message = "Password updated successfully. Redirecting to login page...";
                    session_destroy();
                    header("Refresh: 2; url=login.php");
                    exit();
                } else {
                    $error_message = "Error updating password: " . mysqli_error($conn);
                }
            } else {
                $error_message = "Current password is incorrect.";
            }
        } else {
            $error_message = "User not found.";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <img src="./assets/icons/logo.png" alt="Logo">
        <h1>Update Password</h1>
        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="password.php">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <button type="submit">Update Password</button>
            </div>
        </form>
        <a href="categories_books.php">Cancel</a>
    </div>
</body>
</html>
