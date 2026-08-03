<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['register'])) {
    $meeting_id = $_POST['meeting_id'];
    $user_id = $_POST['user_id'];

    $conn->query("INSERT INTO participant (meeting_id, user_id) VALUES ($meeting_id, $user_id)");
}

if (isset($_POST['delete'])) {
    $meeting_id = $_POST['meeting_id'];
    $user_id = $_POST['user_id'];

    $conn->query("DELETE FROM participant WHERE meeting_id=$meeting_id AND user_id=$user_id");
}


$meetings = $conn->query("SELECT meeting_id, name FROM meeting");
$meeting = $conn->query("SELECT meeting_id, name FROM meeting");
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


            <?php
            //Show participant list
            if (isset($_POST['meeting_id'])) {

                $meeting_id = $_POST['meeting_id'];

                // Get meeting name
                $q = $conn->query("SELECT name FROM meeting WHERE meeting_id=$meeting_id");
                $meeting_name = $q->fetch_assoc()['name'];

                echo "<h4>Participants of $meeting_name</h4>";

                // Fetch all users
                $users = $conn->query("SELECT * FROM `user`");

                echo "<table class=\"table table-striped\">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Action</th>
            </tr>";

                while ($row = $users->fetch_assoc()) {

                    $user_id = $row['user_id'];

                    // Check if registered
                    $exists = $conn->query("
            SELECT * FROM participant
            WHERE meeting_id=$meeting_id AND user_id=$user_id
        ");

                    echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['position']}</td>
                <td>";

                    if ($exists->num_rows > 0) {
                        echo "
                <form action='' method='POST' style='display:inline;'>
                    <input type='hidden' name='meeting_id' value='$meeting_id'>
                    <input type='hidden' name='user_id' value='$user_id'>
                    <button type='submit' name='delete' class='btn btn-danger btn-fixed'>Delete</button>
                </form>
            ";
                    } else {
                        echo "
                <form action='' method='POST' style='display:inline;'>
                    <input type='hidden' name='meeting_id' value='$meeting_id'>
                    <input type='hidden' name='user_id' value='$user_id'>
                    <button type='submit' name='register' class='btn btn-success btn-fixed'>Register</button>
                </form>
            ";
                    }

                    echo "</td></tr>";
                }

                echo "</table>";
            }
            ?>



        </section>
        <aside>
            <h3>Add Participants</h3>
            <p>Use the following drop down to add participans to specific meeting.</p>
            <form action="" method="POST">
                <label>Select Meeting:</label>
                <select name="meeting_id" required>
                    <option value="">-- Select Meeting --</option>
                    <?php while ($m = $meetings->fetch_assoc()) { ?>
                        <option value="<?= $m['meeting_id']; ?>"
                            <?= (isset($_GET['meeting_id']) && $_GET['meeting_id'] == $m['meeting_id']) ? 'selected' : '' ?>>
                            <?= $m['name']; ?>
                        </option>
                    <?php } ?>
                </select>
                <button type="submit">View</button>
            </form><br>
        </aside>

    </main>

    <footer>
        &copy; 2025 Faculty Board Website. All rights reserved.
    </footer>
    <script src="./Assert/js/bootstrap.min.js"></script>
</body>

</html>