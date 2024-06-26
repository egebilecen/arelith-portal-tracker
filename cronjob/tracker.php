<?php

if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']))
    define("MAIN_PATH", "C:\\wamp64\\www\\arelith-portal-tracker\\cronjob\\");
else
    define("MAIN_PATH", "/home/username/");

require_once(MAIN_PATH . "config.php");
require_once(MAIN_PATH . "portal_util.php");

require_once(MAIN_PATH . "libs/file_util.php");
require_once(MAIN_PATH . "libs/request_util.php");

set_time_limit(0);

//------ Main
// debug_var(NULL);

for ($i = 0; $i < TRACKER_RETRY_COUNT; $i++) {
    $portal_request = send_get_request(PORTAL_URL);

    if ($portal_request[0] != 200) {
        $log_str  = "[" . get_current_time() . "] - GET ERROR - Status code is not 200. (Status code: " . $portal_request[0] . ")";

        if ($i != TRACKER_RETRY_COUNT - 1)
            $log_str .= " Sending request again.";

        $log_str .= "\n";
        write_to_file(TRACKER_LOG_FILE, $log_str);

        if ($i == TRACKER_RETRY_COUNT - 1)
            die();
    } else break;
}

$api_data = json_decode($portal_request[1], true);

foreach ($api_data["players"] as $elem) {
    $player_name    = $elem["playerName"];
    $character_name = $elem["visibleName"];
    $portrait       = $elem["portraitResRef"];

    if (
        !TRACKER_SAVE_DISGUISED
        && ($portrait    == "/images/portraits/po_hu_m_99_.jpg"
            || $portrait == "/images/portraits/po_hu_f_99_.jpg")
        && $player_name == $character_name
    ) {
        continue;
    }

    try {
        add_new_player($player_name);
        add_new_character($player_name, $character_name, $portrait);

        add_new_player_activity($player_name);
        add_new_character_activity($character_name);
    } catch (PDOException $e) {
        write_to_file(TRACKER_LOG_FILE, "[" . get_current_time() . "] - PDOException occured. Player name: " . $player_name . ", character name: " . $character_name . ". (Exception: " . $e->getMessage() . ")\n");
        break;
    }
}

write_to_file(TRACKER_LOG_FILE, "[" . get_current_time() . "] - Successfully fetched data.\n");
