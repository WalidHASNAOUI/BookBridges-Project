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
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $prenom = mysqli_real_escape_string($conn, $_POST['prenom']);
    $pseudo = mysqli_real_escape_string($conn, $_POST['pseudo']);
    $age = intval($_POST['age']);
    $mail = mysqli_real_escape_string($conn, $_POST['mail']);
    $motdepasse = password_hash(mysqli_real_escape_string($conn, $_POST['motdepasse']), PASSWORD_DEFAULT);

    // Check if the email already exists
    $sql_check = "SELECT * FROM BookBridges_utilisateur WHERE mail = '$mail'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $error_message = "Email already exists.";
    } else {
        // Insert the new user
        $sql = "INSERT INTO BookBridges_utilisateur (nom, prenom, pseudo, age, mail, motdepasse) VALUES ('$nom', '$prenom', '$pseudo', $age, '$mail', '$motdepasse')";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Signup successful. Redirecting to login page...";
            header("Refresh: 2; url=login.php");
            exit();
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
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
    <title>Signup</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <img src="./assets/icons/logo.png" alt="Logo">
        <h1>Signup</h1>
        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="signup.php">
            <div class="form-group">
                <label for="nom">First Name</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Last Name</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="pseudo">Username</label>
                <input type="text" id="pseudo" name="pseudo" required>
            </div>
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" required>
            </div>
            <div class="form-group">
                <label for="mail">Email</label>
                <input type="email" id="mail" name="mail" required>
            </div>
            <div class="form-group">
                <label for="motdepasse">Password</label>
                <input type="password" id="motdepasse" name="motdepasse" required>
            </div>
            <div class="form-group">
                <button type="submit">Signup</button>
            </div>
        </form>
        <a href="login.php">Already have an account? Login here</a>
    </div>
</body>
</html>
