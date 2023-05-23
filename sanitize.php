<?php
function filter_string(string $str): string
{
    $filtered_str = strip_tags($str);
    $filtered_str = htmlspecialchars($filtered_str);

    return $filtered_str;
}

function filter_email(string $email): string
{
    $filtered_email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $filtered_email = filter_var($filtered_email, FILTER_VALIDATE_EMAIL);

    return $filtered_email;
}
