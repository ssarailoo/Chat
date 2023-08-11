<?php

session_start();
date_default_timezone_set("Asia/Tehran");
$userdata = $_SESSION['user'];
$number=1;
if (is_file('./storage/chat.json')) {
    $jsonChatArray = json_decode(file_get_contents('./storage/chat.json'), true);
    $number=count($jsonChatArray)+1 ;
}
$user = $userdata['username'];
$user .= $userdata['isAdmin'] ? " (admin):" : " :";
if (isset($_POST['send'])) {
    $chatErrors = array();
    if ($userdata['isBlocked']) {
        $chatErrors['blocked'] = "You are blocked from chat!";
    } else {
        if (isLong($_POST['message'])) {
            $chatErrors['messageLength'] = "You must add 1 to 100 characters as message";
        } else if (!empty($_POST['message'])) {
            $message = $_POST['message'];
            $message =  stripslashes(htmlspecialchars($message)) ;
            $profilePicDir=$userdata['profilePic'];
            $chatInfo = [
                'number' => $number,
                'time' => date("y/m/d h:i:s"),
                'user' => $userdata['username'],
                'profilePic'=>$profilePicDir,
                'message' => $message
            ];
            $jsonChatArray[] = $chatInfo;
            file_put_contents('./storage/chat.json', json_encode($jsonChatArray, JSON_PRETTY_PRINT));
        }
        $image = $_FILES['image'];
        $name = $image['name'];
        $size = $image['size'];
        if (isLargImage($size)) {
            $chatErrors['sizeImage'] = "Image is too large";
        } else if ($size > 0) {
            move_uploaded_file($image['tmp_name'], "./storage/pictures/chatPics/$name");
            $imageDir = "./storage/pictures/chatPics/$name";
            $data =  "<img style='width: 150px;height: 150px' src='$imageDir' >";
            $profilePicDir=$userdata['profilePic'];

            $chatInfo = [
                'number' => $number,
                'time' => date("y/m/d h:i:s"),
                'user' => $userdata['username'],
                'profilePic'=>$profilePicDir,
                'message' => $data
            ];
            $jsonChatArray[] = $chatInfo;
            file_put_contents('./storage/chat.json', json_encode($jsonChatArray, JSON_PRETTY_PRINT));


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
    return $size > 0.1 * 1024 * 1024;
}2

?>


