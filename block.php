<?php
if (isset($_POST['block'])) {
    $username = $_POST['username'];
    $users = json_decode(file_get_contents('./storage/users.json') ,true);
    foreach ($users as $index => $user) {
        if ($user['username'] == $username)
            $user['isBlocked'] = true;
        $users[$index] = $user;
    }
    file_put_contents('./storage/users.json', json_encode($users, JSON_PRETTY_PRINT));
    header('location:mainPage.php');
}
if (isset($_POST['unblock'])) {
    $username = $_POST['username'];
    $users = json_decode(file_get_contents('./storage/users.json'),true);
    foreach ($users as $index => $user) {
        if ($user['username'] == $username)
            $user['isBlocked'] = false;
        $users[$index] = $user;
    }
    file_put_contents('./storage/users.json', json_encode($users, JSON_PRETTY_PRINT));
    header('location:mainPage.php');

}