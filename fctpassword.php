<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("tp-epua:3308", "login", "mdp");
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }
    mysqli_select_db($conn, "login");
    mysqli_set_charset($conn, "utf8");

    $email = mysqli_real_escape_string($conn, $_POST['username']);
    $newPassword = $_POST['new-password'];
    $verifyPassword = $_POST['verify-password'];

    if ($newPassword !== $verifyPassword) {
        $error = "Passwords do not match";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE BookBridges_utilisateur SET motdepasse='$hashedPassword' WHERE mail='$email'";

        if ($conn->query($sql) === TRUE) {
            $message = "Password changed successfully";
        } else {
            $error = "Error updating password: " . $conn->error;
        }
    }

    $conn->close();
}
?>
