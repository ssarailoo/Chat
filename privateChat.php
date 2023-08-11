<?php
session_start();
$userLogged = $_SESSION['user'];
date_default_timezone_set("Asia/Tehran");
$to = $_GET['to'];
$users = json_decode(file_get_contents('./storage/users.json'), true);
foreach ($users
         as $i => $user) {
    if ($user['hashedUsername'] == $to) {
        $to = ["username" => $user['username'],
            "name" => $user['name'],
            "email" => $user['email'],
            "password" => $user['password'],
            "profilePic" => $user['profilePic'],
            "bio" => $user['bio'],
            "isAdmin" => $user['isAdmin'],
            "isBlocked" => $user['isBlocked'],
            "friends" => $user['friends'],
            "hashedUsername" => $user['hashedUsername']
        ];

    }
}

$number=1;
if (is_file('./storage/privateChat.json')) {
    $jsonChatArray = json_decode(file_get_contents('./storage/privateChat.json'), true);
    $number=count($jsonChatArray)+1 ;

}
$user = $userLogged['username'];
$user .= $userLogged['isAdmin'] ? " (admin):" : " :";
if (isset($_POST['send'])) {
    $privateChatErrors = array();
    if ($userLogged['isBlocked']) {
        $privateChatErrors['blocked'] = "You are blocked from chat!";
    } else {
        if (isLong($_POST['privateMessage'])) {
            $privateChatErrors['messageLength'] = "You must add 1 to 100 characters as message";
        } else if (!empty($_POST['privateMessage'])) {
            $message = $_POST['privateMessage'];
            $message =  stripslashes(htmlspecialchars($message));

            $chatInfo = [
                'from' => $userLogged['username'],
                'fromProfile'=>$userLogged['profilePic'],
                'number' => $number,
                'time' => date("y/m/d h:i:s"),
                'to' => $to['username'],
                'toProfile'=>$to['profilePic'],
                'message' => $message
            ];
            $jsonChatArray[] = $chatInfo;
            file_put_contents('./storage/privateChat.json', json_encode($jsonChatArray, JSON_PRETTY_PRINT));


        }
        $image = $_FILES['privateImage'];
        $name = $image['name'];
        $size = $image['size'];
        if (isLargImage($size)) {
            $privateChatErrors['sizeImage'] = "Image is too large";
        } else if ($size > 0) {
            move_uploaded_file($image['tmp_name'], "./storage/pictures/chatPics/private/$name");
            $imageDir = "./storage/pictures/chatPics/private/$name";
            $data =  "<img style='width: 150px;height: 150px' src='$imageDir' >";
            $chatInfo = [
                'from' => $userLogged['username'],
                'fromProfile'=>$userLogged['profilePic'],
                'number' => $number,
                'time' => date("y/m/d h:i:s"),
                'to' => $to['username'],
                'toProfile'=>$to['profilePic'],
                'message' => $data
            ];
            $jsonChatArray[] = $chatInfo;
            file_put_contents('./storage/privateChat.json', json_encode($jsonChatArray, JSON_PRETTY_PRINT));


        }

    }


}
if (isset($_POST['deletePrivate'])) {
    $number = $_POST['deletePrivate'];
    $chats = json_decode(file_get_contents('./storage/privateChat.json'), true);
    foreach ($chats as $index => $chat) {
        if ($index + 1 == $number) {
            $chat['message'] = '';
            $chat['deletedTime'] = date("y/m/d h:i:s");
            $chats[$index] = $chat;
            break;
        }
    }
    file_put_contents('./storage/privateChat.json', json_encode($chats, JSON_PRETTY_PRINT));

}
if (isset($_POST['back'])){
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
    return $size > 2 * 1024 * 1024;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <title>Document</title>
    <style>
        .gradient-custom {
            /* fallback for old browsers */
            background: #fccb90;

            /* Chrome 10-25, Safari 5.1-6 */
            background: -webkit-linear-gradient(to bottom right, rgba(252, 203, 144, 1), rgba(213, 126, 235, 1));

            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            background: linear-gradient(to bottom right, rgba(252, 203, 144, 1), rgba(213, 126, 235, 1));
            padding-left: 0;
        }

        .first {
            width: 23rem;
            margin-left: -100px;
        }

        .second {
            width: 30rem;
        }

        .third {
            width: 23rem;
            margin-left: ;
            margin-right: -500px;

        }

        .kadr {
            width: 25rem;
        }


        .mask-custom {
            background: rgba(24, 24, 16, .2);
            border-radius: 2em;
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.05);
            background-clip: padding-box;
            box-shadow: 10px 10px 10px rgba(46, 54, 68, 0.03);
        }
    </style>
</head>
<body style="padding: 0;margin: 0">
<section class="gradient-custom" style="padding: 0">
    <div class="container  py-5" style="width: 100rem;">

        <div class="row w-100 ">


            <div class="col-md-6 col-lg-7 col-xl-7 second">

                <?php
                if (file_exists("./storage/privateChat.json") && filesize("./storage/privateChat.json") > 0) {
                    $chatsInfos = json_decode(file_get_contents("./storage/privateChat.json"), true);
                    foreach ($chatsInfos as $index => $chat) {
                        $number = $chat['number'];
                if (($chat['from'] == $userLogged['username'] && $chat['to'] == $to['username']) || ($chat['from'] == $to['username'] && $chat['to'] == $userLogged['username'])){
                if ($chat['from'] == $userLogged['username']){
                if (empty($chat['message']))
                    continue;
                    $profile = $chat['fromProfile'];
                    $username = $chat['from'];
                    $time = $chat['time'];
                            if ($userLogged['username'] == $chat['from'])
                                $chat['message'] =  $chat['message']. " <form action='' method='post'><button type='submit'  name='deletePrivate' value='$number'>Delete</button></form>";




                ?>
                <ul class="list-unstyled text-white">
                    <li class="d-flex justify-content-between mb-4 notLogged">
                        <div class="card mask-custom w-100">
                            <div class="card-header d-flex justify-content-between p-3"
                                 style="border-bottom: 1px solid rgba(255,255,255,.3);">

                                <p class="text-light small mb-0"><i class="far fa-clock"></i><?= $time ?></p>
                                <p class="fw-bold mb-0"><?= $username ?></p>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">
                                    <?= $chat['message'] ?>
                                </p>
                            </div>
                        </div>
                        <img src="<?= $profile ?>" alt="avatar"
                             class="rounded-circle d-flex align-self-start ms-3 shadow-1-strong" width="60">
                    </li>
                    <?php }   else {
                        if (empty($chat['message']))
                            continue;
                        $profile = $chat['fromProfile'];
                        $username = $chat['from'];
                        $time = $chat['time'];
                        if ($userLogged['username'] == $chat['from'])
                            $chat['message'] =  $chat['message']. " <form action='' method='post'><button type='submit'  name='deletePrivate' value='$number'>Delete</button></form>";

                        ?>
                    <li class="d-flex justify-content-between mb-4 logged">
                        <img src="<?= $profile ?>" alt="avatar"
                             class="rounded-circle d-flex align-self-start me-3 shadow-1-strong" width="60">
                        <div class="card mask-custom kadr">
                            <div class="card-header d-flex justify-content-between p-3"
                                 style="border-bottom: 1px solid rgba(255,255,255,.3);">
                                <p class="fw-bold mb-0"><?= $username ?></p>
                                <p class="text-light small mb-0"><i class="far fa-clock"></i> <?= $time ?> </p>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">
                                    <?= $chat['message'] ?>
                                </p>
                            </div>
                        </div>
                    </li>


                    <?php }
                    }
                    }} ?>
                    <li class="mb-3">
                        <div class="form-outline form-white">
                            <form action="" method="post" enctype="multipart/form-data">

                                <textarea class="form-control" id="textAreaExample3" name="privateMessage"
                                          rows="4"></textarea>
                                <label class="form-label" for="textAreaExample3">Message</label>
                                <input type="file" name="privateImage"><br>


                                <button type="submit" name="send" class="btn btn-light btn-lg btn-rounded float-end">
                                    Send
                                </button>
                                <h3 class="text-danger"><?php if (!empty($privateChatErrors)){
                                    foreach ($privateChatErrors as $error)
                                    echo $error."<br>";
                                    } ?></h3>
                                <button class="btn btn-lg btn-secondary text-white" type="submit" name="back" >back</button>
                            </form>

                        </div>

                    </li>
                </ul>

            </div>


        </div>

    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
</body>
</html>