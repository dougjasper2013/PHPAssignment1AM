<?php
    
    require './PHPMailer/PHPMailerAutoload.php';

    function send_mail($to_address, $to_name, $from_address, $from_name,
        $subject, $body, $is_body_html = false)
    {
        if(!valid_email($to_address))
        {
            throw new Exception('This To address is invalid: ' . htmlspecialchars($to_address));
        }

        if(!valid_email($from_address))
        {
            throw new Exception('This From address is invalid: ' . htmlspecialchars($from_address));
        }

        $mail = new PHPMailer();

        // ***** You must change the following to match you SMTP server and account information. *****
        $mail->isSMTP();
        $mail->Host = 'smtp.somehost.com'; // provides server, smtp.somehost.com is just an example
        $mail->SMTPSecure = 'tls'; // valid values provided by host
        $mail->Port = 587; // supplied by host, 587 is just an example
        $mail->SMTPAuth = true;
        $mail->Username = 'YOUR_USERNAME@somehost.com'; // again this just an example
        $mail->Password = 'YOUR_APP_PASSWORD'; // still just an example

        // ***** These were examples. True requirements would be supplied by host. *****

        // Set From address, To address, subject and body
        $mail->setFrom($from_address, $from_name);
        $mail->addAddress($to_address, $to_name);

        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);

        if ($is_body_html)
        {
            $mail->isHTML(true);
        }

        if (!$mail->send())
        {
            // throw new Exception('Error sending email: ' . htmlspecialchars($mail->ErrorInfo));
        }

    }

    function valid_email($email) // validates that the email address conforms to the proper format for an email
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) == false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }



?>