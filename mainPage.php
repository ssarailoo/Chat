<?php
require_once "DatabaseConnection.php";
session_start();
$errors = $_SESSION['chatErrors'] ?? "";
$userLogged = $_SESSION['user'];
$addFriendError = $_SESSION['addFriendError'] ?? '';
unset($_SESSION['addFriendError']);
$bioUserLogged = file_get_contents($userLogged['bio']);
$profilePicUserLogged = $userLogged['profile_pic'];

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

            <div class="col-md-6 col-lg-5 col-xl-5 mb-4 mb-md-0 first  ">


                <div class="card mask-custom">
                    <h5 class="font-weight-bold mb-3 text-center text-white">People Nearby</h5>
                    <div class="card-body">

                        <ul class="list-unstyled mb-0">
                            <?php
                            $con = DatabaseConnection::getInstance();
                            $pdo = $con->getConnection();
                            $sql = 'Select * from users';
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $usersData = $stmt->fetchAll();

                            foreach ($usersData as $i => $user) {
                                $username = $user['username'];
                                if ($username == $userLogged['username'])
                                    continue;
                                $bio = file_get_contents($user['bio']);
                                $profilePic = $user['profile_pic'];
                                ?>
                                <li class="p-2 border-bottom"
                                    style="border-bottom: 1px solid rgba(255,255,255,.3) !important;">
                                    <a href="#!" class="d-flex text-decoration-none justify-content-between link-light">
                                        <div class="d-flex flex-row justify-content-between">
                                            <img src="<?= $profilePic ?>"
                                                 alt="avatar"
                                                 class="rounded-circle d-flex align-self-center me-3 shadow-1-strong"
                                                 width="60">
                                            <div class="pt-1">
                                                <p class="fw-bold mb-0"><?= $username ?></p>
                                                <p class="small text-white"><?= $bio ?></p>
                                            </div>
                                            <div>
                                                <button class="btn"><a class="btn btn-secondary bg-opacity-50 "
                                                                       href="addFriend.php?friend=<?= $username ?>">Add
                                                        Friend</a></button>
                                                <h5 class="text-danger"><?php if (!empty($addFriendError) && $addFriendError[1] == $username) echo $addFriendError[0]; ?></h5>
                                            </div>
                                        </div>

                                    </a>
                                </li>
                            <?php } ?>

                        </ul>

                    </div>
                </div>

            </div>

            <div class="col-md-6 col-lg-7 col-xl-7 second">
                <p class="text-blue-500">Welcome,
                    <b><?php echo $userLogged['is_admin'] ? $userLogged['username'] . "(Admin)" : $userLogged['username']; ?></b>
                </p>
                <?php
                {
                $con = DatabaseConnection::getInstance();
                $pdo = $con->getConnection();
                $sql = 'Select * from public_chats';
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $chatsInfos = $stmt->fetchAll();
                foreach ($chatsInfos
                as $index => $chat) {
                $id = $chat['id'];
                if ($chat['user_id'] == $userLogged['id']){
                if (empty($chat['message']))
                    continue;
                $sql='SELECT profile_pic,username FROM users WHERE id=:id';
                $stmt=$pdo->prepare($sql);
                $stmt->execute(['id'=>$chat['user_id']]);
                $userData=$stmt->fetch();
                $profile = $userData['profile_pic'];
                $username = $userData['username'];
                $time= $chat['send_at'];
                if ($userLogged['is_admin'])
                    $chat['message'] = $chat['message'] . " <form action='delete.php' method='post'><button type='submit'  name='delete' value='$id'>Delete</button></form>";

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
                        <img src="<?=$profile ?>" alt="avatar"
                             class="rounded-circle d-flex align-self-start ms-3 shadow-1-strong" width="60">
                    </li>
                    <?php } else {
                        if (empty($chat['message']))
                            continue;
                        if ($userLogged['is_admin'])
                            $chat['message'] = $chat['message'] . " <form action='delete.php' method='post'><button type='submit'  name='delete' value='$id'>Delete</button></form>";
                        $profile = $userData['profile_pic'];
                        $username = $userData['username'];
                        $time = $chat['send_at'];

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
                        <form action="chat.php" method="post" enctype="multipart/form-data">

                                <textarea class="form-control" id="textAreaExample3" name="message" rows="4"></textarea>
                                <label class="form-label" for="textAreaExample3">Message</label>
                                <input type="file" name="image"><br>


                            <button type="submit" name="send" class="btn btn-light btn-lg btn-rounded float-end">Send
                            </button>
                            <?php if (!empty($errors))
                            foreach ($errors
                            as $error) { ?>
                                <p class="h4 text-danger"> <?php echo $error . "<br>"; ?> </p>
                            <?php }
                            unset($_SESSION['chatErrors'] ); ?>
                        </form>
            </div>

                    </li>
                </ul>

            </div>
            <div class=" col-md-6 col-lg-5 col-xl-5 mb-4 mb-md-0 third">


                            <div class="card mask-custom">
                                <div class="card-body">

                                    <ul class="list-unstyled mb-0">
                                        <li class="p-2 border-bottom"
                                            style="border-bottom: 1px solid rgba(255,255,255,.3) !important;">
                                            <a href="#!"
                                               class="d-flex text-decoration-none  justify-content-between link-light">
                                                <div class="d-flex flex-row">
                                                    <img src="<?= $profilePicUserLogged ?>"
                                                         alt="avatar"
                                                         class="rounded-circle d-flex align-self-center me-3 shadow-1-strong"
                                                         width="60">
                                                    <div class="pt-1">
                                                        <p class="fw-bold mb-0"><?= $userLogged['username'] ?></p>
                                                        <p class="small text-white"><?= $bioUserLogged ?></p>
                                                    </div>
                                                </div>
                                                <div class="pt-1">
                                                    <button class="btn"><a class="btn btn-secondary bg-opacity-50 "
                                                                           href="profile.php">Profile</a></button>
                                                </div>
                                            </a>
                                        </li>
                                        <?php if ($userLogged['is_admin']) { ?>
                                            <li>
                                                <form class=" my-3" action="block.php" method="post">
                                                    <label class="mx-2" for="username">Username</label>
                                                    <input  type="text" name="username">
                                                    <div class="d-flex mt-2 justify-content-around">
                                                    <input  type="submit" name="block" value="block">
                                                    <input  type="submit" name="unblock" value="unblock">
                                                    </div>
                                                </form>
                                            </li>
                                        <?php }
                                        $sql="Select username,profile_pic,bio,hashed_username From user_friends join users u on user_friends.friend_id = u.id  ";
                                        $stmt=$pdo->prepare($sql);
                                        $stmt->execute();
                                        $friendsData=$stmt->fetchAll();
                                        {
                                       ?>

                                        <h5 class="font-weight-bold mb-3 text-center text-white">Friends</h5>
                                        <?php
                                        foreach ($friendsData as $i => $friendData) {

                                                    $friendBio = file_get_contents($friendData['bio']);
                                                    $friendProfilePic = $friendData['profile_pic'];
                                                    $friendUsername = $friendData['username'];
                                                    $friendHashedUsername = $friendData['hashed_username'];
                                                    ?>
                                                    <li class="p-2 border-bottom"
                                                        style="border-bottom: 1px solid rgba(255,255,255,.3) !important;">
                                                        <a href="#!"
                                                           class="d-flex text-decoration-none  justify-content-between link-light">
                                                            <div class="d-flex flex-row">
                                                                <img src="<?= $friendProfilePic ?>"
                                                                     alt="avatar"
                                                                     class="rounded-circle d-flex align-self-center me-3 shadow-1-strong"
                                                                     width="60">
                                                                <div class="pt-1">
                                                                    <p class="fw-bold mb-0"><?= $friendUsername ?></p>
                                                                    <p class="small text-white"><?= $friendBio ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="pt-1">
                                                                <button class="btn"><a
                                                                        class="btn btn-secondary bg-opacity-50 "
                                                                        href="unfriend.php?unfriend=<?= $friendUsername ?>">Unfriend
                                                                    </a></button>
                                                                <button class="btn"><a
                                                                        class="btn btn-secondary bg-opacity-50 "
                                                                        href="privateChat.php?from=<?php echo $userLogged['hashed_username'] ?>&to=<?php echo $friendHashedUsername ?>">Message
                                                                    </a></button>
                                                            </div>
                                                        </a>
                                                    </li>
                                                <?php }
                                                  }
                                        ?>

                                    </ul>

                                </div>
                            </div>

            </div>

        </div>

    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
</body>
</html>