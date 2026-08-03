<?php
session_start();
include 'connect.php';
$schedule_id = $_GET['schedule_id'];

if (!isset($_SESSION['user_id'])) {
   header("Location: index.php");
   exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>FoHSS</title>
   <link rel="stylesheet" href="header.css">
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

         <h3>Update PDF File</h3>
         <p>Use the following form to upload PDF file.</p>

         <form action="form.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">

            <label for="uploadfile">PDF</label>
            <input type="file" name="uploadfile" id="" required>

            <label for="description">Description</label>
            <textarea id="message" name="description" rows="4" placeholder="Your message"></textarea>

            <button type="submit" name="pdf_upload">Upload PDF File</button>
         </form>

      </section>
   </main>

   <footer>
      &copy; 2025 Faculty Board Website. All rights reserved.
   </footer>

</body>

</html>