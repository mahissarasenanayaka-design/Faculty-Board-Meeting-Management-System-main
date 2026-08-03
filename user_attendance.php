<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Attendance</title>
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="./Assert/css/bootstrap.min.css">
</head>

<body>
    <header>
        <div class="logo-container">
            <img src="FoHSS.png">
            <h1>Faculty Meeting Management System</h1>
        </div>
        <nav>
            <a href="user_home.php">Home</a>
            <a href="user_attendance.php">Attendance</a>
            <a href="user_profile.php">Profile</a>
        </nav>
    </header>


    <main>
        <section class="content">
            <h2>My Attendance</h2>
            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="center">Attendance Records</th>
                        </tr>
                        <tr>
                            <th>Meeting Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT 
                        m.name AS meeting_name,
                        s.schedule_date,
                        s.schedule_time,
                        a.status
                        FROM attendance a
                        INNER JOIN participant p ON a.participant_id = p.participant_id
                        INNER JOIN schedule s ON a.schedule_id = s.schedule_id
                        INNER JOIN meeting m ON s.meeting_id = m.meeting_id
                        WHERE p.user_id = $user_id
                        ORDER BY s.schedule_date DESC";

                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $date = date("d M Y", strtotime($row['schedule_date']));
                                $time = date("h:i A", strtotime($row['schedule_time']));
                                $statusClass = ($row['status'] == "Present") ? "status-present" : "status-absent";
                                echo "<tr>
                                   <td>{$row['meeting_name']}</td>
                                   <td>$date</td>
                                   <td>$time</td>
                                   <td class='$statusClass'>{$row['status']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr>
                                <td colspan='4' class='center'>No attendance records found</td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        &copy; 2025 Faculty Board Website. All rights reserved.
    </footer>
    <script src="./Assert/js/bootstrap.min.js"></script>
</body>
</html>