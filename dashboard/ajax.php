<?php
    require("../config.php");
    require("../libs/request_util.php");

    if(p("login"))
    {
        $username = p("username");
        $password = p("password");

        $query = $db->prepare("SELECT NULL FROM admins WHERE admin_username=? AND admin_password=?");
        $res   = $query->execute([$username, md5($password)]);

        if($query->rowCount() > 0)
        {
            $_SESSION["is_logged"] = true;
        }

        return_json([
            "query_success" => $res,
            "status"        => $query->rowCount()
        ]);
    }
    else if(p("search", false) && p("type"))
    {
        $sql_query;

        if(p("type") == "character")
            $sql_query = "SELECT character_name FROM characters WHERE character_name LIKE ?";
        else
            $sql_query = "SELECT player_name FROM players WHERE player_name LIKE ?";

        $sql_query .= " LIMIT 10";

        $query = $db->prepare($sql_query);
        $res   = $query->execute(["%".p("search")."%"]);

        return_json([
            "query_success" => $res,
            "type"          => p("type"),
            "data"          => $query->fetchAll(PDO::FETCH_COLUMN)
        ]);
    }
?>
