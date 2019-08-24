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
 */

$data = [];
if (isset($_POST['sub'], $_POST['username'], $_POST['password'], $_POST['confirm-password'], $_POST['first-name'], $_POST['last-name'], $_POST['age'], $_POST['gender'], $_POST['location'], $_FILES['icon'])) {
    if ($_FILES['icon']['error'] == 2) {
        $data['error'] = "File size is too large!";
        $data['result'] = false;
    } else {
        $data['a'] = $_FILES['icon'];
        $data['b'] = substr($_FILES['icon']['type'], 0, strrpos($_FILES['icon']['type'], "/"));
        if (substr($_FILES['icon']['type'], 0, strrpos($_FILES['icon']['type'], "/")) == "image") {
            $icon = substr($_FILES['icon']['name'], 0, strrpos($_FILES['icon']['name'], "."));
            $Register = new Registration();
            if ($Register->register(
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
                $data['result'] = true;
            } else {
                $data['error'] = $Register->error;
                $data['result'] = false;
            }
        } else {
            $data['error'] = "Icon is not an image!";
            $data['result'] = false;
        }
    }
} else {
    $data['error'] = "One or more fields is empty!";
    $data['result'] = false;
}

echo json_encode($data);
?>