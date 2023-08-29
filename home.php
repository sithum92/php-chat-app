<?php
session_start();

if (isset($_SESSION['username'])) {
    include 'app/db.conn.php';
    include 'app/helpers/user.php';
    include 'app/helpers/conversations.php';
    include 'app/helpers/timeAgo.php';

    # Getting User data data
    $user = getUser($_SESSION['username'], $conn);
    # Getting User conversations
    $conversations = getConversations($user['user_id'], $conn);
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
        <link rel="stylesheet" href="./css/style.css">
        <link rel="icon" href="img/logo.png">
        <script src="https://kit.fontawesome.com/e3119e6791.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css" integrity="sha384-QYIZto+st3yW+o8+5OHfT6S482Zsvz2WfOzpFSXMF9zqeLcFV0/wlZpMtyFcZALm" crossorigin="anonymous">
        <title>Chat App - Home</title>
    </head>

    <body class="d-flex justify-content-center align-items-center vh-100">
        <div class="p-2 w-400 rounded shadow">
            <div>

                <div class="d-flex mb-3 p-3 bg-light justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="./upload/<?= $user['p_p'] ?>" alt="" class="w-25 rounded-circle">
                        <h3 class="fs-xs m-2">
                            <?= $user['name'] ?>
                        </h3>
                    </div>
                    <a href="logout.php" class="btn btn-dark">Logout</a>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search..." name="searchText" id="searchText">
                    <button class="btn btn-primary" id="searchBtn">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                <?php foreach ($conversations as $conversation) { ?>
                    <?php if (last_seen($conversation['last_seen']) == "Active") { ?>
                        <div title="online">
                            <div class="online">

                            </div>
                        <?php } ?>
                        </div>
                    <?php } ?>



                    <ul class="list-group mvh-50 overflow-auto" id="chatList">
                        <?php if (!empty($conversations)) {
                            foreach ($conversations as $conversation) { ?>
                                <li class="list-group-item">
                                    <a href="chat.php?user= <?= $conversation['username'] ?>" class="d-flex justify-content-between align-item-center p-2">
                                        <div class="d-flex align-items-center">
                                            <img src="../php-chat-app/upload/<?= $conversation['p_p'] ?>" alt="" srcset="" class="w-10 rounded-circle">
                                            <h3 class="fs-xs m2">
                                                <?= $conversation['name'] ?>
                                            </h3>
                                        </div>
                                        <div title="online">
                                            <div class="online">

                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php }
                        } else { ?>
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fa fa-comments d-block fs-big"></i>
                                No messages yet, Start the conversation
                            </div>
                        <?php } ?>
                    </ul>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script>
            $(document).ready(function() {
                // When the input with id "searchText" detects an input event (typing), execute the following function
                $("#searchText").on("input", function() {
                    // Get the value of the input field with id "searchText"
                    var searchText = $(this).val();
                    if(searchText == "")return;

                    // Make an AJAX POST request to the 'search.php' file located in the '
                    //app/ajax' directory
                    // Send the 'searchText' value as a parameter named 'key'
                    $.post('app/ajax/search.php', {
                        key: searchText
                    }, function(data, status) {
                        // This function will execute when the POST request is successful
                        // It receives 'data' (the response from the server) and 'status'

                        // Replace the content of the element with id "chatList"
                        //with the 'data' received from the server
                        $("#chatList").html(data);
                    });
                });

                // Search using the button
                $("#searchBtn").on("click", function() {
                    var searchText = $("#searchText").val();
                    if(searchText == "")return;
                    $.post('app/ajax/search.php', {
                        key: searchText
                    }, function(data, status) {
                        $("#chatList").html(data);
                    })
                });



                /** 
      auto update last seen 
      for logged in user
      **/
                let lastSeenUpdate = function() {
                    $.get("app/ajax/update_last_seen.php");
                }
                lastSeenUpdate();
                /** 
                auto update last seen 
                every 10 sec
                **/
                setInterval(lastSeenUpdate, 100);

            });
        </script>
    </body>

    </html>

<?php
} else {
    header("Location: index.php");
    exit;
}
?>