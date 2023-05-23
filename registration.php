<?php
require 'sanitize.php';
require 'auth.php';

function render_registration_form ()
{ ?>



    <form method="POST" enctype="multipart/form-data">
        <p>
            <input type="text" name="username" size="100" maxlength="100" placeholder="Представьтесь пожалуйста" required>
        </p>

        <p>
            <input type="text" name="email" size="100" maxlength="50" placeholder="Введите Email" required>
        </p>
        <p>
        <div>
            <b>Пароль</b><br>
            <input type="password" name="password_first">
        </div>

        <div>
            <b>Подтверждение пароля</b><br>
            <input type="password" name="password_second">
        </div>


    </>
    <p>
        <b>Немного о себе:</b><br>
        <textarea name="about_me" cols="80" rows="10"></textarea>
    </p>

    <p>
        <b>Ваша фотография:</b><br>
        <input type="file" name ='user_photo'>
    </p>

    <p>
        <input type="submit" value="Зарегистрироваться">
    </p>
    </form>



<?php

}


function process_registration_form()
{
    $error_arr = [];
    $username = array_key_exists('username', $_POST) ? $_POST['username'] : '';
    $filtered_username = filter_string($username);

    if (!$filtered_username) {
        $errors_arr[] = 'Вы не указали свое имя';
    }


    $email = array_key_exists('email', $_POST) ? $_POST['email'] : '';
    $filtered_email = filter_email($email);

    if (!$filtered_email) {
        $errors_arr[] = 'Вы не указали свой email';
    }


    $password_first = array_key_exists('password_first', $_POST) ? $_POST['password_first'] : '';
    $filtered_password_first = filter_string($password_first);

    $password_second = array_key_exists('password_second', $_POST) ? $_POST['password_second'] : '';
    $filtered_password_second = filter_string($password_second);

    if (!$password_first || !$password_second) {
        $errors_arr[] = 'Вы не указали пароль';
    }

    if ($filtered_password_first !== $filtered_password_second) {
        $errors_arr[] = 'Пароль не подтвержден, либо подтвержден неверно.';
    }



    $about_me = array_key_exists('about_me', $_POST) ? $_POST['about_me'] : '';
    $filtered_about_me = filter_string($about_me);


    $user_photo = upload_user_photo();
    if (!$user_photo) {
        $errors_arr[] = 'Не удалось загрузить фотографию';
    }



    $content_html = '';

    if ($errors_arr) {
        $content_html .= '<p>';
        $content_html .= implode('<br>', $errors_arr);
        $content_html .= '</p>';

        return $content_html;
    }

$user_id = registration_user_to_db($filtered_username,$filtered_email,$filtered_password_first, $filtered_about_me, $user_photo);
if($user_id){
    $content_html .='Вы зарегистрированы <br>';
    $content_html .= '<a href="/">Войти на сайт</a>';
}
return $content_html;
}



function upload_user_photo(): ?string
{
    if (!isset($_FILES['user_photo'])) {
        return false;
    }

    $filename = $_FILES['user_photo']['name'];
    $tmp_path = $_FILES['user_photo']['tmp_name'];

    $photo_dir = 'photo';
    if (!file_exists($photo_dir)) {
        mkdir($photo_dir);
    }

    $new_path = __DIR__ . DIRECTORY_SEPARATOR . $photo_dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($tmp_path, $new_path)) {
        return null;
    }

    return $filename;
}


?>

<!DOCTYPE html>
<html lang="ru">
<head>

    <title>Регистрация пользователей</title>
</head>

<body text="#ff00ff">


<?php

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    echo process_registration_form();
}else{
    render_registration_form();
}

?>
</body>
</html>