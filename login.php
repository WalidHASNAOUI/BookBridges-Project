<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <img src="book.png">
        <h1>Sign in BookMeet</h1>

        <?php include 'fctlogin.php'; ?>

        <form method="post" action="login.php">
            <div class="form-group">
                <label for="username">Email address</label>
                <input type="text" id="username" name="username" required <?php if(isset($error)) echo 'style="border-color: red;"'; ?>>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required <?php if(isset($error)) echo 'style="border-color: red;"'; ?>>
            </div>

            <div class="form-group">
                <button type="submit">Sign in</button>
            </div>
            <a href="https://tp-epua.univ-smb.fr/~dahmanei/BookBridges-Project/password.php">Forgot password?</a>
        </form>

        <?php
        if (isset($message)) {
            echo '<p style="color: green;">' . $message . '</p>';
        } elseif (isset($error)) {
            echo '<p style="color: red;">' . $error . '</p>';
        }
        ?>

        <div class="secondary-links">
            <a>New to BookMeet? </a>
            <a href="https://tp-epua.univ-smb.fr/~dahmanei/BookBridges-Project/signup.php">Create an account</a>
        </div>
    </div>
</body>
</html>

