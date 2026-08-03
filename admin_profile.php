<?php
session_start();
include 'connect.php';

// Protecting account from unothurized accesses
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

$error = $_SESSION['not_match'] ?? '';
$error2 = $_SESSION['incorrect_current'] ?? '';
$success = $_SESSION['password_changed'] ?? '';


function showError($error)
{
  return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}
function showError2($error2)
{
  return !empty($error2) ? "<p class='error-message'>$error2</p>" : '';
}
function showSuccess($success)
{
  return !empty($success) ? "<p class='success-message'>$success</p>" : '';
}

$user_id = $_SESSION['user_id'];



$query = mysqli_query($conn, "SELECT * FROM user WHERE user_id = '$user_id'");
$user = mysqli_fetch_assoc($query);


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FoHSS</title>
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="./Assert/css/bootstrap.min.css">
  <style>
    form {
      width: 400px;
      margin: 50px auto;
    }

    h3,
    p {
      text-align: center;
    }
  </style>
</head>

<body>
  <header>
    <div class="logo-container">
      <img src="FoHSS.png" alt="Website Logo">
      <h1>Faculty Meeting Management System</h1>
    </div>
    <nav>
      <a href="home.php">Home</a>
      <a href="schedule.php">Schedule</a>
      <a href="registration.php">Registration</a>
      <a href="participant.php">Participants List</a>
      <a href="attendence.php">Attendence</a>
      <a href="admin_profile.php">Profile</a>
    </nav>
  </header>

  <main>
    <section class="content">
      <?php echo showError($error);
      unset($_SESSION['not_match']); ?>
      <?php echo showError2($error2);
      unset($_SESSION['incorrect_current']); ?>
      <?php echo showSuccess($success);
      unset($_SESSION['password_changed']); ?>
      <h3>Update User Details</h3>
      <p>Use the following form to change user password.</p>

      <form action="form.php" method="post">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo $user['name']; ?>" readonly>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" readonly>

        <label>Role</label>
        <!--<input type="text" name="role" value="<?php echo $user['role']; ?>" readonly><br>-->

        <label>Current Password</label>
        <input type="password" name="current_password" placeholder="Current Password" required>

        <label>New Password</label>
        <input type="password" name="new_password" placeholder="New Password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>

        <button type="submit" name="change_admin_password">Update Password</button>
      </form>

    </section>
  </main>

  <footer>
    &copy; 2025 Faculty Board Website. All rights reserved.
  </footer>

</body>

</html>