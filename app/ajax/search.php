<?php

session_start();

#check if the user is logged in
if(isset($_SESSION['username'])){
    #check if the key is submitted
    if(isset($_POST['key'])){
        #database connection file
        include '../db.conn.php';

        #creating simple search algorithim
        $key = "%{$_POST['key']}%";

        $sql = "SELECT * FROM users WHERE username LIKE ? OR name LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$key,$key]);

        if($stmt->rowCount()>0){ 
            $users = $stmt->fetchAll();

foreach($users as $user){
    if($user['user_id'] == $_SESSION['user_id']) continue;
            ?>
            <li class="list-group-item">
            <a href="chat.php?user= <?= $user['username'] ?>" class="d-flex justify-content-between align-item-center p-2">
                <div class="d-flex align-items-center">
                    <img src="../php-chat-app/upload/<?= $user['p_p'] ?>" alt="" srcset="" class="w-10 rounded-circle">
                    <h3 class="fs-xs m2">
                        <?= $user['name'] ?>
                    </h3>
                </div>
                <div title="online">
                    <div class="online">

                    </div>
                </div>
            </a>
        </li>
      <?php   } }else{ ?>

            <div class="alter alter-info text-center">
                <i class="fa fa-user-times d-block fs-big"></i>
                The useer "<?=htmlspecialchars($_POST['key'])?>" is not found.
            </div>

            <?php }
    }

}else {
    header("Location:../../index.php");
    exit;
}
