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

$mail = $_SESSION['lgn'];

// Fetch user information
$sql_user = "SELECT * FROM BookBridges_utilisateur WHERE mail = '$mail'";
$result_user = mysqli_query($conn, $sql_user);
$user = mysqli_fetch_assoc($result_user);

// Fetch rated books with average ratings
$sql_ratings = "
    SELECT BookBridges_livre.id, BookBridges_livre.titre, BookBridges_livre.image, AVG(BookBridges_avis.note) as average_rating
    FROM BookBridges_avis
    JOIN BookBridges_livre ON BookBridges_avis.id_livre = BookBridges_livre.id
    WHERE BookBridges_avis.id_utilisateur = (SELECT id FROM BookBridges_utilisateur WHERE mail = '$mail')
    GROUP BY BookBridges_livre.id, BookBridges_livre.titre, BookBridges_livre.image
";
$result_ratings = mysqli_query($conn, $sql_ratings);
$ratings = [];
while ($row = mysqli_fetch_assoc($result_ratings)) {
    $ratings[] = $row;
}
$rated_books_count = count($ratings);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['prenom']) . ' ' . htmlspecialchars($user['nom']); ?>'s Profile</title>
    <link rel="stylesheet" href="categories_books.css">
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
            <a href="profile.php" class="button">Profile</a>
            <a href="password.php" class="update-password-button">Update Password</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </header>
    <div class="container">
        <div class="main-content">
            <div class="profile-header">
                <div class="profile-image">
                    <img src="./assets/icons/default_profile.png" alt="Profile Image">
                </div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($user['prenom']) . ' ' . htmlspecialchars($user['nom']); ?></h1>
                    <p>Email: <?php echo htmlspecialchars($user['mail']); ?></p>
                    <p>Age: <?php echo htmlspecialchars($user['age']); ?></p>
                    <p>Number of Books Rated: <?php echo $rated_books_count; ?></p>
                </div>
            </div>
            <div class="profile-ratings">
                <h2>Rated Books</h2>
                <div class="books">
                    <?php foreach ($ratings as $rating): ?>
                        <div class="book">
                            <a href="book_details.php?book_id=<?php echo $rating['id']; ?>">
                                <img src="<?php echo htmlspecialchars($rating['image']); ?>" alt="Book Image">
                                <h3><?php echo htmlspecialchars($rating['titre']); ?></h3>
                                <p>Average Rating: <?php echo number_format($rating['average_rating'], 1); ?>/5</p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
