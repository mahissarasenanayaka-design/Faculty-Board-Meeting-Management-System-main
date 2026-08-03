<?php
session_start();
include 'connect.php';
include 'email.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

$success = $_SESSION['register_success'] ?? '';
$success2 = $_SESSION['delete_user_success'] ?? '';
$error = $_SESSION['register_error'] ?? '';


function showSuccess($success)
{
  return !empty($success) ? "<p class='success-message'>$success</p>" : '';
}
function showSuccess2($success2)
{
  return !empty($success2) ? "<p class='success-message'>$success2</p>" : '';
}
function showError($error)
{
  return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FoHSS</title>
  <link rel="stylesheet" href="">
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="./Assert/css/bootstrap.min.css">

  <style>
    .center {
      text-align: center;
    }

    .btn-fixed {
      width: 80px;
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
      <?php echo showSuccess($success);
      unset($_SESSION['register_success']); ?>
      <?php echo showSuccess2($success2);
      unset($_SESSION['delete_user_success']); ?>
      <?php echo showError($error);
      unset($_SESSION['register_error']); ?>
      <h2>Registered Users</h2>
      <div class="table-container">
        <table class="table table-striped">
          <thead>
            <tr>
              <th colspan="6" class="center">Faculty Members</th>
            </tr>
            <tr>
              <th colspan="6" class="center">Faculty of Humanities & Social Sciences</th>
            </tr>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Position</th>
              <th>Department</th>
              <th>Role</th>
              <th>Action</th>

            </tr>
          </thead>
          <tbody>
            <?php
            $sql1 = "SELECT * FROM user";
            $result = mysqli_query($conn, $sql1);
            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                $user_id = $row['user_id'];
                $name = $row['name'];
                $email = $row['email'];
                $position = $row['position'];
                $department = $row['department'];
                $role = $row['role'];

                echo "<tr>
                     <td>$name</td>
                     <td>$email</td>
                     <td>$position</td>
                     <td>$department</td>
                     <td>$role</td>
                     <td> 
                          <div class='d-flex gap-2'>
                          <form action='form.php' method='POST'>
                          <input type='hidden' name='user_id' value='$user_id'>
                          <button type='submit' name='delete_user' class='btn btn-danger btn-sm btn-fixed'>Delete</button>
                          </form>

                          <form action='update_user.php' method='get'>
                          <input type='hidden' name='user_id' value='$user_id'>
                          <button type='submit' name='update_user' class='btn btn-primary btn-sm btn-fixed'>Update</button>
                          </form>
                          </div>
                     </td>
                  </tr>";
              }
            }
            ?>
          </tbody>
        </table>
      </div>

    </section>

    <aside>
      <h3>User Registration</h3>
      <p>Use the following form to register users and admin members.</p>

      <form action="form.php" method="post">
        <label for="name">Name</label>
        <input type="text" name="name" required>

        <label for="email">Email</label>
        <input type="email" name="email" required>

        <label for="message">Position</label>
        <select name="position" required>
          <option value="">--Select Position--</option>
          <option value="Dean">Dean</option>
          <option value="Department head">Department head</option>
          <option value="Professor">Professor</option>
          <option value="Senior Lecturer">Senior Lecturer</option>
          <option value="Lecturer">Lecturer</option>
          <option value="Other">Other</option>
        </select>

        <label for="message">Department</label>
        <select name="department" required>
          <option value="">--Select Department--</option>
          <option value="Economics">Department of Economics</option>
          <option value="English and Linguistics">Department of English and Linguistics</option>
          <option value="English Language Teaching">Department of English Language Teaching</option>
          <option value="Geography">Department of Geography</option>
          <option value="History and Archaeology">Department of History and Archaeology</option>
          <option value="Information Technology">Department of Information Technology</option>
          <option value="Pali and Buddhist Studies">Department of Pali and Buddhist Studies</option>
          <option value="Public Policy">Department of Public Policy</option>
          <option value="Sinhala">Department of Sinhala</option>
          <option value="Sociology">Department of Sociology</option>
        </select>

        <!-- <label for="message">Password</label>
        <input type="password" name="password" required> -->

        <label for="message">Role</label>
        <select name="role" required>
          <option value="">--Select Role--</option>
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>

        <button type="submit" name="register">Register</button>
      </form>


    </aside>
  </main>

  <footer>
    &copy; 2025 Faculty Board Website. All rights reserved.
  </footer>
</body>

</html>