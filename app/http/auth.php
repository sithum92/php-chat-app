<?php

Session_start();

#database connection file
include '../db.conn.php';

#check if username & password are submitted
if(isset($_POST['username']) && isset($_POST['password'])) {


    #get data from POST request and store them in var
    $password = $_POST['password'];
    $username = $_POST['username'];

    #simple form validation
    if(empty($username)) {
        #error message

        $errors['error'] = "Username is required";
        $errorString = http_build_query($errors);

        #redirect to 'index.php' and apssing error message
        header("Location:../../index.php?errors=$errorString");

    } elseif(empty($password)) {
        #error message

        $errors['error'] = "Password is required";
        $errorString = http_build_query($errors);

        #redirect to 'index.php' and apssing error message
        header("Location:../../index.php?errors=$errorString");
    } else {
        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);

        #if the username is exist
        if($stmt->rowCount() === 1) {
            #fetching user data
            $user = $stmt->fetch();

            #if both username's are strictly equal
            if($user['username'] === $username) {
                #verifying the encrypted password
                if(password_verify($password, $user['password'])) {
                    #successfully logged in
                    #creating the session
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['user_id'] = $user['user_id'];

                    #redirect to 'home.php'
                    header("Location:../../home.php");
                } else {
                    #error message

                    $errors['error'] = "Incorect username or password";
                    $errorString = http_build_query($errors);

                    #redirect to 'index.php' and apssing error message
                    header("Location:../../index.php?errors=$errorString");
                }
            } else {
                #error message

                $errors['error'] = "Incorect username or password";
                $errorString = http_build_query($errors);

                #redirect to 'index.php' and apssing error message
                header("Location:../../index.php?errors=$errorString");
            }
        }
    }

} else {
    #error message

    $errors['error'] = "Username & Password are required";
    $errorString = http_build_query($errors);
    header("Location:../../index.php?errors=$errorString");
    exit;
}
