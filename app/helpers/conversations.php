<?php

function getConversations($user_id, $conn)
{

    $sql = "SELECT c.conversation_id, u.name, u.username, u.p_p, u.last_seen
    FROM conversations c
    JOIN users u ON (c.user_1 = u.user_id OR c.user_2 = u.user_id)
    WHERE c.user_1 = ? OR c.user_2 = ?
    ORDER BY c.conversation_id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $conversations;
    } else {
        return [];
    }

}
