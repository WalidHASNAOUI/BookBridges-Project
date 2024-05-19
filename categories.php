<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Catégories</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<header class="header">
    <div class="logo">BookBridges</div>
    <form class="search-bar" action="books.php" method="GET">
        <input type="text" name="search" placeholder="Rechercher un livre...">
        <button type="submit">Rechercher</button>
    </form>
</header>
<div class="container">
    <h1>Catégories</h1>
    <?php
    /* Connexion à la base de données sur le serveur tp-epua */
    $conn = @mysqli_connect("tp-epua:3308", "sbaisa", "3h4pqAgf");

    if (mysqli_connect_errno()) {
        echo "<p>Erreur : " . mysqli_connect_error() . "</p>";
    } else {
        mysqli_select_db($conn, "sbaisa");
        mysqli_query($conn, "SET NAMES UTF8");
    }

    // Récupérer les catégories
    $sql = "SELECT id, nom FROM BookBridges_categorie ORDER BY nom";
    $result = mysqli_query($conn, $sql);

    // Afficher les catégories sous forme de liens
    echo "<div class='categories'>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='category'>";
        echo "<a href='books.php?category_id=" . $row['id'] . "'>" . $row['nom'] . "</a>";
        echo "</div>";
    }
    echo "</div>";

    mysqli_close($conn);
    ?>
</div>
</body>
</html>
