<?php
require_once "DatabaseConnection.php";
session_start();
date_default_timezone_set("Asia/Tehran");
$userLogged = $_SESSION['user'];
$con = DatabaseConnection::getInstance();
$pdo = $con->getConnection();

$user = $userLogged['username'];
$user .= $userLogged['isAdmin'] ? " (admin):" : " :";
if (isset($_POST['send'])) {
    $chatErrors = array();
    if ($userLogged['is_blocked']) {
        $chatErrors['blocked'] = "You are blocked from chat!";
    } else {
        if (isLong($_POST['message'])) {
            $chatErrors['messageLength'] = "You must add 1 to 100 characters as message";
        } else if (!empty($_POST['message'])) {
            $message = $_POST['message'];
            $message = stripslashes(htmlspecialchars($message));
            $stmt = $pdo->prepare('INSERT INTO public_chats(user_id, send_at, message)VALUES (:user_id,:send_at,:message)');
            $stmt->execute(['user_id' => $userLogged['id'], 'send_at' => date("y/m/d h:i:s"), 'message' => $message]);
        }
        $image = $_FILES['image'];
        $name = $image['name'];
        $size = $image['size'];
        if (isLargImage($size)) {
            $chatErrors['sizeImage'] = "Image is too large";
        } else if ($size > 0) {
            move_uploaded_file($image['tmp_name'], "./storage/pictures/chatPics/$name");
            $imageDir = "./storage/pictures/chatPics/$name";
            $data = "<img style='width: 150px;height: 150px' src='$imageDir' >";
            $stmt = $pdo->prepare('INSERT INTO public_chats(user_id, send_at, message)VALUES (:user_id,:send_at,:message)');
            $stmt->execute(['user_id' => $userLogged['id'], 'send_at' => date("y/m/d h:i:s"), 'message' => $data]);
        }

    }
    $_SESSION['chatErrors'] = $chatErrors;
    header('location:mainPage.php');
}

/**
 * @param string $message
 * @return bool
 */
function isLong(string $message): bool
{
    return strlen($message) > 100;
}

function isLargImage(int $size): bool
{
    return $size > 0.5 * 1024 * 1024;
}

2

?>


