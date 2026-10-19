<?php
    session_start();

    require_once('message.php');

    // get data from form and assign to variables
    $user_name = filter_input(INPUT_POST, 'user_name');
    $user_password = filter_input(INPUT_POST, 'password');
    $email_address = filter_input(INPUT_POST, 'email_address');

    $hash = password_hash($user_password, PASSWORD_DEFAULT);
    
    require_once("database.php"); // require_once prevents duplicate connections to the database
    
    // Validation to be added later to ensure no null data and no duplicate contacts

    $queryUsers = 'SELECT * FROM registrations';

    $statement = $db->prepare($queryUsers);
    $statement->execute();
    $users = $statement->fetchAll();
    $statement->closeCursor();

    foreach ($users as $user) {
        if ($user_name == $user["userName"]) {
            $_SESSION["user_error"] = "Invalid data, Duplicate Username. Try again.";
            $url = "user_error.php";
            header("Location: " . $url);
            die();
        }
    }

    if ($user_name == null || $user_password == null || $email_address == null) {
            $_SESSION["user_error"] = "Invalid user data, Check all fields and try again.";
            $url = "user_error.php";
            header("Location: " . $url);
            die();
        }
    

    // Add Registration

    $query = 'INSERT INTO registrations (userName, password, emailAddress)
        VALUES (:userName, :password, :emailAddress)';

    $statement = $db->prepare($query);

    $statement->bindValue(':userName', $user_name);
    $statement->bindValue(':password', $hash);
    $statement->bindValue(':emailAddress', $email_address);    

    $statement->execute();    
    $statement->closeCursor();

    $_SESSION["userName"] = $user_name;
    $_SESSION["isLoggedIn"] = 1;

    // set up email variables
    $to_address = $email_address;
    $to_name = $user_name;
    $from_address = 'YOUR_USERNAME@somehost.com'; // example address
    $from_name = 'Contact Manager 2026 AM';
    $subject = 'Contact manager 2026 - Registration Complete';
    $body = '<p>Thanks for registering with our site.</p>' . 
        '<p>Sincerely,</p>' . 
        '<p>Contact Manager 2026</p>';
    $is_body_html = true;

    // Send email
    try
    {
        send_mail($to_address, $to_name, $from_address, $from_name, $subject, $body, $is_body_html);
    }
    catch (Exception $ex)
    {
        $_SESSION["user_error"] = $ex->getMessage();
        $url = "user_error.php";
        header("Location: " . $url);
        die();
    }

    $url = "register_user_confirmation.php";
    header("Location: " . $url);
    die();

?>
