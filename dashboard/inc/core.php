<?php

if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']))
    define("MAIN_PATH", "C:\\wamp64\\www\\arelith-portal-tracker\\");
else
    define("MAIN_PATH", "/home/username/");

require_once(MAIN_PATH . "config.php");
require_once(MAIN_PATH . "portal_util.php");
require_once(MAIN_PATH . "libs/request_util.php");
