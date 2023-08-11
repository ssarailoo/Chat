<?php
require_once "DatabaseConnection.php";
session_start();

$con = DatabaseConnection::getInstance();
$pdo = $con->getConnection();
$sql = 'Select * from users';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usersData = $stmt->fetchAll();


if (isset($_POST['submit'])) {
    $userName = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    $enteredReg = [
        'username' => $userName,
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'profilePic' => './storage/pictures/defaultPic.png',
        'bio' => './storage/defaultBio.txt',

    ];

    if (validationReg($enteredReg)) {
        $hashedUser = hash('ripemd160', $enteredReg['username']);
        $enteredReg['hashed_username'] = $hashedUser;
        $con = DatabaseConnection::getInstance();
        $pdo = $con->getConnection();
        $sql = "INSERT INTO users(username, name, email, password, hashed_username)
                       VALUES(:username,:name,:email,:password,:hashed_username)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $enteredReg['username'], 'name' => $enteredReg['name'],
            'email' => $enteredReg['email']
            , 'password' => $enteredReg['password'], 'hashed_username' => $enteredReg['hashed_username']]);
        header('location:signin.php');
    } else  header('location:signup.php');
}
if (isset($_POST['Login'])) {
    $enteredLog['username'] = $_POST['username-log'];
    $enteredLog['password'] = $_POST['password-log'];
    loginValidation($enteredLog) ? header('location:mainPage.php') : header('location:signin.php');

}

/**
 * @param array $inputs
 * @param bool $bail
 * @return bool|array
 */
function validationReg(array $inputs): bool|array
{
    $bail = ['username' => true,
        'name' => true,
        'email' => true,
        'password' => true];
    global $usersData;
    $registerErrors = array();
    /*
    not filled
    */
    if (!filled($inputs, 'username')) {
        $registerErrors['username'][] = 'please enter your username';
        $bail['username'] = false;
    }
    if (!filled($inputs, 'name')) {
        $registerErrors['name'][] = 'please enter your name';
        $bail['name'] = false;
    }

    if (!filled($inputs, 'email')) {
        $registerErrors['email'][] = 'please enter your email';
        $bail['email'] = false;
    }
    if (!filled($inputs, 'password')) {
        $registerErrors['password'][] = 'please enter your password';
        $bail['password'] = false;
    }
    /*
    username
    */
    if ($bail['username'] && !validCharsForUser($inputs['username'])) {
        $registerErrors['username'][] = "you must enter A-Z and 0-9 as characters";

    }
    if ($bail['username'] && !numOfChar($inputs['username'])) {
        $registerErrors['username'][] = "you must enter 3 to 32 characters";

    }

    if ($bail['username'] && !isExisted('username', $inputs['username'])) {
        $registerErrors['username'][] = "Entered username is existed";
    }
    /*
  name
 */
    if ($bail['name'] && !validCharsForName($inputs['name'])) {
        $registerErrors['name'][] = 'you must enter a-z and 0-9 as characters';
    }
    if ($bail['name'] && !numOfChar($inputs['name'])) {
        $registerErrors['name'][] = 'you must enter 3 to 32 characters';
    }
    /*
    Email
    */
    if ($bail['email'] && !isEmail($inputs['email'])) {
        $registerErrors['email'][] = 'please enter a valid email';
    }
    if ($bail['email'] && !isExisted('email', $inputs['email'])) {
        $registerErrors['email'][] = 'Entered Email is existed';
    }
    /*
    password
     */
    if ($bail['password'] && !numOfCharsForPass($inputs['password'])) {
        $registerErrors['password'][] = 'you must enter 4 to 32 characters';
    }

    if (empty($registerErrors))
        return $inputs;
    else {
        $_SESSION['errors-register'] = $registerErrors;
        return false;
    }
}

//


/**
 * @param array $inputs
 * @param bool $bail
 * @return bool|array
 */
function loginValidation(array $inputs, bool $bail = true): bool
{

    $loginErrors = array();
    if (!filled($inputs, 'username')) {

        $loginErrors['username'][] = 'Please Enter a Username';
        $bail = false;
    }
    if (!filled($inputs, 'password')) {
        $loginErrors['password'][] = 'Please Enter your password';
        $bail = false;
    }
    if ($bail && !matchUserPass($inputs['username'], $inputs['password'])) {
        $loginErrors['main'][] = 'Entered username and password does not match';
    }

    if (empty($loginErrors)) {
        $con = DatabaseConnection::getInstance();
        $pdo = $con->getConnection();
        $sql = "SELECT * from users WHERE username='$inputs[username]'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        unset($data['password']);
        $_SESSION['user'] = $data;

        return true;
    } else {
        $_SESSION['errors-login'] = $loginErrors;
        return false;
    }

}


/**
 * @param array $inputs
 * @param string $name
 * @return bool
 */
function filled(array $inputs, string $name): bool
{
    return (isset($inputs[$name]) && !empty($inputs[$name]));
}

/**
 * @param string $input
 * @return false|int
 */
function validCharsForUser(string $input): false|int
{
    return preg_match("/[a-zA-Z0-9]/", $input);
}

function numOfChar(string $input): false|int
{
    return preg_match("/.{3,32}/", $input);

}

/**
 * @param string $col
 * @param string $value
 * @return bool
 */
function isExisted(string $col, string $value): bool
{
    $con = DatabaseConnection::getInstance();
    $pdo = $con->getConnection();
    $sql = "SELECT $col FROM users WHERE $col='$value'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return !$stmt->fetch();
}


/**
 * @param string $input
 * @return false|int
 */
function validCharsForName(string $input): false|int
{
    return preg_match("/[a-z\s]/", $input);
}

/**
 * @param string $input
 * @return false|int
 */
function numOfCharsForPass(string $input): false|int
{
    return preg_match("/.{4,32}/s", $input);
}

/**
 * @param string $email
 * @return mixed
 */
function isEmail(string $email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * @param string $username
 * @param string $password
 * @return bool
 */
function matchUserPass(string $username, string $password): bool
{
    $con = DatabaseConnection::getInstance();
    $pdo = $con->getConnection();
    $sql = "SELECT password FROM users WHERE username='$username'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch()['password'] == $password;
}

