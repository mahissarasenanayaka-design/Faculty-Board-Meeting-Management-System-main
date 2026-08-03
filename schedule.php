<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

$success = $_SESSION['meeting_success'] ?? '';
$success2 = $_SESSION['meeting_deleted'] ?? '';
$success3 = $_SESSION['schedule_upload_success'] ?? '';
$success4 = $_SESSION['schedule_deleted'] ?? '';


function showSuccess($success)
{
  return !empty($success) ? "<p class='success-message'>$success</p>" : '';
}
function showSuccess2($success2)
{
  return !empty($success2) ? "<p class='success-message'>$success2</p>" : '';
}
function showSuccess3($success3)
{
  return !empty($success3) ? "<p class='success-message'>$success3</p>" : '';
}
function showSuccess4($success4)
{
  return !empty($success4) ? "<p class='success-message'>$success4</p>" : '';
}

// <form action='form.php' method='POST'>
// <input type='hidden' name='schedule_id' value='$schedule_id'>
// <button type='submit' name='update_schedule' class='btn btn-primary btn-sm btn-fixed'>Update</button>
// </form>

//<div class='d-flex gap-2'>  </div>
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

    #buttons {
      padding: 10px 15px;
      background-color: #4b5c89;
      color: white;
      text-decoration: none;
      display: inline-block;
      border-radius: 4px;
      cursor: pointer;
      border: none;
      transition: 0.3s;
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
      <?php echo showSuccess3($success3);
      unset($_SESSION['meeting_deleted']); ?>
      <?php echo showSuccess2($success2);
      unset($_SESSION['schedule_upload_success']); ?>
      <?php echo showSuccess4($success4);
      unset($_SESSION['schedule_deleted']); ?>
      <?php
      // Manage meeting
      if (isset($_POST['manage_meeting'])) {

        $users = $conn->query("SELECT * FROM `meeting`");

        echo "<table class=\"table table-striped\">
            <tr>
                <th>Meeting ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>";

        while ($row = $users->fetch_assoc()) {
          $meeting_id = $row['meeting_id'];
          $name = $row['name'];

          echo "<tr>
                <td>$meeting_id</td>
                <td>$name</td>
                 <td>
                         <div class='d-flex gap-2'>
                         <form action='form.php' method='POST'>
                         <input type='hidden' name='meeting_id' value='$meeting_id'>
                         <button type='submit' name='delete_meeting' class='btn btn-danger btn-sm btn-fixed'>Delete</button>
                         </form>
                     </td>
                <tr>";


          echo "</td></tr>";
        }

        echo "</table>";
      }
      //Manage Meeting
      ?>
      <h2>Meeting Shedule</h2>
      <div class="table-container">
        <table class="table table-striped">
          <thead>
            <tr>
              <th colspan="6" class="center">Meeting Shedule</th>
            </tr>
            <tr>
              <th colspan="6" class="center">Faculty of Humanities & Social Sciences</th>
            </tr>
            <tr>
              <th>Meeting Name</th>
              <th>Date</th>
              <th>Time</th>
              <th>Description</th>
              <th>Upload PDF</th>
              <th>Action</th>

            </tr>
          </thead>
          <tbody>
            <?php
            $sql1 = "SELECT
                 m.name,
                 s.schedule_id,
                 s.schedule_date,
                 s.schedule_time,
                 s.description
                 FROM
                 meeting m
                 INNER JOIN
                 schedule s
                 ON
                 m.meeting_id = s.meeting_id
                 ORDER BY
                 s.schedule_date ASC;";
            $result = mysqli_query($conn, $sql1);
            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['name'];
                $schedule_date = $row['schedule_date'];
                $schedule_time = $row['schedule_time'];
                $description = $row['description'];
                $schedule_id = $row['schedule_id'];
                $formatted_date = date("d M Y", strtotime($row['schedule_date']));
                $formatted_time = date("h:i A", strtotime($row['schedule_time']));

                $stmt = $conn->prepare("SELECT * FROM meeting_file WHERE schedule_id = ?");
                $stmt->bind_param("i", $schedule_id);
                $stmt->execute();
                $res = $stmt->get_result();
                $uploaded = $res->num_rows > 0;

                $btn_class = $uploaded ? "btn btn-success btn-sm btn-fixed" : "btn btn-primary btn-sm btn-fixed";
                $btn_text  = $uploaded ? "Uploaded" : "Upload";
                $disabled  = $uploaded ? "disabled" : "";


                echo "<tr>
                     <td>$name</td>
                     <td>$formatted_date</td>
                     <td>$formatted_time</td>
                     <td>$description</td>
                     
                     <td>
                        <form action='upload_pdf.php' method='get' style='display:inline;'>
                          <input type='hidden' name='schedule_id' value='$schedule_id'>
                          <button class='$btn_class' $disabled>$btn_text</button>
                        </form>
                     </td>
                     <td>
                         <form action='form.php' method='POST'>
                         <input type='hidden' name='schedule_id' value='$schedule_id'>
                         <button type='submit' name='delete_schedule' class='btn btn-danger btn-sm btn-fixed'>Delete</button>
                         </form>
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
      <?php echo showSuccess($success);
      unset($_SESSION['meeting_success']); ?>
      <h3>Schedule Upload</h3>
      <p>Use the following form to upload meeting schedules.</p>
      <form action="form.php" method="post">

        <label>Select Meeting:</label>
        <select name="meeting_id" required>
          <option value="">-- Select Meeting --</option>
          <?php
          $sql = "SELECT meeting_id, name FROM meeting";
          $result = mysqli_query($conn, $sql);
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['meeting_id'] . "'>" . $row['name'] . "</option>";
          }
          ?>
        </select>

        <label for="schedule_date">Date</label>
        <input type="date" name="schedule_date" required>

        <label for="schedule_time">Time</label>
        <input type="time" name="schedule_time" required>

        <label for="description">Description</label>
        <textarea id="message" name="description" rows="4" placeholder="Your message"></textarea>

        <button id="buttons" type="submit" name="schedule_upload">Upload Meeting Schedule</button>
      </form><br><br>


      <h3>Create Meeting</h3><br>
      <p>Use the following form to create meetings.</p>
      <form action="form.php" method="post">
        <label for="name">Meeting Name</label>
        <input type="text" name="name" required>
        <button id="buttons" type="submit" name="register_meeting">Create new Meeting</button>
      </form><br><br>

      <form action="" method="post">
        <label for="">Manage Meeting</label>
        <button id="buttons" type="submit" name="manage_meeting">Manage Meetings</button>
      </form>

    </aside>
  </main>

  <footer>
    &copy; 2025 Faculty Board Website. All rights reserved.
  </footer>
</body>

</html>