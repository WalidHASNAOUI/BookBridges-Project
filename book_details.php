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

if (!isset($_GET['book_id'])) {
    die("Book ID not specified.");
}

$book_id = intval($_GET['book_id']);

$sql_book = "
    SELECT 
        BookBridges_livre.*, bookbridges_auteur.*,
        AVG(BookBridges_avis.note) as average_rating,
        COUNT(BookBridges_avis.id) as nbr_of_ratings
    FROM 
        BookBridges_livre 
    JOIN 
        bookbridges_auteur
    ON
        bookbridges_livre.id_auteur = bookbridges_auteur.id
    LEFT JOIN 
        BookBridges_avis 
    ON 
        BookBridges_livre.id = BookBridges_avis.id_livre 
    WHERE 
        BookBridges_livre.id = $book_id
    GROUP BY 
        BookBridges_livre.id";

$result_book = mysqli_query($conn, $sql_book);

if (!$result_book || mysqli_num_rows($result_book) == 0) {
    die("Book not found.");
}

$book = mysqli_fetch_assoc($result_book);

$sql_categories = "
    SELECT 
        BookBridges_categorie.id,
        BookBridges_categorie.nom 
    FROM 
        BookBridges_categorie 
    JOIN 
        BookBridges_livre_categorie 
    ON 
        BookBridges_categorie.id = BookBridges_livre_categorie.id_categorie 
    WHERE 
        BookBridges_livre_categorie.id_livre = $book_id";

$result_categories = mysqli_query($conn, $sql_categories);
$categories = [];
while ($row = mysqli_fetch_assoc($result_categories)) {
    $categories[] = $row;
}

$sql_ratings = "
    SELECT 
        note, 
        COUNT(*) as count 
    FROM 
        BookBridges_avis 
    WHERE 
        id_livre = $book_id 
    GROUP BY 
        note 
    ORDER BY 
        note DESC";
$result_ratings = mysqli_query($conn, $sql_ratings);
$ratings = [];
while ($row = mysqli_fetch_assoc($result_ratings)) {
    $ratings[$row['note']] = $row['count'];
}

// Initialize counts for each rating value (1-5 stars) to 0
$rating_counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
$total_ratings = 0;

foreach ($rating_counts as $rating => $count) {
    if (isset($ratings[$rating])) {
        $rating_counts[$rating] = $ratings[$rating];
        $total_ratings += $ratings[$rating];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['titre']); ?></title>
    <link rel="stylesheet" href="categories_books.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .book-details-container {
            display: flex;
            align-items: flex-start;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .book-details-image {
            flex: 0 0 250px;
            margin-right: 20px;
        }
        .book-details-image img {
            width: 100%;
            border-radius: 10px;
        }
        .book-details-content {
            flex: 1;
        }
        .book-details-content h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .book-details-content p {
            font-size: 16px;
            color: #666;
            margin: 10px 0;
        }
        .book-details-content .rating {
            display: flex;
            align-items: center;
            font-size: 18px;
            margin: 10px 0;
        }
        .book-details-content .rating span {
            color: #FFD700;
            margin-right: 5px;
        }
        .book-details-content .rating .average-rating {
            margin-left: 10px;
            font-weight: bold;
            color: #333;
        }
        .book-details-content .genres {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }
        .book-details-content .genres a {
            text-decoration: none;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .book-details-content .genres a:hover {
            background-color: #45a049;
        }
        .book-details-content .actions {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        .book-details-content .actions button {
            padding: 10px 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .book-details-content .actions button:hover {
            background-color: #45a049;
        }
        .book-details-content .actions a {
            padding: 10px 20px;
            border: 2px solid #4CAF50;
            background-color: white;
            color: #4CAF50;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .book-details-content .actions a:hover {
            background-color: #4CAF50;
            color: white;
        }
        .rating-statistics {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
        <div class="header_logo_app">
            <a href="categories_books.php?category_id=1"><img src="./assets/icons/logo.png" alt="Logo"></a>
        </div>
       
        <div class="header_username">
            <p>Hy <b><?php echo $_SESSION['mail'] . " !"; ?></b></p>
            <a href="password.php" class="update-password-button">Update Password</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </header>
    <div class="book-details-container">
        <div class="book-details-image">
            <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="Book Image">
        </div>
        <div class="book-details-content">
            <h2><?php echo htmlspecialchars($book['titre']); ?></h2>
            <p>par <?php echo htmlspecialchars($book['nom']); ?></p>
            <div class="rating">
                <span>&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                <div class="average-rating"><?php echo number_format($book['average_rating'], 1); ?> / 5 (<?php echo $book['nbr_of_ratings']; ?> ratings)</div>
            </div>
            <p><strong>Première publication:</strong> <?php echo htmlspecialchars($book['annee']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($book['resume'])); ?></p>
            <div class="genres">
                <?php foreach ($categories as $category): ?>
                    <a href="categories_books.php?category_id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['nom']); ?></a>
                <?php endforeach; ?>
            </div>
            <div class="rating-statistics">
                <h3>Statistiques de notation</h3>
                <canvas id="ratingChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('ratingChart').getContext('2d');
            var ratingChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['5 stars', '4 stars', '3 stars', '2 stars', '1 star'],
                    datasets: [{
                        label: 'Number of Ratings',
                        data: [
                            <?php echo $rating_counts[5]; ?>,
                            <?php echo $rating_counts[4]; ?>,
                            <?php echo $rating_counts[3]; ?>,
                            <?php echo $rating_counts[2]; ?>,
                            <?php echo $rating_counts[1]; ?>
                        ],
                        backgroundColor: [
                            'rgba(255, 165, 0, 0.6)',
                            'rgba(255, 165, 0, 0.6)',
                            'rgba(255, 165, 0, 0.6)',
                            'rgba(255, 165, 0, 0.6)',
                            'rgba(255, 165, 0, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 165, 0, 1)',
                            'rgba(255, 165, 0, 1)',
                            'rgba(255, 165, 0, 1)',
                            'rgba(255, 165, 0, 1)',
                            'rgba(255, 165, 0, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
