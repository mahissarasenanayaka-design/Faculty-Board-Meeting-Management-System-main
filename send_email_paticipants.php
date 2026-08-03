<?php
include 'connect.php';
include 'email.php'; 

date_default_timezone_set('Asia/Colombo');

// Target date
$target_date = date('Y-m-d', strtotime('+5 days'));

// Get meetings scheduled on that date
$sql = "SELECT m.meeting_id, m.name AS meeting_name, s.schedule_date, s.schedule_time,
        u.email, u.name AS user_name
        FROM meeting m
        INNER JOIN schedule s ON m.meeting_id = s.meeting_id
        INNER JOIN participant p ON m.meeting_id = p.meeting_id
        INNER JOIN user u ON p.user_id = u.user_id
        WHERE s.schedule_date = '$target_date'";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $username = $row['user_name'];
        $meeting_name = $row['meeting_name'];
        $date = date("d M Y", strtotime($row['schedule_date']));
        $time = date("h:i A", strtotime($row['schedule_time']));

        $subject = "Reminder: Upcoming Meeting - $meeting_name";

        $body = "Dear <b>{$username}</b>,<br><br>
                 This is a reminder that you have a meeting '<b>{$meeting_name}</b>' scheduled on <b>{$date}</b> at <b>{$time}</b>.<br><br>
                 Please make sure to attend.<br><br>
                 Kind regards,<br>
                 Faculty Board Meeting Management System";

        try {
            sendEmail($email, $body, $subject);
            echo "Email sent to $email<br>";
        } catch (Exception $e) {
            // Store failed email
            $failed_email = $conn->real_escape_string($email);
            $failed_subject = $conn->real_escape_string($subject);
            $failed_body = $conn->real_escape_string($body);

            $conn->query("INSERT INTO failed_emails(email, subject, body) VALUES('$failed_email','$failed_subject','$failed_body')");
        }
    }
} else {
    echo "No meetings scheduled for 5 days from today.";
}
