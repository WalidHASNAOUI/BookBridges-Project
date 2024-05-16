<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <img src="book.png">
        <h1>Change password</h1>

        <?php include 'fctpassword.php'; ?>

        <form id="passwordForm" method="post" action="password.php">
            <div class="form-group">
                <label for="username">Email address</label>
                <input type="text" id="username" name="username" required <?php if(isset($error)) echo 'style="border-color: red;"'; ?>>
            </div>

            <div class="form-group">
                <label for="new-password">New password</label>
                <input type="password" id="new-password" name="new-password" required <?php if(isset($error)) echo 'style="border-color: red;"'; ?>>
            </div>

            <div class="form-group">
                <label for="verify-password">Verify new password</label>
                <input type="password" id="verify-password" name="verify-password" required <?php if(isset($error)) echo 'style="border-color: red;"'; ?>>
                <p id="error-message" style="color: red; display: none;">Passwords do not match.</p>
            </div>

            <div class="form-group">
                <button type="submit">Submit</button>
            </div>
        </form>

        <?php
        if (isset($message)) {
            echo '<p style="color: green;">' . $message . '</p>';
        } elseif (isset($error)) {
            echo '<p style="color: red;">' . $error . '</p>';
        }
        ?>

        <div class="secondary-links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>

    <script>
        document.getElementById('passwordForm').addEventListener('submit', function(event) {
            const newPassword = document.getElementById('new-password').value;
            const verifyPassword = document.getElementById('verify-password').value;
            const errorMessage = document.getElementById('error-message');

            if (newPassword !== verifyPassword) {
                event.preventDefault();
                errorMessage.style.display = 'block';
                document.getElementById('new-password').style.borderColor = 'red';
                document.getElementById('verify-password').style.borderColor = 'red';
            } else {
                errorMessage.style.display = 'none';
                document.getElementById('new-password').style.borderColor = '';
                document.getElementById('verify-password').style.borderColor = '';
            }
        });
    </script>
</body>
</html>
