<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$success = $_SESSION['attendace_success'] ?? '';

function showSuccess($success)
{
  return !empty($success) ? "<p class='success-message'>$success</p>" : '';
}

// mark attendance
if (isset($_POST['mark_attendance'])) {

    $schedule_id = $_POST['schedule_id'];

    foreach ($_POST['status'] as $participant_id => $status) {
        $status_val = ($status == 'Present') ? 1 : 0;

        mysqli_query($conn, "
            INSERT INTO attendance (participant_id, schedule_id, status)
            VALUES ('$participant_id', '$schedule_id', '$status_val')
            ON DUPLICATE KEY UPDATE status = '$status_val'
        ");
    }
    $_SESSION['attendace_success'] = "Successfully marked attendance!";
    header("Location: attendence.php?marked_schedule=$schedule_id");
    exit;
}


$meetings = mysqli_query($conn, "SELECT meeting_id, name FROM meeting");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Attendance Marking</title>
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="./Assert/css/bootstrap.min.css">
    <style>
        .table-fixed {
            width: 100%;
        }

        .radio-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo-container">
            <img src="FoHSS.png">
            <h1>Faculty Meeting Management System</h1>
        </div>
        <nav>
            <a href="home.php">Home</a>
            <a href="schedule.php">Schedule</a>
            <a href="registration.php">Registration</a>
            <a href="participant.php">Participants List</a>
            <a href="attendence.php">Attendance</a>
            <a href="admin_profile.php">Profile</a>
        </nav>
    </header>

    <main>

        <section class="content">

            <?php
            if (isset($_POST['view_attendance'])) {

                $meeting_id = $_POST['meeting_id'];

               //Get Schedule
                $schedules = mysqli_query($conn, "
                SELECT schedule_id, schedule_date
                FROM schedule
                WHERE meeting_id = '$meeting_id'
                ORDER BY schedule_date
            ");

                $scheduleData = [];
                while ($s = mysqli_fetch_assoc($schedules)) {
                    $scheduleData[] = $s;
                }

               //Get schedules
                $participants = mysqli_query($conn, "
                SELECT p.participant_id, u.name
                FROM participant p
                JOIN user u ON u.user_id = p.user_id
                WHERE p.meeting_id = '$meeting_id'
            ");
                
                echo "<h4 class='mt-4'>Attendance Record</h4>";
                echo "<div class='table-container'>";
                echo "<table class='table table-bordered text-center'>
            <tr>
                <th>Name</th>";

                // Header dates
                foreach ($scheduleData as $sd) {
                    echo "<th>" . date("d M Y", strtotime($sd['schedule_date'])) . "</th>";
                }

                echo "</tr>";

                // Attendance rows
                while ($p = mysqli_fetch_assoc($participants)) {

                    echo "<tr>
                <td class='text-start'>{$p['name']}</td>";

                    foreach ($scheduleData as $sd) {

                        $att = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT status FROM attendance
                WHERE participant_id = '{$p['participant_id']}'
                AND schedule_id = '{$sd['schedule_id']}'
            "));

                        if ($att) {
                            echo $att['status'] == 1
                                ? "<td class='text-success'>Present</td>"
                                : "<td class='text-danger'>Absent</td>";
                        } else {
                            echo "<td>-</td>";
                        }
                    }

                    echo "</tr>";
                }

                echo "</table>";
                echo"</div>";
            }
            ?>


            <?php
            //Show attendance table
            if (isset($_POST['meeting_id'], $_POST['schedule_id'])) {

                $meeting_id  = $_POST['meeting_id'];
                $schedule_id = $_POST['schedule_id'];

                // Check if attendance already marked
                $already_marked = mysqli_num_rows(mysqli_query($conn, "
                SELECT * FROM attendance WHERE schedule_id = '$schedule_id'
            "));

                if ($already_marked > 0) {
                    echo "<p class='alert alert-success'>Attendance already marked for this schedule.</p>";
                } else {

                    // Get meeting and schedule info
                    $info = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT m.name, s.schedule_date
                    FROM schedule s
                    JOIN meeting m ON m.meeting_id = s.meeting_id
                    WHERE s.schedule_id = '$schedule_id'
            "));

                    $formatted_date = date("d M Y", strtotime($info['schedule_date']));
                    echo "<h4>Attendance for {$info['name']} ({$formatted_date})</h4>";

                    // Fetch participants and attendance
                    $users = mysqli_query($conn, "
                    SELECT 
                    p.participant_id,
                    u.name,
                    u.position,
                    IFNULL(a.status, 0) AS status
                    FROM participant p
                    JOIN user u ON u.user_id = p.user_id
                    LEFT JOIN attendance a 
                    ON a.participant_id = p.participant_id
                    AND a.schedule_id = '$schedule_id'
                    WHERE p.meeting_id = '$meeting_id'
            ");

                    echo "<form method='POST'>
            <input type='hidden' name='schedule_id' value='$schedule_id'>
            <input type='hidden' name='meeting_id' value='$meeting_id'>
            <div class='table-container'>
            <table class='table table-bordered table-fixed'>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th class='radio-center'>Present</th>
                    <th class='radio-center'>Absent</th>
                </tr>";

                    while ($row = mysqli_fetch_assoc($users)) {

                        $present_checked = ($row['status'] == 1) ? 'checked' : '';
                        $absent_checked  = ($row['status'] == 0) ? 'checked' : '';

                        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['position']}</td>
                <td class='radio-center'>
                    <input type='radio' name='status[{$row['participant_id']}]' value='Present' $present_checked>
                </td>
                <td class='radio-center'>
                    <input type='radio' name='status[{$row['participant_id']}]' value='Absent' $absent_checked>
                </td>
            </tr>";

                    }

                    echo "</table>
             </div>
            <button type='submit' name='mark_attendance'>Mark Attendance</button>
        </form>";
                }
            }
            ?>

        </section>

        <aside>
            <?php echo showSuccess($success);
            unset($_SESSION['attendace_success']); ?>
            <h3>Mark Attendance</h3>

            <!-- Select the meeting-->
            <form method="POST">
                <label>Select Meeting:</label>
                <select name="meeting_id" required>
                    <option value="">-- Select Meeting --</option>
                    <?php while ($m = mysqli_fetch_assoc($meetings)) { ?>
                        <option value="<?= $m['meeting_id']; ?>"
                            <?= (isset($_POST['meeting_id']) && $_POST['meeting_id'] == $m['meeting_id']) ? 'selected' : ''; ?>>
                            <?= $m['name']; ?>
                        </option>
                    <?php } ?>
                </select>
                <br>
                <button>Select Meeting</button>
            </form>

            <br>

            <!-- select the schedule date -->
            <?php
            if (isset($_POST['meeting_id'])) {

                $meeting_id = $_POST['meeting_id'];

                // Get schedules that do NOT have attendance marked
                $schedules = mysqli_query($conn, "
                SELECT schedule_id, schedule_date
                FROM schedule
                WHERE meeting_id = '$meeting_id'
                AND schedule_id NOT IN (SELECT DISTINCT schedule_id FROM attendance)
                ORDER BY schedule_date
        ");
        
            ?>
                <form method="POST">
                    <input type="hidden" name="meeting_id" value="<?= $meeting_id; ?>">

                    <label>Select Schedule Date:</label>
                    <select name="schedule_id" required>
                        <option value="">-- Select Date --</option>
                        <?php while ($s = mysqli_fetch_assoc($schedules)) { ?>
                            <option value="<?= $s['schedule_id']; ?>"
                                <?= (isset($_POST['schedule_id']) && $_POST['schedule_id'] == $s['schedule_id']) ? 'selected' : ''; ?>>
                                <?= date("d M Y", strtotime($s['schedule_date'])); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <br>
                    <button>Select Schedule</button>
                </form>
            <?php } ?>
            <br><br>

            <h3>View Attendance</h3>

            <form method="POST">
                <label>Select Meeting:</label>
                <select name="meeting_id" required>
                    <option value="">-- Select Meeting --</option>
                    <?php
                    $meetings = mysqli_query($conn, "SELECT meeting_id, name FROM meeting");
                    while ($m = mysqli_fetch_assoc($meetings)) {
                        echo "<option value='{$m['meeting_id']}'>{$m['name']}</option>";
                    }
                    ?>
                </select>
                <br>
                <button type="submit" name="view_attendance">
                    View Attendance
                </button>
            </form>
        </aside>

    </main>

    <footer>
        &copy; 2025 Faculty Board Website
    </footer>

</body>

</html>