<?php
require_once 'mysqli.php';
// Для сложности взлома
const SECRET_SALT = 'askdjlasjqq21';

const USER_SESSION_COOKIE_NAME = 'user_session';

function get_user_id_by_email_and_password(strint $email, string $password): ?int
{
// Связываемся с базой и получаем имейл и пароль
    $mysqli = db_connect();
    $password_hash = generate_password($password);

    $query ='SELECT ID from users where email = ? AND password = ?';
    $statement = mysqli_prepare($mysqli, $query);
    mysqli_bind_param($statement, 'ss', ...[$email, $password_hash]);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if ($result === false){
        return null;
    }

    $row = mysqli_fetch_assoc($result);

    if (!$row){
        return null;
    }

    $user_id = $row['ID'];
    return $user_id;
}

function generate_user_session_id(int $user_id)
{
    return md5(SECRET_SALT . $user_id);

}
function generate_password(string $password)
{
    return md5(SECRET_SALT . $password);

}


function set_user_session_id(int $user_id){
    //получаем сгенерированный айди
    $session_id = generate_user_session_id();
    // делаем куки
    setcookie(USER_SESSION_COOKIE_NAME, $session_id, strtotime('+30 days'));
    // save in base
    $mysqli = db_connect();
    $query = 'UPDATE users set session_id = ? where ID = ?';
    $statement = mysqli_prepare($mysqli, $query);
    mysqli_bind_param($statement, 'si', ...[$session_id, $user_id]);
    mysqli_stmt_execute($statement);

}

function get_user_id_by_session_id(string $session_id)
{
    $mysqli = db_connect();
    $query ='SELECT ID from users where session_id = ? AND password = ?';
    $statement = mysqli_prepare($mysqli, $query);
    mysqli_bind_param($statement, 's', ...[$session_id]);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);


    if ($result === false){
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    if (!$row){
        return null;
    }
    $user_id = $row['ID'];
    return $user_id;
}

function get_user_id()
{
    $session_id = array_key_exists(USER_SESSION_COOKIE_NAME, $_COOKIE) ? $_COOKIE[USER_SESSION_COOKIE_NAME]: null;
    if (!$session_id) {
        return null;
    }
        $user_id = get_user_id_by_session_id($session_id);
        return $user_id;
}

function registration_user_to_db(string $username, string $email, string $password, string $about_me, string $user_photo): int
{
    $mysqli = db_connect();
    $password_hash = generate_password($password);


    $query = "INSERT INTO users SET username = ?, email = ?, password = ?, about_me = ?, user_photo = ?";
    $statement = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($statement, 'sssss', ...[$username, $email, $password_hash, $about_me, $user_photo]);
    mysqli_stmt_execute($statement);

    $user_id = mysqli_insert_id($mysqli);

    return $user_id;
}