<?php
session_start();

if (!isset($_SESSION['lgn']) || !isset($_SESSION['id'])) {
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
$user_id = $_SESSION['id'];
$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message']) && $book_id !== null) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $sql = "INSERT INTO BookBridges_messages (sender_id, book_id, message) VALUES ($user_id, $book_id, '$message')";
    $conn->query($sql);
}

// Fetch book details
$sql_book = "
    SELECT 
        BookBridges_livre.titre
    FROM 
        BookBridges_livre
    WHERE 
        BookBridges_livre.id = $book_id";
$result_book = mysqli_query($conn, $sql_book);
$book = mysqli_fetch_assoc($result_book);

// Fetch messages between users
$sql_messages = "
    SELECT 
        BookBridges_messages.*, 
        sender.mail as sender_mail
    FROM 
        BookBridges_messages
    JOIN 
        BookBridges_utilisateur as sender ON BookBridges_messages.sender_id = sender.id
    WHERE 
        BookBridges_messages.book_id = $book_id
    ORDER BY 
        BookBridges_messages.timestamp ASC";
$result_messages = mysqli_query($conn, $sql_messages);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Networking for <?php echo htmlspecialchars($book['titre']); ?></title>
    <link rel="stylesheet" href="networking.css">
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
        <div class="main-content">
            <h1>Networking for "<?php echo htmlspecialchars($book['titre']); ?>"</h1>
            <div class="message-form">
                <h2>Send a Message</h2>
                <form method="POST" action="networking.php?book_id=<?php echo $book_id; ?>">
                    <input type="hidden" name="sender_id" value="<?php echo $user_id; ?>">
                    <textarea name="message" placeholder="Write a message..." required></textarea>
                    <button type="submit">Send</button>
                </form>
            </div>
            <div class="messages-list">
                <h2>Messages:</h2>
                <?php while ($message = mysqli_fetch_assoc($result_messages)): ?>
                    <div class="message">
                        <p><strong><?php echo htmlspecialchars($message['sender_mail']); ?>:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
                        <p><em><?php echo $message['timestamp']; ?></em></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
