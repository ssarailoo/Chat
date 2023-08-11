<?php
session_start();

$jsonArray = [];

if (is_file('./storage/users.json')) {
    $jsonArray = json_decode(file_get_contents('./storage/users.json'), true);
}

if (isset($_POST['submit'])) {
    $userName = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    $user = [
        'username' => $userName,
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'profilePic' => './storage/pictures/defaultPic.png',
        'bio' => './storage/defaultBio.txt',
        'isAdmin' => false,
        'isBlocked' => false,
        'friends' => []

    ];

    if (validationReg($user)) {
       $hashedUser=hash( 'ripemd160',$user['username']);
       $user['hashedUsername']=$hashedUser;
        $jsonArray[] = $user;
        file_put_contents('./storage/users.json', json_encode($jsonArray, JSON_PRETTY_PRINT));
        header('location:signin.php');
    } else  header('location:signup.php');
}
if (isset($_POST['Login'])) {
    $user['username'] = $_POST['username-log'];
    $user['password'] = $_POST['password-log'];

    if (loginValidation($user)) {
        $clients = json_decode(file_get_contents('./storage/users.json'), true);
        foreach ($clients as $client) {
            if ($client['username'] == $user['username']) {
                $_SESSION['user'] = $client;
            }
        }
        header('location:mainPage.php');
    } else  header('location:signin.php');
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
    global $jsonArray;
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

    if ($bail['username'] && !isUnique($jsonArray, 'username', $inputs['username'])) {
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
    if ($bail['email'] && !isUnique($jsonArray, 'email', $inputs['email'])) {
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
function loginValidation(array $inputs, bool $bail = true): bool|array
{
    global $jsonArray;
    $loginErrors = array();
    if (!filled($inputs, 'username')) {

        $loginErrors['username'][] = 'Please Enter a Username';
        $bail = false;
    }
    if (!filled($inputs, 'password')) {
        $loginErrors['password'][] = 'Please Enter your password';
        $bail = false;
    }
    if ($bail && !matchUserPass($jsonArray, 'username', $inputs['username'], 'password', $inputs['password'])) {
        $loginErrors['main'][] = 'Entered username and password does not match';
    }

    if (empty($loginErrors))
        return $inputs;
    else {
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
 * @param array $array
 * @param string $key
 * @param string $target
 * @return bool
 */
function isUnique(array $array, string $key, string $target): bool
{
    $is_unique = true;
    foreach ($array as $user) {
        if ($user[$key] == $target) {
            $is_unique = false;
            break;
        }

    }
    return $is_unique;
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
 * @param array $array
 * @param string $usernameKey
 * @param string $userName
 * @param string $passwordKey
 * @param string $pass
 * @return bool
 */
function matchUserPass(array $array, string $usernameKey, string $userName, string $passwordKey, string $pass): bool
{
    foreach ($array as $user) {
        if ($user[$usernameKey] == $userName && $user[$passwordKey] == $pass) {
            return true;
        }
    }
    return false;
}

