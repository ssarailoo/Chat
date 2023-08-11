<?php
session_start();
$userName = $_SESSION['user']['username'];
if (!is_dir("./users/" . $userName)) {
    mkdir("./users/" . $userName);
}
if (!is_dir("users/$userName/pictures")) {

    mkdir("users/$userName/pictures");
}
if (!is_dir("./users/$userName/bio")) {
    mkdir("./users/$userName/bio");
}
if (isset($_POST['delete'])) {
    unlink($_POST['delete']);
}
if (isset($_POST['setProfile'])) {
    $proPicDir = $_POST['setProfile'];
    $_SESSION['user']['profilePic']=$proPicDir;
    $users = file_get_contents('./storage/users.json');
    $users = json_decode($users, true);
    foreach ($users as $i => $user) {
        if ($user['username'] == $userName) {
            $user['profilePic'] = $proPicDir;
            $users[$i] = $user;
        }
    }
    $jsonData = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents('./storage/users.json', $jsonData);
}

if (isset($_POST['submit'])) {
    $file = $_FILES['file'];
    $fileName = $file['name'];


    move_uploaded_file($file['tmp_name'], "./users/$userName/pictures/$fileName");
    if (!empty($_POST['bio'])) {
        $bio = $_POST['bio'];
        file_put_contents("./users/$userName/bio/bio.txt", $bio);
        $_SESSION['user']['bio']="./users/$userName/bio/bio.txt";
        $users = file_get_contents('./storage/users.json');
        $users = json_decode($users, true);
        foreach ($users as $i => $user) {
            if ($user['username'] == $userName) {
                $user['bio'] = "./users/$userName/bio/bio.txt";
                $users[$i] = $user;
            }
        }
        $jsonData = json_encode($users, JSON_PRETTY_PRINT);
        file_put_contents('./storage/users.json', $jsonData);
    }
}
if (isset($_POST['logout'])){
    session_unset();
    header('location:signin.php');
}
if (isset($_POST['home'])){

    header('location:mainPage.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
<body>
<section class="gradient-custom" style="padding: 0">
    <div class="container  py-5" style="width: 100rem;">
<form action="" enctype="multipart/form-data" method="post">
    <input type="text" name="bio" placeholder="bio">
    <input type="file" name="file">
    <input type="submit" name="submit">
</form>


<?php
$images = scandir("./users/$userName/pictures");

$images = array_slice($images, 2);

if (!empty($images)) {

    foreach ($images as $i => $image) {
        $imageDir = "./users/$userName/pictures/$image";
        ?>


        <img class="mt-5" style="width:150px; height: 150px" src="<?php echo $imageDir ?> ">
        <form action="" method="post">
            <button type="submit" name="delete" value="<?php echo $imageDir ?>">Delete</button>
            <button type="submit" name="setProfile" value="<?php echo $imageDir ?>">Set this as profile picture</button>
        </form>
    <?php }
} ?>
<form action="" method="post">
    <div class="d-flex flex-row justify-content-between mt-5">
        <button class="btn btn-secondary btn-lg m-3" type="submit" name="home">Group Chat</button>
        <button class="btn btn-secondary btn-lg m-3" type="submit"  name="logout">Logout</button>

    </div>
</form>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
</body>
</html>

