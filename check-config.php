<?php

if ( !file_exists("config.php") ) {
    header("Location: install.php");
    die("<script>window.location='install.php'</script>");
}
require_once 'config.php';
require_once 'db.php';
$db = new DB($cfg["db"]["host"], $cfg["db"]["port"], $cfg["db"]["name"], $cfg["db"]["user"], $cfg["db"]["pass"]);

function requireApiKey() {
    global $cfg;
    header("Content-type: application/json");
    if ( empty($_REQUEST["apikey"]) || $_REQUEST["apikey"] != $cfg["api_key"] ) {
        http_response_code(403);
        die(json_encode(array("error"=>"A valid API key is required")));
    }
}