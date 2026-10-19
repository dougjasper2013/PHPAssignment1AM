```php
<?php
    session_start();
    date_default_timezone_set("America/Toronto");

    // Those on Atlantic Time Zone can use:
    // date_default_timezone_set("America/Halifax");

    require_once("database.php");

    $user_name = filter_input(INPUT_POST, 'user_name');
    $user_password = filter_input(INPUT_POST, 'password');

    $queryUsers = 'SELECT * FROM registrations WHERE userName = :userName';

    $statement = $db->prepare($queryUsers);
    $statement->bindValue(':userName', $user_name);
    $statement->execute();
    $user = $statement->fetch();
    $statement->closeCursor();

    if ($user)
    {
        $now = new DateTime();

        // Check whether the account is currently locked.
        if ($user['failedAttempts'] >= 3 && $user['lastFailedLogin'] !== NULL)
        {
            $last_failed = new DateTime($user['lastFailedLogin']);

            $interval = $now->getTimestamp() - $last_failed->getTimestamp();

            if ($interval < 300)
            {
                $remaining = 300 - $interval;

                $_SESSION['login_error'] = "Account locked. Try again in "
                    . ceil($remaining) . " second(s).";

                header("Location: login_form.php");
                exit();
            }
        }

        // Verify the password.
        if (password_verify($user_password, $user['password']))
        {
            // Login successful.
            $_SESSION['isLoggedIn'] = TRUE;

            $query = "UPDATE registrations
                      SET failedAttempts = 0,
                          lastFailedLogin = NULL
                      WHERE userName = :userName";

            $statement = $db->prepare($query);
            $statement->bindValue(':userName', $user_name);
            $statement->execute();
            $statement->closeCursor();

            $_SESSION['userName'] = $user['userName'];
            $_SESSION['userID'] = $user['userID'];

            header("Location: login_confirmation.php");
            exit();
        }
        else
        {
            // Incorrect password: increment the failed-attempt counter.
            $query = "UPDATE registrations
                      SET failedAttempts = COALESCE(failedAttempts, 0) + 1,
                          lastFailedLogin = NOW()
                      WHERE userName = :userName";

            $statement = $db->prepare($query);
            $statement->bindValue(':userName', $user_name);
            $statement->execute();
            $statement->closeCursor();

            // Retrieve the updated failed-attempt count.
            $query = "SELECT failedAttempts, lastFailedLogin
                      FROM registrations
                      WHERE userName = :userName";

            $statement = $db->prepare($query);
            $statement->bindValue(':userName', $user_name);
            $statement->execute();
            $updatedUser = $statement->fetch();
            $statement->closeCursor();

            // Lock the account immediately after the third incorrect attempt.
            if ($updatedUser['failedAttempts'] >= 3)
            {
                $_SESSION['login_error'] =
                    "Account locked. Try again in 300 second(s).";
            }
            else
            {
                $_SESSION['login_error'] = "Incorrect Password";
            }

            header("Location: login_form.php");
            exit();
        }
    }
    else
    {
        $_SESSION['login_error'] = "User not found.";
        header("Location: login_form.php");
        exit();
    }
?>
```