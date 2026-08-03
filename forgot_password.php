<?php
session_start();

$success = $_SESSION['changed_success'] ?? '';
$error = $_SESSION['changed_error'] ?? '';
$error2 = $_SESSION['email_not_found'] ?? '';


function showSuccess($success)
{
    return !empty($success) ? "<p class='success-message'>$success</p>" : '';
}
function showError($error)
{
  return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}
function showError2($error2)
{
  return !empty($error2) ? "<p class='error-message'>$error2</p>" : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FohSS Login</title>
    <link rel="stylesheet" href="login.css">
    <style>
        .success-message {
            padding: 12px;
            background: #d4edda;
            /* Light green background */
            border-radius: 6px;
            font-size: 16px;
            color: #155724;
            /* Dark green text */
            text-align: center;
            margin-top: 5px;
            margin-bottom: 5px;

        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-box">
            <form action="form.php" method="post">
                <h2>FoHSS Login</h2>
                <?php echo showSuccess($success);
                unset($_SESSION['changed_success']); ?>
                <?php echo showError($error);
                unset($_SESSION['changed_error']); ?>
                <?php echo showError2($error2);
                unset($_SESSION['email_not_found']); ?>
                <p>Forgot password! Reset now.</p>

                <input type="email" name="email" placeholder="Email" required>

                <button type="submit" name="forgot_password">Reset Password</button>

                <p>Don't have an account? Contact FoHSS Admin</p>
                <p><a href="index.php">Back to Login</a></p>

            </form>
        </div>
    </div>
</body>

</html>