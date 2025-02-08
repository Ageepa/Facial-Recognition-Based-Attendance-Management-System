<?php
$to = "seminisagarika@gmail.com"; // Replace with a valid email address
$subject = "Test Email";
$message = "This is a test email.";
$headers = "From: ageepafernando@gmail.com"; // Replace with a valid sender email address

if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?>
