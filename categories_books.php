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

$rating_submitted = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating']) && isset($_POST['book_id'])) {
    $rating = intval($_POST['rating']);
    $book_id = intval($_POST['book_id']);
    $user_id = $_SESSION["id"]; // Assuming user ID is 1 for demonstration purposes, update this as needed

    $sql = "INSERT INTO BookBridges_avis (id_utilisateur, note, id_livre) VALUES ($user_id, $rating, $book_id)";
    if ($conn->query($sql) === TRUE) {
        $rating_submitted = true;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$results_per_page = 10; // Number of books per page
$start_from = ($page - 1) * $results_per_page;

$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    // Updated to use the author name
    $search_query = "AND (BookBridges_livre.titre LIKE '%$search%' OR BookBridges_auteur.nom LIKE '%$search%')";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Books</title>
    <link rel="stylesheet" href="categories_books.css">
    <script>
        function setRating(bookId, rating) {
            document.getElementById('rating-input-' + bookId).value = rating;
            for (var i = 1; i <= 5; i++) {
                var star = document.getElementById('star-' + bookId + '-' + i);
                if (i <= rating) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            }
        }

        function showPopup(message) {
            var popup = document.createElement('div');
            popup.className = 'popup';
            popup.innerText = message;
            document.body.appendChild(popup);
            setTimeout(function() {
                popup.classList.add('show');
            }, 100); // Delay the show class to ensure transition
            setTimeout(function() {
                popup.classList.remove('show');
                setTimeout(function() {
                    popup.remove();
                }, 300); // Ensure this matches the transition time
            }, 3000); // Show popup for 3 seconds
        }

        window.onload = function() {
            <?php if ($rating_submitted): ?>
            showPopup('Rating submitted successfully!');
            <?php endif; ?>
        }
    </script>
    <style>
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            z-index: 1000;
        }
        .popup.show {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1.1);
        }
        .logout-button, .update-password-button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .logout-button:hover, .update-password-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
<header>
        <div class="header_logo_app">
            <a href="categories_books.php?category_id=1"><img src="./assets/icons/logo.png" alt="Logo"></a>
        </div>
        <div class="header_search_bar">
            <form method="GET" action="categories_books.php">
                <div class="search-bar">
                    <input type="text" name="search" placeholder="Search for books...">
                    <button type="submit">Search</button>
                </div>
            </form>
        </div>
        <div class="header_username">
            <p>Hy <b><?php echo $_SESSION['mail'] . " !"; ?></b></p>
            <a href="profile.php" class="button prf">Profile</a>
            <a href="password.php" class="update-password-button">Update Password</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </header>
    <div class="container">
        <div class="sidebar">
            <h2>Categories</h2>
            <div class="categories">
                <?php
                $sql = "SELECT id, nom FROM BookBridges_categorie ORDER BY nom";
                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='category' onclick=\"window.location.href='categories_books.php?category_id=" . $row['id'] . "'\">";
                    echo $row['nom'];
                    echo "</div>";
                }
                ?>
            </div>
        </div>
        <div class="main-content">
            <div class="books">
                <?php
                $sql_books = "
                    SELECT 
                        BookBridges_livre.*, 
                        BookBridges_auteur.nom as author_name,
                        AVG(BookBridges_avis.note) as average_rating,
                        COUNT(BookBridges_avis.id) as nbr_of_ratings
                    FROM 
                        BookBridges_livre 
                    JOIN 
                        BookBridges_livre_categorie 
                    ON 
                        BookBridges_livre.id = BookBridges_livre_categorie.id_livre 
                    LEFT JOIN 
                        BookBridges_avis 
                    ON 
                        BookBridges_livre.id = BookBridges_avis.id_livre
                    JOIN 
                        BookBridges_auteur
                    ON
                        BookBridges_livre.id_auteur = BookBridges_auteur.id
                    WHERE 
                        1=1";
                if ($category_id !== null) {
                    $sql_books .= " AND BookBridges_livre_categorie.id_categorie = $category_id";
                }
                $sql_books .= " $search_query 
                    GROUP BY 
                        BookBridges_livre.id 
                    LIMIT 
                        $start_from, $results_per_page";
                $result_books = mysqli_query($conn, $sql_books);

                while ($row_books = mysqli_fetch_assoc($result_books)) {
                    echo "<div class='book'>";
                    echo "<a href='book_details.php?book_id=" . $row_books['id'] . "'>";
                    echo "<img src='" . $row_books['image'] . "' alt='Book Image'>";
                    echo "<h3>" . $row_books['titre'] . "</h3>";
                    echo "</a>";
                    echo "<p>" . $row_books['author_name'] . "</p>";
                    echo "<p>Note: " . number_format($row_books['average_rating'], 1) . "/5 (" . $row_books['nbr_of_ratings'] . " ratings)</p>";
                    echo "<div class='rating-form'>";
                    echo "<form method='POST' action='categories_books.php?category_id=$category_id&page=$page'>";
                    echo "<input type='hidden' name='book_id' value='" . $row_books['id'] . "'>";
                    echo "<input type='hidden' id='rating-input-" . $row_books['id'] . "' name='rating' value=''>";
                    for ($i = 1; $i <= 5; $i++) {
                        echo "<span class='star' id='star-" . $row_books['id'] . "-" . $i . "' onclick='setRating(" . $row_books['id'] . ", " . $i . ")'>&#9733;</span>";
                    }
                    echo "<br><button type='submit'>Evaluez ce livre</button></br>";
                    echo "</form>";
                    echo "</div>";
                    echo "<a class='discuss-link' href='networking.php?book_id=" . $row_books['id'] . "' >Discuss this book</a>"; // Add this line
                    echo "</div>";
                }
                ?>
            </div>
            <div class="pagination">
                <?php
                $sql_total = "
                    SELECT COUNT(DISTINCT BookBridges_livre.id) AS total 
                    FROM BookBridges_livre 
                    JOIN BookBridges_livre_categorie 
                    ON BookBridges_livre.id = BookBridges_livre_categorie.id_livre 
                    JOIN BookBridges_auteur
                    ON BookBridges_livre.id_auteur = BookBridges_auteur.id
                    WHERE 1=1";
                if ($category_id !== null) {
                    $sql_total .= " AND BookBridges_livre_categorie.id_categorie = $category_id";
                }
                $sql_total .= " $search_query";
                $result_total = mysqli_query($conn, $sql_total);
                $row_total = mysqli_fetch_assoc($result_total);
                $total_pages = ceil($row_total['total'] / $results_per_page);

                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='categories_books.php?category_id=$category_id&page=$i'";
                    if ($i == $page) echo " class='active'";
                    echo ">$i</a> ";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
