<?php

include '../db.conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if username, password, and name are submitted
    if (isset($_POST['username'], $_POST['password'], $_POST['name'])) {
        $name = $_POST['name'];
        $password = $_POST['password'];
        $username = $_POST['username'];

        $data = 'name=' . $name . '&username=' . $username;

        #simple form Validation
        if (empty($name)) {
            redirectToSignupWithError("Name is required", $data);
        } elseif (empty($username)) {
            redirectToSignupWithError("Username is required", $data);
        } elseif (empty($password)) {
            redirectToSignupWithError("Password is required", $data);
        } else {
            $sql = "SELECT username FROM users WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username]);

            if ($stmt->rowCount() > 0) {
                redirectToSignupWithError("The username ($username) is taken", $data);
            } else {
                $imgUploadResult = handleProfilePictureUpload($username);

                if (!$imgUploadResult['success']) {
                    redirectToSignupWithError($imgUploadResult['error'], $data);
                }

                $password = password_hash($password, PASSWORD_DEFAULT);
                $insertSql = "INSERT INTO users (name, username, password, p_p) VALUES (?,?,?,?)";
                $insertValues = [$name, $username, $password, $imgUploadResult['new_img_name'] ?? null];
                $stmt = $conn->prepare($insertSql);
                $stmt->execute($insertValues);

                redirectToIndexWithSuccess("Account created successfully");
            }
        }
    } else {
        header("Location: ../../sign-up.php");
        exit;
    }
}

function handleProfilePictureUpload($username)
{
    $img_name = $_FILES['profilepicture']['name'];
    $tmp_name = $_FILES['profilepicture']['tmp_name'];
    $error = $_FILES['profilepicture']['error'];

    if ($error === 0) {
        $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_exs = array("jpg", "jpeg", "png");

        if (in_array($img_ex, $allowed_exs)) {
            $new_img_name = $username . '.' . $img_ex;

            //get project name and store 
            $projectName = getProjectName();
            // Construct the absolute path to the root directory
            $rootDirectory = $_SERVER['DOCUMENT_ROOT'];
            $uploadsDirectory = $rootDirectory ."/" . $projectName . '/upload/';

            $img_upload_path = $uploadsDirectory . $new_img_name;

            move_uploaded_file($tmp_name, $img_upload_path);

            return ['success' => true, 'new_img_name' => $new_img_name];
        } else {
            return ['success' => false, 'error' => "You can't upload files of this type"];
        }
    } else {
        return ['success' => true];
    }
} 

function getProjectName()
{
    // Get the current URL
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Parse the URL to extract the path
    $urlParts = parse_url($currentUrl);
    $path = $urlParts['path'];

    // Split the path into segments
    $pathSegments = explode('/', trim($path, '/'));
    $projectName = 'default';
    // The project name is usually the first segment after the domain (if using a typical structure)
    if (count($pathSegments) >= 2) {
        $projectName = $pathSegments[0];
    } 

    return $projectName;


}

function redirectToSignupWithError($errorMessage, $data)
{
    $url = "../../sign-up.php?errors=$errorMessage&$data";
    header("Location: $url");
    exit;
}

function redirectToIndexWithSuccess($successMessage)
{
    $url = "../../index.php?success=$successMessage";
    header("Location: $url");
    exit;
}
