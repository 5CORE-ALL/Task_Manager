<?php
    $to = "tech-support@5core.com, software13@5core.com";
    $subject = "HTML Email Test";
    
    $message = "
    <html>
    <head>
        <title>Welcome Email</title>
    </head>
    <body>
        <h1>Welcome to Our Service!</h1>
        <p>Thank you for signing up.</p>
        <p><a href='https://example.com'>Click here</a> to get started.</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Your Name <admin@new-tm.5coremanagement.com>\r\n";
    $headers .= "Reply-To: admin@new-tm.5coremanagement.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    if (mail($to, $subject, $message, $headers)) {
        echo "HTML Email sent successfully!";
    } else {
        echo "Failed to send HTML email!";
    }
    ?>