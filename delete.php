<?php
require_once "DatabaseConnection.php";
session_start();

$admin=$_SESSION['user']['username'];
date_default_timezone_set("Asia/Tehran");
$pdo=DatabaseConnection::getInstance()->getConnection();
if (isset($_POST['delete'])) {
   $id= $_POST['delete'];
    $stmt = $pdo->prepare('DELETE FROM public_chats WHERE id=:id ');
    $stmt->execute(['id'=>$id]);
    header('location:mainPage.php');
}

