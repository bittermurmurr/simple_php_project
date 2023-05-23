<?php
require 'mysqli.php';


function get_programming_languages_arr(): array
{

    $query = "SELECT language_name, short_language_name FROM programming_languages ORDER BY language_name";
    return fetch_all_from_query($query);
}
function get_education_arr():array
{

    $query = "SELECT education_name, short_education_name FROM educations ORDER BY education_name";
    return fetch_all_from_query($query);

}

function get_learning_times_arr(): array
{

    $query = "SELECT title, short_name FROM learning_times ORDER BY title";
    return fetch_all_from_query($query);

}



function get_education_id_by_short_name(string $short_education_name): ?int
{
    $mysqli = db_connect();
    $query = "SELECT ID from educations where short_education_name = ?";
    $statement = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($statement, 's', ...[$short_education_name]);
    mysqli_stmt_execute($statement);

    $result = mysqli_stmt_get_result($statement);

    if ($result === false){
        return null;
    }
    $row = mysqli_fetch_assoc($result);
    return $row['ID'];

}



function get_learning_time_id_by_short_name(string $learning_time_short_name): ?int
{
    $mysqli = db_connect();
    $query = "SELECT ID from learning_times where short_name = ?";
    $statement = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($statement, 's', ...[$learning_time_short_name]);
    mysqli_stmt_execute($statement);

    $result = mysqli_stmt_get_result($statement);

    if ($result === false){
        return null;
    }
    $row = mysqli_fetch_assoc($result);
    return $row['ID'];

}

function get_programming_language_id_by_short_language_name(string $short_language_name): ?int
{
    $mysqli = db_connect();
    $query = "SELECT ID from programming_languages where short_language_name = ?";
    $statement = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($statement, 's', ...[$short_language_name]);
    mysqli_stmt_execute($statement);

    $result = mysqli_stmt_get_result($statement);

    if ($result === false){
        return null;
    }
    $row = mysqli_fetch_assoc($result);
    return $row['ID'];

}



function add_request_for_training_to_db(
    string $username,
    string $about_me,
    string $short_language_name,
    string $email,
           $education_short_name,
           $learning_time_short_name
): bool|int
{
    $mysqli = db_connect();

    $programming_language_id = get_programming_language_id_by_short_language_name($short_language_name);
    if (!$programming_language_id){
        return false;
    }

    $education_id = get_education_id_by_short_name($education_short_name);


    $learning_time_id = get_learning_time_id_by_short_name($learning_time_short_name);






    if (!$programming_language_id){
        return false;
    }

    $query = 'INSERT INTO request_for_training SET username = ?, about_me = ?, programming_language_id = ?, email =?, education_id =?, learning_time_id =?';
    $statement = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param(
        $statement,
        'ssisii',
        ...[$username, $about_me, $programming_language_id, $email, $education_id, $learning_time_id]);
    mysqli_stmt_execute($statement);



    $request_id = mysqli_insert_id($mysqli);
    return $request_id;

}
