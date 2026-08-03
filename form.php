<?php
session_start();
require_once 'connect.php';
include 'email.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $department = $_POST['department'];

    $plainPassword = bin2hex(random_bytes(4)); // 8 characters
    
    $password = password_hash($plainPassword, PASSWORD_DEFAULT);

    $role = $_POST['role'];
    

    $subject = "Access Details for Faculty Board Meeting Management System";
    $body = "Dear <b>{$name}</b>,<br>
             You have been successfully registered in the Faculty Board Meeting Management System. To access the system, please 
             log in using the temporary password provided below. For security reasons, you are required to change this temporary 
             password immediately after your first login and set a strong, secure password of your choice.<br><br>

             Temporary Password: <b>{$plainPassword}</b><br><br>

             Login Instructions: <br><br>

             Visit the Faculty Board Meeting Management System login page.
             Enter your registered email address and the temporary password.
             Update your password to a strong password to ensure account security.<br><br>

             If you experience any issues while logging in or changing your password, please contact the system administrator 
             for assistance.<br><br>

             Thank you for your cooperation.<br><br>

             Kind regards,<br>
             Faculty Board Meeting Management System Administrator";

    $checkEmail = $conn->query("SELECT email FROM user WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        header("Location: registration.php");
        exit();
    } else {
        $conn->query("INSERT INTO user (name, email, position, department, password, role) VALUES ('$name', '$email', '$position', '$department', '$password', '$role')");
        $_SESSION['register_success'] = 'successfully registered!';
        SendEmail($email, $body, $subject);
        header("Location: registration.php");
        exit();
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM user WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];

            if ($user['role'] === 'admin') {
                header("Location: home.php");
            } else {
                header("Location: user_home.php");
            }
            exit();
        }
    }
    $_SESSION['login_error'] = 'Incorrect email or password';
    header("Location: index.php");
    exit();
}

if (isset($_POST['register_meeting'])) {
    $name = $_POST['name'];

    $checkMeeting = $conn->query("SELECT name FROM meeting WHERE name = '$name'");
    if ($checkMeeting->num_rows > 0) {
        $_SESSION['date_error'] = 'Date is already registered!';
        header("Location: schedule.php");
        exit();
    } else {
        $sql = "INSERT INTO meeting (name) VALUES ('$name')";
        $conn->query($sql);
        $_SESSION['meeting_success'] = 'Meeting created successfully!';
        header("Location: schedule.php");
        exit();
    }
}

if (isset($_POST['delete_meeting'])) {
    $meeting_id = $_POST['meeting_id'];

    $sql = "DELETE FROM meeting WHERE meeting_id = $meeting_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['meeting_deleted'] = 'Meeting deleted successfully!';
        header("Location: schedule.php");
        exit();
    } else {
        echo "Error deleting file: " . mysqli_error($conn);
        header("Location: schedule.php");
        exit();
    }
} else {
    echo "Invalid request!";
}



if (isset($_POST['schedule_upload'])) {
    $meeting_id = $_POST['meeting_id'];
    $schedule_date = $_POST['schedule_date'];
    $schedule_time = $_POST['schedule_time'];
    $description = $_POST['description'];


    $sql = "INSERT INTO schedule (meeting_id, schedule_date, schedule_time, description) VALUES ('$meeting_id', '$schedule_date', '$schedule_time', '$description')";
    mysqli_query($conn, $sql);
    $_SESSION['schedule_upload_success'] = "Successfully created schedule!";
    header("Location: schedule.php");
    exit();
}







if (isset($_POST['delete_file'])) {

    $file_id = $_POST['file_id'];

    //Get the file name from the database
    $stmt = $conn->prepare("SELECT file_name FROM meeting_file WHERE file_id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filename = $row['file_name'];

   
        $pdfPath  = "files/" . $filename;
        $txtPath  = "files/" . pathinfo($filename, PATHINFO_FILENAME) . ".txt";
        $hocrPath = "files/" . pathinfo($filename, PATHINFO_FILENAME) . ".hocr";


        if (file_exists($pdfPath)) unlink($pdfPath);
        if (file_exists($txtPath)) unlink($txtPath);
        if (file_exists($hocrPath)) unlink($hocrPath);

        //Delete the record from the database
        $deleteStmt = $conn->prepare("DELETE FROM meeting_file WHERE file_id = ?");
        $deleteStmt->bind_param("i", $file_id);

        if ($deleteStmt->execute()) {
            $_SESSION['delete_pdf_success'] = "File deleted successfully!";
            header("Location: home.php");
            exit();
        } else {
            $_SESSION['delete_pdf_error'] = "Error deleting file!";
        }
    } else {
        $_SESSION['delete_pdf_error'] = "Error deleting file!";
    }
} else {
    $_SESSION['delete_pdf_error'] = "Error deleting file!";
}


if (isset($_POST['delete_user'])) {

    $user_id = $_POST['user_id'];

    $sql = "DELETE FROM user WHERE user_id = $user_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['delete_user_success'] = "Faculty member deleted successfully!";
        header("Location: registration.php");
        exit();
    } else {
        // echo "Error deleting file: " . mysqli_error($conn);
        header("Location: registration.php");
        exit();
    }
} else {
    echo "Invalid request!";
}



if (isset($_POST['delete_schedule'])) {

    $schedule_id = $_POST['schedule_id'];

    $sql = "DELETE FROM schedule WHERE schedule_id = $schedule_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['schedule_deleted'] = 'Schedule deleted successfully!';
        header("Location: schedule.php");
        exit();
    } else {

        header("Location: schedule.php");
        exit();
    }
} else {
    echo "Invalid request!";
}



//Add Participant
if (isset($_POST['add_participant'])) {
    $meeting_id = $_POST['meeting_id'];
    $user_id = $_POST['user_id'];

    $checkParticipant = $conn->query("SELECT meeting_id,user_id FROM participant WHERE meeting_id = '$meeting_id' && user_id = '$user_id'");
    if ($checkParticipant->num_rows > 0) {
        $_SESSION['register_error'] = 'User is already registered!';
        header("Location: participants.php");
        exit();
    } else {
        $sql = "INSERT INTO participant (meeting_id, user_id) VALUES ('$meeting_id', '$user_id')";
        mysqli_query($conn, $sql);
        header("Location: schedule.php");
        exit();
    }
}


if (isset($_POST['change_admin_password'])) {
    $user_id = $_SESSION['user_id'];
    $password = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new != $confirm) {
        $_SESSION['not_match'] = "New passwords do not match!";
        header("Location: admin_profile.php");
        exit();
    } else {
        $query = $conn->query("SELECT password FROM user WHERE user_id='$user_id'");
        $row = $query->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $conn->query("UPDATE user SET password='$hashed' WHERE user_id='$user_id'");
            $_SESSION['password_changed'] = "Password Changed Successfully!";
        } else {
            $_SESSION['incorrect_current'] = "Current password is incorrect!";
        }
        header("Location: admin_profile.php");
        exit();
    }
}

if (isset($_POST['change_user_password'])) {
    $user_id = $_SESSION['user_id'];
    $password = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new != $confirm) {
        $_SESSION['not_match'] = "New passwords do not match!";
        header("Location: user_profile.php");
        exit();
    } else {
        $query = $conn->query("SELECT password FROM user WHERE user_id='$user_id'");
        $row = $query->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $conn->query("UPDATE user SET password='$hashed' WHERE user_id='$user_id'");
            $_SESSION['password_changed'] = "Password Changed Successfully!";
        } else {
            $_SESSION['incorrect_current'] = "Current password is incorrect!";
        }
        header("Location: user_profile.php");
        exit();
    }
}



if (isset($_POST['pdf_upload'])) {

    $schedule_id = $_POST['schedule_id'];
    $description = $_POST['description'];
    $filename = $_FILES['uploadfile']['name'];
    $tempname = $_FILES['uploadfile']['tmp_name'];

    
    if (!is_dir("files")) mkdir("files", 0777, true);

    $pdfPath = "files/" . $filename;

   
    $check = $conn->prepare("SELECT * FROM meeting_file WHERE schedule_id = ?");
    $check->bind_param("i", $schedule_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        header("Location: upload_pdf.php");
        exit();
    }

   
    if (!move_uploaded_file($tempname, $pdfPath)) {
        $_SESSION['file_upload_error'] = "Failed to upload file!";
        header("Location: upload_pdf.php");
        exit();
    }

    // OCR PROCESS
    // Poppler path
    $popplerPath = "C:\\Program Files\\poppler-25.07.0\\Library\\bin\\pdftoppm.exe";

    // Convert PDF to PNG images
    exec("\"$popplerPath\" -png \"$pdfPath\" \"files/page\" 2>&1", $popplerOutput);

    // Debug Poppler output (optional, remove after testing)
    // echo "<pre>Poppler output:\n"; print_r($popplerOutput); echo "</pre>";

    // Tesseract path
    $tesseractPath = "C:\\Program Files\\Tesseract-OCR\\tesseract.exe";

    $finalText = "";

    
    foreach (glob("files/page-*.png") as $img) {

      
        exec("\"$tesseractPath\" \"$img\" \"files/tmp_text\" --oem 3 --psm 3 2>&1", $tessOutput);

        // Debug Tesseract output (optional)
        // echo "<pre>Tesseract output:\n"; print_r($tessOutput); echo "</pre>";

        if (file_exists("files/tmp_text.txt")) {
            $text = trim(file_get_contents("files/tmp_text.txt"));

            if (strlen($text) > 5) {
                $finalText .= "\n" . $text;
            }

            unlink("files/tmp_text.txt");
        }

       
        unlink($img);
    }


    $textFile = "files/" . pathinfo($filename, PATHINFO_FILENAME) . ".txt";
    file_put_contents($textFile, $finalText);

    //insert into database
    $sql = "INSERT INTO meeting_file (file_name, description, schedule_id) 
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $filename, $description, $schedule_id);

    if ($stmt->execute()) {
        $_SESSION['file_upload_success'] = "File uploaded successfully!";
    } else {
        $_SESSION['message'] = "Database error!";
    }

    header("Location: home.php");
    exit();
}





if (isset($_POST['forgot_password'])) {

    $email = $_POST['email'];

    $plainPassword = bin2hex(random_bytes(4)); // 8 characters
    // Hash the password before saving to DB
    $password = password_hash($plainPassword, PASSWORD_DEFAULT);


    
    $result = $conn->query("SELECT * FROM user WHERE email = '$email'");

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();
        $user_id = $user['user_id'];
        $name = $user['name'];

       
        $update = $conn->query("UPDATE user SET password='$password' WHERE user_id='$user_id'");

        $subject = "Password Reset for Faculty Board Meeting Management System";
        $body = "Dear <b>{$name}</b>,<br>
             We received a request to reset your password for your account.<br><br>
             

             Temporary Password: <b>{$plainPassword}</b><br><br>

             Please use this temporary password to log in to your account. After logging in, 
             we strongly recommend that you change your password immediately for security purposes.<br><br>

             If you did not request this password reset, please ignore this email or contact
             our support team immediately.<br><br>


             Thank you for your cooperation.<br><br>

             Best regards,<br>
             Faculty Board Meeting Management System Administrator";


        if ($update) {
            SendEmail($email, $body, $subject);
            $_SESSION['changed_success'] = "You will receive an email!";
            header("Location: forgot_password.php");
            exit();
        } else {
            $_SESSION['changed_error'] = "Error updating password!";
            header("Location: forgot_password.php");
            exit();
        }
    } else {
        $_SESSION['email_not_found'] = "Email not found!";
        header("Location: forgot_password.php");
        exit();
    }
}

$conn->close();
