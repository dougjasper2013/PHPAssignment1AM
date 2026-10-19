<?php
    session_start();    

?>

<!DOCTYPE html>

<html>

    <head>
        <title>Contact Manager - Register User Confirmation</title>
        <link rel="stylesheet" type="text/css" href="css/contact.css" />
    </head>

    <body>

        <?php include("header.php"); ?>

        <main>
            <h2>Register User Confirmation</h2>

            <p>
                Thank you, <?php echo $_SESSION["userName"]; ?> for
                registering. Enjoy the App.
            </p>
            
            <p><a href="index.php">View Contact List</a></p>

        </main>

        <?php include("footer.php"); ?>

    </body>

</html>