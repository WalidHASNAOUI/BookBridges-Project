<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("tp-epua:3308", "login", "mdp");
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }
    mysqli_select_db($conn, "login");
    mysqli_set_charset($conn, "utf8");

    $firstName = mysqli_real_escape_string($conn, $_POST['first-name']);
    $lastName = mysqli_real_escape_string($conn, $_POST['last-name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $age = (int)$_POST['age'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['new-password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO BookBridges_utilisateur (nom, prenom, pseudo, age, mail, motdepasse) VALUES ('$lastName', '$firstName', '$username', $age, '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        header("Location: https://tp-epua.univ-smb.fr/~dahmanei/BookBridges-Project/genre.php");
        exit(); 
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>


