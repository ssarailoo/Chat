<?php
session_start();
$admin=$_SESSION['user']['username'];
date_default_timezone_set("Asia/Tehran");

if (isset($_POST['delete'])) {
    $number = $_POST['delete'];
    $chats = json_decode(file_get_contents('./storage/chat.json'), true);
    foreach ($chats as $index => $chat) {
        if ($index + 1 == $number) {
            $chat['message'] = '';
            $chat['deletedByAdmin'] = $admin;
            $chat['deletedTime'] = date("y/m/d h:i:s");
            $chats[$index] = $chat;
            break;
        }
    }
    file_put_contents('./storage/chat.json', json_encode($chats, JSON_PRETTY_PRINT));
    header('location:mainPage.php');
}

