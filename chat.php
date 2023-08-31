<?php
session_start();

if (isset($_SESSION['username'])) {
    # database connection file
    include 'app/db.conn.php';

    include 'app/helpers/user.php';
    include 'app/helpers/chat-helper.php';
    include 'app/helpers/opened.php';

    include 'app/helpers/timeAgo.php';

    if (!isset($_GET['user'])) {
        header("Location: home.php");
        exit;
    }

    # Getting User data data
    $chatWith = getUser($_GET['user'], $conn);

    if (empty($chatWith)) {
        header("Location: home.php");
        exit;
    }

    $chats = getChats($_SESSION['user_id'], $chatWith['user_id'], $conn);

    opened($chatWith['user_id'], $conn, $chats);
    ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="d-flex
             justify-content-center
             align-items-center
             vh-100">
    <div class="w-400 shadow p-4 rounded">
        <a href="home.php" class="fs-4 link-dark">&#8592;</a>

        <div class="d-flex align-items-center">
            <img src="upload/<?=$chatWith['p_p']?>"
                class="w-15 rounded-circle">

            <h3 class="display-4 fs-sm m-2">
                <?=$chatWith['name']?> <br>
                <div class="d-flex
               	              align-items-center" title="online">
                    <?php
                            if (last_seen($chatWith['last_seen']) == "Active") {
                                ?>
                    <div class="online"></div>
                    <small class="d-block p-1">Online</small>
                    <?php } else { ?>
                    <small class="d-block p-1">
                        Last seen:
                        <?=last_seen($chatWith['last_seen'])?>
                    </small>
                    <?php } ?>
                </div>
            </h3>
        </div>

        <div class="shadow p-4 rounded
    	               d-flex flex-column
    	               mt-2 chat-box" id="chatBox">
            <?php
                     if (!empty($chats)) {
                         foreach($chats as $chat) {
                             if($chat['from_id'] == $_SESSION['user_id']) { ?>
            <p class="rtext align-self-end
						        border rounded p-2 mb-1">
                <?=$chat['message']?>
                <small class="d-block">
                    <?=$chat['created_at']?>
                </small>
            </p>
            <?php } else { ?>
            <p class="ltext border 
					         rounded p-2 mb-1">
                <?=$chat['message']?>
                <small class="d-block">
                    <?=$chat['created_at']?>
                </small>
            </p>
            <?php }
            }
                     } else { ?>
            <div class="alert alert-info 
    				            text-center">
                <i class="fa fa-comments d-block fs-big"></i>
                No messages yet, Start the conversation
            </div>
            <?php } ?>
        </div>
        <div class="input-group mb-3">
            <textarea cols="3" id="message" class="form-control"></textarea>
            <button class="btn btn-primary" id="sendBtn">
                <i class="fa fa-paper-plane"></i>
            </button>
        </div>

    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        //This function scrolls the chat box to the bottom,
        // ensuring that new messages are visible as they appear.
        var scrollDown = function() {
            let chatBox = document.getElementById('chatBox');
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        scrollDown();

        $(document).ready(function() {
            //This is the jQuery shorthand for ensuring that the enclosed code runs once the
            // DOM is fully loaded. It's good practice to put your code that relies on
            // DOM elements being present inside this block.

            $("#sendBtn").on('click', function() {
                //This function handles sending messages using AJAX. 
                //It takes the input message, posts it to the server (insert.php), 
                //and then appends the received data to the chat box.
                message = $("#message").val();
                if (message == "") return;

                $.post("app/ajax/insert.php", {
                        message: message,
                        to_id: <?=$chatWith['user_id']?>
                    },
                    function(data, status) {
                        $("#message").val("");
                        $("#chatBox").append(data);
                        scrollDown();
                    });
            });

            /** 
            auto update last seen 
            for logged in user
            **/
            // This function sends an AJAX GET request to update_last_seen.php, 
            //which presumably updates the user's last seen status. 
            //This is likely part of a real-time functionality to keep track of users
            //' online/offline status.
            let lastSeenUpdate = function() {
                $.get("app/ajax/update_last_seen.php");
            }
            lastSeenUpdate();
            /** 
            auto update last seen 
            every 10 sec
            **/
            //This sets an interval to call the lastSeenUpdate function every 10 seconds. 
            //This way, the last seen status of the user is updated at regular intervals.
            setInterval(lastSeenUpdate, 10000);



            // auto refresh / reload
            //This function sends an AJAX POST request to getMessage.php to retrieve 
            //new messages for the chat. It appends the received data to the chat box 
            //and scrolls down if new data is available.
            let fechData = function() {
                $.post("app/ajax/getMessage.php", {
                        id_2: <?=$chatWith['user_id']?>
                    },
                    function(data, status) {
                        $("#chatBox").append(data);
                        if (data != "") scrollDown();
                    });
            }

            fechData();
            /** 
            auto update last seen 
            every 0.5 sec
            **/
            // This sets an interval to call the fechData function every 0.5 seconds. 
            //This way, the chat is checked for new messages at a frequent interval, 
            //providing a near real-time chat experience.
            setInterval(fechData, 500);

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