<?php

session_start();
$error = $_SESSION['login_error'] ?? '';


function showError($error)
{
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FohSS Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="container">
        <div class="form-box">
            <form action="form.php" method="post">
                <h2>FoHSS Login</h2>
                <?php echo showError($error);
                unset($_SESSION['login_error']); ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <p>Don't have an account? Contact FoHSS Admin</p>
                <p><a href="forgot_password.php">Forgot password?</a></p>

            </form>
        </div>
    </div>
</body>

</html>