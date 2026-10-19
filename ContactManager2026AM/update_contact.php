<?php
    session_start();

    // get data from form and assign to variables
    $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_VALIDATE_INT);
    $first_name = filter_input(INPUT_POST, 'first_name');
    $last_name = filter_input(INPUT_POST, 'last_name');
    $email_address = filter_input(INPUT_POST, 'email_address');
    $phone_number = filter_input(INPUT_POST, 'phone_number');
    $birth_date = filter_input(INPUT_POST, 'birth_date');
    $type_id = filter_input(INPUT_POST, 'type_id', FILTER_VALIDATE_INT);

    // get the uploaded image (if any)
    $image = $_FILES['file1'];

    require_once("database.php"); // require_once prevents duplicate connections to the database
    require_once("image_util.php"); // for image processing functions

    // Get current contact record to check current image name

    $queryContacts = 'SELECT * FROM contacts WHERE contactID = :contact_id';

    $statement = $db->prepare($queryContacts);
    $statement->bindValue(':contact_id', $contact_id);
    $statement->execute();
    $contact = $statement->fetch();
    $statement->closeCursor();

    $old_image_name = $contact['imageName'];
    $base_dir = 'images/';
    $image_name = $old_image_name;

    // Validation to be added later to ensure no null data and no duplicate contacts

    $queryContacts = 'SELECT * FROM contacts';

    $statement = $db->prepare($queryContacts);
    $statement->execute();
    $contacts = $statement->fetchAll();
    $statement->closeCursor();

    foreach ($contacts as $contact) {
        if ($email_address == $contact["emailAddress"] && $contact_id != $contact["contactID"]) {
            $_SESSION["update_error"] = "Invalid data, Duplicate Email Address. Try again.";
            $url = "update_error.php";
            header("Location: " . $url);
            die();
        }
    }

    if ($first_name == null || $last_name == null || $email_address == null ||
        $phone_number == null || $birth_date == null || $type_id == null ) {
            $_SESSION["update_error"] = "Invalid contact data, Check all fields and try again.";
            $url = "update_error.php";
            header("Location: " . $url);
            die();
        }

    // If new image is uploaded

    if($image && $image['error'] == UPLOAD_ERR_OK)
    {
        // process new image
        $original_filename = basename($image['name']);
        $upload_path = $base_dir . $original_filename;
        move_uploaded_file($image['tmp_name'], $upload_path);

        process_image($base_dir, $original_filename);

        // save _100 version in DB
        $dot_pos = strrpos($original_filename, '.');
        $new_image_name = substr($original_filename, 0, $dot_pos) . '_100' . substr($original_filename, $dot_pos);
        $image_name = $new_image_name;

        if ($old_image_name != 'placeholder_100.jpg')
        {    
            $old_base = substr($old_image_name, 0, strrpos($old_image_name, '_100'));
            $old_ext = substr($old_image_name, strrpos($old_image_name, '.'));
            $original = $old_base . $old_ext;

            $img100 = $old_base . '_100' . $old_ext;
            $img400 = $old_base . '_400' . $old_ext;    
            
            foreach([$original, $img100, $img400] as $file)
            {
                $path = $base_dir . $file;
                if(file_exists($path)) 
                {
                    unlink($path);
                }
            }
        }
    }

    // Update contact

    $query = '
        UPDATE contacts
        SET firstName = :firstName,
            lastName = :lastName,
            emailAddress = :emailAddress,
            phoneNumber = :phoneNumber,
            dob = :birthDate,
            typeID = :typeID,
            imageName = :imageName
        WHERE contactID = :contactID';

    $statement = $db->prepare($query);

    $statement->bindValue(':firstName', $first_name);
    $statement->bindValue(':lastName', $last_name);
    $statement->bindValue(':emailAddress', $email_address);
    $statement->bindValue(':phoneNumber', $phone_number);
    $statement->bindValue(':birthDate', $birth_date);
    $statement->bindValue(':contactID', $contact_id);
    $statement->bindValue(':typeID', $type_id);
    $statement->bindValue(':imageName', $image_name);

    $statement->execute();    
    $statement->closeCursor();

    $_SESSION["fullName"] = $first_name . " " . $last_name;
    $url = "update_contact_confirmation.php";
    header("Location: " . $url);
    die();

?>
