<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <title>Chat App - Sign-Up</title>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="w-400 p-5 shadow rounded">

        <form method="post" action="./app/http/signup.php" enctype="multipart/form-data">

            <div class="d-flex justify-content-center flex-column">
                <h3 class="display-4 fs-1 text-center">
                    <img src="./img/logo.png" alt="" srcset="" class="w-25">
                    SIGN-UP
                </h3>
            </div>

            <!-- //Capture errors -->
            <?php if (isset($_GET['errors'])) { ?>
                <div class="alert alert-warning" role="alert">
                    <?php echo htmlspecialchars($_GET['errors']); ?>
                </div>
            <?php }

            if (isset($_GET['name'])) {
                $name = $_GET['name'];
            } else $name = '';

            if (isset($_GET['username'])) {
                $username = $_GET['username'];
            } else $username = '';
            ?>

            <div class="mb-3">
                <label class="form-label">
                    Name</label>
                <input type="text" name="name" value="<?= $name ?>" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">
                    User name</label>
                <input type="text" class="form-control" value="<?= $username ?>" name="username">
            </div>


            <div class="mb-3">
                <label class="form-label">
                    Password</label>
                <input type="password" class="form-control" name="password">
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Profile Picture</label>
                <input type="file" class="form-control" name="profilepicture">
            </div>

            <button class="btn btn-primary" type="submit">Sign Up</button>
            <a href="index.php">Log-In</a>

        </form>
    </div>
</body>

</html>