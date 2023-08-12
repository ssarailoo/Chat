<?php
require_once "DatabaseConnection.php";
session_start();
$userLoggedId = $_SESSION['user']['id'];
$friendID = $_GET['friend'];
$con = DatabaseConnection::getInstance();
$pdo = $con->getConnection();
$stmt = $pdo->prepare("SELECT * from user_friends WHERE  friend_id=:friend_id");
$stmt->execute(['friend_id' => $friendID]);
if ($stmt->fetch())
 {
    $stmt = $pdo->prepare("SELECT username from users where  id=:friend_id");
    $stmt->execute(['friend_id' => $friendID]);
    $friendUsername = $stmt->fetch()['username'];
    $errorAddFriend = ["you have already added $friendUsername", $friendUsername];
    $_SESSION['addFriendError'] = $errorAddFriend;
    header('location:mainPage.php');
}

if (!isset($errorAddFriend)) {
    $stmt = $pdo->prepare('INSERT INTO user_friends(user_id,friend_id)VALUES(:user_id,:friend_id)');
    $stmt->execute(['user_id' => $userLoggedId, 'friend_id' => $friendID]);
}
header('location:mainPage.php');