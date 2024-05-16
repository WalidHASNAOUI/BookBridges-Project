<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("tp-epua:3308", "login", "mdp");
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }
    mysqli_select_db($conn, "login");
    mysqli_set_charset($conn, "utf8");

    $email = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT motdepasse FROM BookBridges_utilisateur WHERE mail = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['motdepasse'])) {
            $message = "Connected successfully";
        } else {
            $error = "Unknown user";
        }
    } else {
        $error = "Unknown user";
    }

    $conn->close();
}
?>
