<?php
session_start();
$userlogged = $_SESSION['user']['username'];


$friendUsername = $_GET['friend'];

$users = json_decode(file_get_contents('./storage/users.json'), true);
foreach ($users as $user) {
    if ($user['username'] == $friendUsername) {
        $hashedUsername = $user['hashedUsername'];
        $friendProfile=$user['profilePic'];
        $friendBio=$user['bio'];

    }
}
foreach ($users as $index => $user) {
    if ($user['username'] == $userlogged) {
        foreach ($user['friends'] as $friend) {
            if ($friend['username'] == $friendUsername) {
                $errorAddFriend = ["you have already added $friendUsername", $friendUsername];
                $_SESSION['addFriendError'] = $errorAddFriend;
                header('location:mainPage.php');

            }
        }
        if (!isset($errorAddFriend)) {

            $user['friends'][] =
                [
                    'profile' => $friendProfile,
                    'username' => $friendUsername,
                    'bio' => $friendBio,
                    'hashedUsername' => $hashedUsername
                ];

            $users[$index] = $user;

        }
    }
}
file_put_contents('./storage/users.json', json_encode($users, JSON_PRETTY_PRINT));
header('location:mainPage.php');