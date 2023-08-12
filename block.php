<?php
require_once "DatabaseConnection.php";
$con = DatabaseConnection::getInstance();
$pdo = $con->getConnection();

if (isset($_POST['block'])) {
    $username = $_POST['username'];
    $stmt = $pdo->prepare("Update users SET is_blocked=1 WHERE username=:username");
    $stmt->execute(['username' => $username]);
    header('location:mainPage.php');
}
if (isset($_POST['unblock'])) {
    $username = $_POST['username'];
    $stmt = $pdo->prepare("Update users SET is_blocked=0 WHERE username=:username");
    $stmt->execute(['username' => $username]);;
    header('location:mainPage.php');

}