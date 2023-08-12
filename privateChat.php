<?php
require_once "DatabaseConnection.php";
session_start();
$userLogged = $_SESSION['user'];
date_default_timezone_set("Asia/Tehran");
$pdo = DatabaseConnection::getInstance()->getConnection();
$to = $_GET['to'];
$stmt=$pdo->prepare('SELECT id from users where hashed_username=:to');
$stmt->execute(['to'=>$to]);
$to_id=$stmt->fetch()['id'];
$user = $userLogged['username'];
$user .= $userLogged['is_admin'] ? " (admin):" : " :";
if (isset($_POST['send'])) {
    $privateChatErrors = array();
    if ($userLogged['is_blocked']) {
        $privateChatErrors['blocked'] = "You are blocked from chat!";
    } else {
        if (isLong($_POST['privateMessage'])) {
            $privateChatErrors['messageLength'] = "You must add 1 to 100 characters as message";
        } else if (!empty($_POST['privateMessage'])) {
            $message = $_POST['privateMessage'];
            $message = stripslashes(htmlspecialchars($message));
            $stmt = $pdo->prepare('INSERT INTO private_chats(message,send_at,from_id,to_id )VALUES (:message,:send_at,:from_id,:to_id)');
            $stmt->execute(['message' => $message, 'send_at' => date("y/m/d h:i:s"), 'from_id' => $userLogged['id'], 'to_id' => $to_id]);


        }
        $image = $_FILES['privateImage'];
        $name = $image['name'];
        $size = $image['size'];
        if (isLargImage($size)) {
            $privateChatErrors['sizeImage'] = "Image is too large";
        } else if ($size > 0) {
            move_uploaded_file($image['tmp_name'], "./storage/pictures/chatPics/private/$name");
            $imageDir = "./storage/pictures/chatPics/private/$name";
            $data = "<img style='width: 150px;height: 150px' src='$imageDir' >";
            $stmt = $pdo->prepare('INSERT INTO private_chats( message,send_at,from_id,to_id )VALUES (:message,:send_at,:from_id,:to_id)');
            $stmt->execute(['message' => $data, 'send_at' => date("y/m/d h:i:s"), 'from_id' => $userLogged['id'], 'to_id' => $to_id]);
        }
    }


}
if (isset($_POST['deletePrivate'])) {
    $id = $_POST['deletePrivate'];
    $stmt = $pdo->prepare('DELETE FROM private_chats WHERE id=:id ');
    $stmt->execute(['id'=>$id]);

}
if (isset($_POST['back'])) {
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
              $stmt= $pdo->prepare('SELECT * FROM private_chats WHERE (from_id=:from_id AND to_id=:to_id)OR (from_id=:to_id AND to_id=:from_id) ');
               $stmt->execute(['from_id'=>$userLogged['id'],'to_id'=>$to_id]);
              $chatsInfos= $stmt->fetchAll();
                foreach ($chatsInfos

                as $index => $chat) {
                $id = $chat['id'];
               {
                if ($chat['from_id'] == $userLogged['id']){

               $stmt= $pdo->prepare("SELECT profile_pic,username from users WHERE id=:id ");
               $stmt->execute(['id'=>$userLogged['id']]);
               $userData=$stmt->fetch();
                $profile = $userData['profile_pic'];
                $username = $userData['username'];
                $time = $chat['send_at'];
                    $chat['message'] = $chat['message'] . " <form action='' method='post'><button type='submit'  name='deletePrivate' value='$id'>Delete</button></form>";
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
                    <?php } else {
                        $stmt= $pdo->prepare("SELECT profile_pic,username from users WHERE id=:to_id ");
                        $stmt->execute(['to_id'=>$to_id]);
                        $userData=$stmt->fetch();
                        $profile = $userData['profile_pic'];
                        $username = $userData['username'];
                        $time = $chat['send_at'];
                        if ($userLogged['username'] == $chat['from_id'])
                            $chat['message'] = $chat['message'] . " <form action='' method='post'><button type='submit'  name='deletePrivate' value='$id'>Delete</button></form>";

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

                    } ?>
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
                                <h3 class="text-danger"><?php if (!empty($privateChatErrors)) {
                                        foreach ($privateChatErrors as $error)
                                            echo $error . "<br>";
                                    } ?></h3>
                                <button class="btn btn-lg btn-secondary text-white" type="submit" name="back">back
                                </button>
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