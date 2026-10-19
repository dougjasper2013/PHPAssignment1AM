<?php
    session_start();
?>

<!DOCTYPE html>

<html>

    <head>
        <title>Contact Manager - Update Error</title>
        <link rel="stylesheet" type="text/css" href="css/contact.css" />
    </head>

    <body>

        <?php include("header.php"); ?>

        <main>
            <h2>Update Error</h2>           
            
            <p>Error Message: <?php echo $_SESSION["update_error"]; ?> </p>
            
            <p><a href="index.php">View Contact List</a></p>
        </main>

        <?php include("footer.php"); ?>

    </body>

</html>