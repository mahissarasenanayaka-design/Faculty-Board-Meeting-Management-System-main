<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


if (!isset($_GET['schedule_id'])) {
    die("User ID not provided");
}
$schedule_id = $_GET['user_id'];

// Get user details
$query = mysqli_query($conn, "SELECT * FROM user WHERE schedule_id = '$schedule_id'");
if (mysqli_num_rows($query) == 0) {
    die("User not found");
}
$user = mysqli_fetch_assoc($query);

// Handle form submission
if (isset($_POST['update_schedule'])) {
    $schedule_id = $_POST['schedule_id'];
    $name = !empty($_POST['name']) ? $_POST['name'] : $user['name'];
    $email = !empty($_POST['email']) ? $_POST['email'] : $user['email'];
    $position = !empty($_POST['position']) ? $_POST['position'] : $user['position'];
    $department = !empty($_POST['department']) ? $_POST['department'] : $user['department'];
    $role = !empty($_POST['role']) ? $_POST['role'] : $user['role'];

   
    $stmt = $conn->prepare("UPDATE user SET name=?, email=?, position=?, department=?, role=? WHERE user_id=?");
    $stmt->bind_param("sssssi", $name, $email, $position, $department, $role, $user_id);

    if ($stmt->execute()) {
        echo "<p style='padding:12px; background:#d4edda; border-radius:6px; font-size:16px; color:#155724; text-align:center; margin-top:5px; margin-bottom:5px;'>Error: " . $stmt->error . "</p>";

       
        $query = mysqli_query($conn, "SELECT * FROM user WHERE user_id = '$user_id'");
        $user = mysqli_fetch_assoc($query);
    } else {
        echo "<p style='color:red; text-align:center;'>Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoHSS</title>
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="./Assert/css/bootstrap.min.css">
    <style>
        form {
            width: 420px;
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
            <h1>FoHSS Faculty Board</h1>
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
            <h3>User Details</h3>

            <form action="" method="post">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">

                <label>Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>">

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

                <label>Position</label>
                <select name="position">
                    <option value="">--Select Position--</option>
                    <option value="Dean" <?= $user['position'] == 'Dean' ? 'selected' : '' ?>>Dean</option>
                    <option value="Department head" <?= $user['position'] == 'Department head' ? 'selected' : '' ?>>Department head</option>
                    <option value="Professor" <?= $user['position'] == 'Professor' ? 'selected' : '' ?>>Professor</option>
                    <option value="Senior Lecturer" <?= $user['position'] == 'Senior Lecturer' ? 'selected' : '' ?>>Senior Lecturer</option>
                    <option value="Lecturer" <?= $user['position'] == 'Lecturer' ? 'selected' : '' ?>>Lecturer</option>
                    <option value="Other" <?= $user['position'] == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>

                <label>Department</label>
                <select name="department">
                    <option value="">--Select Department--</option>
                    <option value="Economics" <?= $user['department'] == 'Economics' ? 'selected' : '' ?>>Department of Economics</option>
                    <option value="English and Linguistics" <?= $user['department'] == 'English and Linguistics' ? 'selected' : '' ?>>Department of English and Linguistics</option>
                    <option value="English Language Teaching" <?= $user['department'] == 'English Language Teaching' ? 'selected' : '' ?>>Department of English Language Teaching</option>
                    <option value="Geography" <?= $user['department'] == 'Geography' ? 'selected' : '' ?>>Department of Geography</option>
                    <option value="History and Archaeology" <?= $user['department'] == 'History and Archaeology' ? 'selected' : '' ?>>Department of History and Archaeology</option>
                    <option value="Information Technology" <?= $user['department'] == 'Information Technology' ? 'selected' : '' ?>>Department of Information Technology</option>
                    <option value="Pali and Buddhist Studies" <?= $user['department'] == 'Pali and Buddhist Studies' ? 'selected' : '' ?>>Department of Pali and Buddhist Studies</option>
                    <option value="Public Policy" <?= $user['department'] == 'Public Policy' ? 'selected' : '' ?>>Department of Public Policy</option>
                    <option value="Sinhala" <?= $user['department'] == 'Sinhala' ? 'selected' : '' ?>>Department of Sinhala</option>
                    <option value="Sociology" <?= $user['department'] == 'Sociology' ? 'selected' : '' ?>>Department of Sociology</option>
                </select>

                <label>Role</label>
                <select name="role">
                    <option value="">--Select Role--</option>
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>

                <button type="submit" name="update_user">Update</button>
            </form>

        </section>
    </main>

    <footer>
        &copy; 2025 Faculty Board Website. All rights reserved.
    </footer>

</body>

</html>