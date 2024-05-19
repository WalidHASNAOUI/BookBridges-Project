<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du Livre</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<header class="header">
<div class="logo-container">
        <img src="logo.png" alt="books Logo" class="logo">
    </div>
    <form class="search-bar" action="books.php" method="GET">
        <input type="text" name="search" placeholder="Rechercher un livre...">
        <button type="submit">Rechercher</button>
    </form>
</header>
<div class="container">
    <?php
    /* Connexion à la base de données sur le serveur tp-epua */
    $conn = @mysqli_connect("tp-epua:3308", "sbaisa", "3h4pqAgf");

    if (mysqli_connect_errno()) {
        echo "<p>Erreur : " . mysqli_connect_error() . "</p>";
    } else {
        mysqli_select_db($conn, "sbaisa");
        mysqli_query($conn, "SET NAMES UTF8");
    }

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $book_id = $_GET['id'];

        // Récupérer les détails du livre
        $sql = "SELECT l.titre, l.image, l.resume, l.annee, l.prix 
                FROM BookBridges_livre l
                WHERE l.id = $book_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            echo "<div class='book-details'>";
            echo "<img src='" . $row['image'] . "' alt='Image'>";
            echo "<h2>" . $row['titre'] . "</h2>";
            echo "<p><strong>Résumé:</strong> " . $row['resume'] . "</p>";
            echo "<p><strong>Année:</strong> " . $row['annee'] . "</p>";
            echo "<p><strong>Prix:</strong> " . $row['prix'] . "€</p>";
            echo "</div>";
        } else {
            echo "<p>Erreur : livre non trouvé.</p>";
        }

    } else {
        echo "<p>Erreur : ID de livre non valide.</p>";
    }

    mysqli_close($conn);
    ?>
</div>
</body>
</html>
