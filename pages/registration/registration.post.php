<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/registration.class.php";

/**
 * POST: 
 *  sub
 *  username
 *  password
 *  confirm-password
 *  first-name
 *  last-name
 *  age
 *  gender
 *  location
 *  icon
 * 
 * Processes the user's input and registers them. Takes image name and adds it to
 * user data, and moves image to ./images/uploads/users folder.
 * @return json {"result": booelan, "error"?: string}
 */

$data = [];
$allValid = true;
$empties = '';
foreach (['username','password','confirm-password','first-name','last-name','age','gender','location'] as $p) {
    if (!isset($_POST[$p]) || empty($_POST[$p])) {
        $allValid = false;
        break;
    }
}
if ($allValid && isset($_FILES['icon'])) {              // if all post data is set
    if ($_FILES['icon']['error'] == 2) {                // if icon is too large
        $data['error'] = "File size is too large!";     // error
        $data['result'] = false;                        // result false
    } else if (substr($_FILES['icon']['type'], 0, strrpos($_FILES['icon']['type'], "/")) == "image") {  // if file is image
        $icon = substr($_FILES['icon']['name'], 0, strrpos($_FILES['icon']['name'], "."));              // get file name without extension
        $Register = new Registration();                 // set Register class
        if ($Register->register(                        // if register and successfully move file to images folder
            $_POST['username'],
            $_POST['password'],
            $_POST['confirm-password'],
            $_POST['first-name'],
            $_POST['last-name'],
            $_POST['age'],
            $_POST['gender'],
            $_POST['location'],
            $icon
        )
        && move_uploaded_file($_FILES['icon']['tmp_name'], FILE_UPLOAD_DIR . basename($_FILES['icon']['name']))) {
            $data['result'] = true;                     // result true
        } else {
            $data['error'] = $Register->error;          // error
            $data['result'] = false;                    // result false
        }
    } else {
        $data['error'] = "Icon is not an image!";       // error
        $data['result'] = false;                        // result false
    }
} else {
    $data['error'] = "One or more fields is empty!";    // error
    $data['result'] = false;                            // return false
}

echo json_encode($data);                                // send data
?>