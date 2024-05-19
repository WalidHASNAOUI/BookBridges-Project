<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Livres</title>
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
        <input type="hidden" name="category_id" value="<?php echo isset($_GET['category_id']) ? $_GET['category_id'] : ''; ?>">
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

    if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
        $category_id = $_GET['category_id'];

        // Définir le nombre de résultats par page
        $results_per_page = 10;

        // Déterminer la page actuelle
        if (isset($_GET["page"]) && is_numeric($_GET["page"])) {
            $page = $_GET["page"];
        } else {
            $page = 1;
        }

        $start_from = ($page - 1) * $results_per_page;

        // Récupérer les informations de la catégorie
        $sql_category = "SELECT nom FROM BookBridges_categorie WHERE id = $category_id";
        $result_category = mysqli_query($conn, $sql_category);
        $row_category = mysqli_fetch_assoc($result_category);
        echo "<h1>Livres de la catégorie: " . $row_category['nom'] . "</h1>";

        // Rechercher des livres si une recherche est effectuée
        $search_query = "";
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search_query = "AND l.titre LIKE '%" . mysqli_real_escape_string($conn, $_GET['search']) . "%'";
        }

        // Récupérer les livres de la catégorie sélectionnée
        $sql = "SELECT l.id, l.titre, l.image 
                FROM BookBridges_livre l
                JOIN BookBridges_livre_categorie lc ON l.id = lc.id_livre
                WHERE lc.id_categorie = $category_id $search_query
                ORDER BY l.titre
                LIMIT $start_from, $results_per_page";
        $result = mysqli_query($conn, $sql);

        // Afficher les livres sous forme de cartes
        echo "<div class='card-container'>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card'>";
            echo "<a href='details.php?id=" . $row['id'] . "'>";
            echo "<img src='" . $row['image'] . "' alt='Image'>";
            echo "<div class='card-content'>";
            echo "<h3>" . $row['titre'] . "</h3>";
            echo "</div>";
            echo "</a>";
            echo "</div>";
        }
        echo "</div>";

        // Pagination
        $sql_total = "SELECT COUNT(l.id) AS total 
                      FROM BookBridges_livre l
                      JOIN BookBridges_livre_categorie lc ON l.id = lc.id_livre
                      WHERE lc.id_categorie = $category_id $search_query";
        $result_total = mysqli_query($conn, $sql_total);
        $row_total = mysqli_fetch_assoc($result_total);
        $total_pages = ceil($row_total["total"] / $results_per_page);

        echo "<div class='pagination'>";
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='books.php?category_id=" . $category_id . "&page=" . $i . "&search=" . (isset($_GET['search']) ? $_GET['search'] : '') . "'";
            if ($i == $page) {
                echo " class='active'";
            }
            echo ">" . $i . "</a> ";
        }
        echo "</div>";

    } else {
        echo "<p>Erreur : catégorie non trouvée.</p>";
    }

    mysqli_close($conn);
    ?>
</div>
</body>
</html>
