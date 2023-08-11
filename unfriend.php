<?php
session_start();
$friendUsername = $_GET['unfriend'];
$userlogged = $_SESSION['user'];
$userlogged = $userlogged['username'];

$users = json_decode(file_get_contents('./storage/users.json'), true);
foreach ($users as $index => $user) {
    if ($user['username'] == $userlogged) {
        $friends = $user['friends'];
        foreach ($friends as $i => $friend) {
            if ($friend['username'] == $friendUsername)
                unset($friends[$i]);

        }
        $user['friends'] = $friends;
        $users[$index] = $user;

    }
}
file_put_contents('./storage/users.json', json_encode($users, JSON_PRETTY_PRINT));
header('location:mainPage.php');