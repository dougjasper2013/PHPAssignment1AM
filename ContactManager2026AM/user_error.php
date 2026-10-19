<?php
    session_start();
?>

<!DOCTYPE html>

<html>

    <head>
        <title>Contact Manager - User Error</title>
        <link rel="stylesheet" type="text/css" href="css/contact.css" />
    </head>

    <body>

        <?php include("header.php"); ?>

        <main>
            <h2>User Error</h2>           
            
            <p>Error Message: <?php echo $_SESSION["user_error"]; ?> </p>

            <p><a href="register_user_form.php">Register User</a></p>
            <p><a href="login_form.php">Login</a></p>
            <p><a href="index.php">View Contact List</a></p>
        </main>

        <?php include("footer.php"); ?>

    </body>

</html>