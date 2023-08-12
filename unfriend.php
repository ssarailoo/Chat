<?php
require_once "DatabaseConnection.php";
session_start();
$friendId = $_GET['unfriend'];
$userLogged = $_SESSION['user'];
$userLoggedId = $userLogged['id'];
$con = DatabaseConnection::getInstance();
$pdo = $con->getConnection();
$stmt = $pdo->prepare("DELETE from user_friends WHERE friend_id=:friend_id ");
$stmt->execute(['friend_id' => $friendId]);
header('location:mainPage.php');