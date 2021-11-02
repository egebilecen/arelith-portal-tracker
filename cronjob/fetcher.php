<?php

$MAIN_PATH = "../";

require_once($MAIN_PATH."config.php");
require_once($MAIN_PATH."portal_util.php");

require_once($MAIN_PATH."libs/file_util.php");
require_once($MAIN_PATH."libs/request_util.php");
require_once($MAIN_PATH."libs/simple_html_dom/simple_html_dom.php");

set_time_limit(0);

//------ Main
debug_var(NULL);

$portal_request = send_get_request(PORTAL_URL);

if($portal_request[0] != 200)
{
    write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - GET ERROR - Status code is not 200. (Status code: ".$portal_request[0].")\n");
    die();
}

$portal_html = str_get_html($portal_request[1]);

foreach($portal_html->find("div.player") as $elem)
{
    $player_name    = $elem->find("div.player-name")[0]->innertext;
    $character_name = $elem->find("div.character-name")[0]->innertext;
    $portrait       = $elem->find("div.back > img")[0]->src;

    // Skip disguised
    if(($portrait    == "portraits/po_hu_m_99_.jpg"
        || $portrait == "portraits/po_hu_f_99_.jpg")
    && $player_name == $character_name)
    {
        continue;
    }

    $is_player_added    = add_new_player($player_name);
    $is_character_added = add_new_character($player_name, $character_name, $portrait);

    if(!$is_player_added)
        add_new_player_activity($player_name);

    if(!$is_character_added)
        add_new_character_activity($character_name);
}

write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - Successfully fetched data.\n");

?>
