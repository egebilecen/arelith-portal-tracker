<?php

define("FETCHER_LOG_FILE", getcwd()."/fetcher.log");
define("FETCHER_RETRY_COUNT", 5);
define("FETCHER_SAVE_DISGUISED", 1);
define("PORTAL_URL", "https://portal.nwnarelith.com/");
define("PORTAL_UPDATE_INTERVAL", 15); // minute
define("STR_ARRAY_SEPARATOR", "|");
define("DATE_FORMAT", "d/m/Y H:i:s");

//------ Time Functions
function get_current_time()
{
    return date(DATE_FORMAT, time());
}

function convert_time_str($str)
{
    return date_create_from_format(DATE_FORMAT, $str);
}

//------ Array Functions
function append_to_str_list($str_list, $val, $sep=STR_ARRAY_SEPARATOR)
{
    $arr_list = explode($sep, $str_list);
    array_push($arr_list, $val);

    return implode($sep, $arr_list);
}

//------ DB Utility Functions
//---- General
function update_column($table, $column, $val, $where, $where_val)
{
    global $db;

    $query = $db->prepare("UPDATE ".$table." SET ".$column."=? WHERE ".$where."=?");
    $res   = $query->execute([$val, $where_val]);

    if(!$res) write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - update_column() - SQL query failed.\n");

    return $res;
}

//---- Player
function add_new_player($player_name)
{
    global $db;

    if(is_player_exist($player_name)) return false;

    $query = $db->prepare("INSERT INTO players SET player_id=NULL,
                                                   player_name=?,
                                                   player_date=CURRENT_TIMESTAMP,
                                                   player_activity=?");
    $res   = $query->execute([$player_name, get_current_time()]);

    if(!$res)
    {
        write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - add_new_player() - SQL query failed.\n");
        return false;
    }

    return true;
}

function add_new_player_activity($player_name)
{
    $player_data = get_player_data_from_name($player_name);

    $res = update_column("players",
                         "player_activity", 
                         append_to_str_list($player_data["player_activity"], get_current_time()),
                         "player_name", $player_name);

    if(!$res) write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - add_new_character_activity() - Failed.\n");

    return $res;
}

function get_player_id_from_name($player_name)
{
    $player_id = -1;

    if(!is_player_exist($player_name, $player_id)) return -1;

    return $player_id;
}

function get_player_name_from_id($player_id)
{
    global $db;

    $player_name = "";

    $query = $db->prepare("SELECT player_name FROM players WHERE player_id=?");
    $res   = $query->execute([$player_id]);

    if(!$res) write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - get_player_name_from_id() - SQL query failed.\n");
    else      $player_name = $query->fetch(PDO::FETCH_ASSOC)["player_name"];
    
    return $player_name;
}

function get_player_data_from_name($player_name)
{
    global $db;

    $query = $db->prepare("SELECT * FROM players WHERE player_name=?");
    $res   = $query->execute([$player_name]);

    if(!$res)
    {
        write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - get_player_data_from_name() - SQL query failed.\n");
        return false;
    }

    return $query->fetch(PDO::FETCH_ASSOC);
}

function is_player_exist($player_name, &$id_out = NULL)
{
    global $db;

    $query = $db->prepare("SELECT player_id FROM players WHERE player_name=?");
    $res   = $query->execute([$player_name]);

    if(!$res)
    {
        write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - is_player_exist() - SQL query failed.\n");
        return false;
    }

    if($id_out != NULL) $id_out = intval($query->fetch(PDO::FETCH_ASSOC)["player_id"]);

    return $query->rowCount() > 0;
}

//---- Character
function add_new_character($player_name, $character_name, $character_portrait="")
{
    global $db;
    
    if(is_character_exist($character_name))
    {
        // Player changed the player name
        $player_id      = get_player_id_from_name($player_name);
        $character_data = get_character_data_from_name($character_name);

        if(!in_array($player_id, explode(STR_ARRAY_SEPARATOR, $character_data["character_player_id"])))
        {
            update_column("characters",
                          "character_player_id", 
                          append_to_str_list($character_data["character_player_id"], $player_id),
                          "character_name", $character_name);
        }

        return false;
    }

    $player_id = get_player_id_from_name($player_name);

    $query = $db->prepare("INSERT INTO characters SET character_id=NULL,
                                                      character_name=?,
                                                      character_portrait=?,
                                                      character_player_id=?,
                                                      character_date=CURRENT_TIMESTAMP,
                                                      character_activity=?");
    $res   = $query->execute([$character_name, $character_portrait, $player_id, get_current_time()]);

    if(!$res)
    {
        write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - add_new_character() - SQL query failed.\n");
        return false;
    }

    return true;
}

function add_new_character_activity($character_name)
{
    $character_data = get_character_data_from_name($character_name);

    $res = update_column("characters",
                         "character_activity", 
                         append_to_str_list($character_data["character_activity"], get_current_time()),
                         "character_name", $character_name);

    if(!$res) write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - add_new_character_activity() - Failed.\n");

    return $res;
}

function get_character_id_from_name($character_name)
{
    $character_id = -1;

    if(!is_character_exist($character_name, $character_id)) return -1;

    return $character_id;
}

function get_character_data_from_name($character_name)
{
    global $db;

    $query = $db->prepare("SELECT * FROM characters WHERE character_name=?");
    $res   = $query->execute([$character_name]);

    if(!$res)
    {
        write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - get_character_data_from_name() - SQL query failed.\n");
        return false;
    }

    return $query->fetch(PDO::FETCH_ASSOC);
}

function is_character_exist($character_name, &$id_out = NULL)
{
    global $db;

    $query = $db->prepare("SELECT character_id FROM characters WHERE character_name=?");
    $res   = $query->execute([$character_name]);

    if(!$res)
    {
        write_to_file(FETCHER_LOG_FILE, "[".get_current_time()."] - is_character_exist() - SQL query failed.\n");
        return false;
    }

    if($id_out != NULL) $id_out = intval($query->fetch(PDO::FETCH_ASSOC)["character_id"]);

    return $query->rowCount() > 0;
}

?>