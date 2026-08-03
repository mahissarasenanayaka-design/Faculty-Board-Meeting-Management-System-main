<?php
include 'connect.php';
include 'email.php';

$sql = "SELECT * FROM failed_email";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $id = $row['id'];
        $email = $row['email'];
        $subject = $row['subject'];
        $body = $row['body'];

        try {

            sendEmail($email, $body, $subject);

            // Delete if email sent successfully
            $delete = "DELETE FROM failed_email WHERE id = $id";
            $conn->query($delete);

            echo "Retry email sent to $email <br>";
        } catch (Exception $e) {

            echo "Still failed for $email <br>";
        }
    }
} else {

    echo "No failed emails";
}
