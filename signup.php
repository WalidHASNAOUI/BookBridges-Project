<?php include 'fctsignup.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <img src="book.png" alt="BookMeet Logo">
        <h1>Sign up to BookMeet</h1>

        <?php include 'fctsignup.php'; ?>

        <form id="signupForm" method="post" action="signup.php">
            <div class="form-group">
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="first-name" required>
            </div>

            <div class="form-group">
                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="last-name" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" required>
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="new-password">Password</label>
                <input type="password" id="new-password" name="new-password" required>
            </div>

            <div class="form-group">
                <label for="verify-password">Verify password</label>
                <input type="password" id="verify-password" name="verify-password" required>
                <p id="error-message" style="color: red; display: none;">Passwords do not match.</p>
            </div>

            <div class="form-group">
                <button type="submit">Sign Up</button>
            </div>
        </form>

        <div class="secondary-links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(event) {
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
