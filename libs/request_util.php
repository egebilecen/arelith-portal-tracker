<?php

function p($key, $trim=true)
{
    $data = $_POST[$key];
    
    if($trim) $data = trim($data);

    return $data;
}

function g($key, $trim=true)
{
    $data = $_GET[$key];
    
    if($trim) $data = trim($data);

    return $data;
}

function r($key, $trim=true)
{
    $data = p($key, $trim);

    if($data == NULL) return g($key, $trim);

    return $data;
}

function get_current_url_file()
{
    return explode("?", end(explode("/", $_SERVER['REQUEST_URI'])))[0];
}

function send_get_request($url)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPGET, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36");
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

    $data = curl_exec($curl);
    $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return array($status_code, $data);
}

function send_post_request($url, $post_data)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36");
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $data = curl_exec($curl);
    $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return array($status_code, $data);
}

?>
