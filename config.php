<?php

set_time_limit(0);
date_default_timezone_set("Europe/Istanbul");
session_start();

$db_host = 'localhost';
$db_name = 'arelith_portal_tracker';
$db_user = 'root';
$db_pass = '';

try
{
    $db = new PDO('mysql:host='.$db_host.';dbname='.$db_name.';', $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
    ## CHARSET ##
    $db->query("SET NAMES utf8");
    $db->query("SET CHARACTER SET utf8");
}
catch(PDOException $e)
{
    $data = [
        "code" => -1,
        "data" => array(),
        "text" => "Cannot connect to database!"
    ];

    die(json_encode($data));
}

function print_json($data)
{
    echo json_encode($data);
}

function return_json($data)
{
    die(print_json($data));
}

function debug_var($var)
{
    var_dump($var);
    die();
}

function login_check()
{
    if(!$_SESSION["is_logged"]) header("Location: index.php");
}

?>
