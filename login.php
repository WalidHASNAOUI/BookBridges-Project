<?php
session_start();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mail = mysqli_real_escape_string($conn, $_POST['mail']);
    $password = mysqli_real_escape_string($conn, $_POST['motdepasse']);

    $sql = "SELECT * FROM BookBridges_utilisateur WHERE mail = '$mail'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        // Assuming passwords are stored hashed
        if (password_verify($password, $user['motdepasse'])) { //password_verify($password, $user['motdepasse']
            $_SESSION['mail'] = $user['prenom'] . " " . $user['nom'];
            $_SESSION['lgn'] = $user["mail"]; 
            $_SESSION["id"] = $user["id"];
            header("Location: categories_books.php?category_id=1");
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Invalid email or password.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <img src="./assets/icons/logo.png" alt="Logo">
        <h1>Login</h1>
        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="mail">Email</label>
                <input type="email" id="mail" name="mail" required>
            </div>
            <div class="form-group">
                <label for="motdepasse">Password</label>
                <input type="password" id="motdepasse" name="motdepasse" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
        <a href="./signup.php">Vous n'avez pas de compte ? Inscrivez-vous ici</a>

    </div>
</body>
</html>
