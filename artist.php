<?php

require_once 'check-config.php';

requireApiKey();

switch ( $_SERVER["REQUEST_METHOD"] ) {
    case "GET":
        if ( empty($_GET["id"]) || !is_numeric($_GET["id"]) ) {
            http_response_code(400);
            die(json_encode(array("error"=>"A valid 'artist_id' must be provided")));
        }
        $id = $_GET["id"];
        $similar = $db->getArtist($id);
        die(json_encode($similar));
        break;
    
    default:
        http_response_code(405);
        die(json_encode(array("error"=>"This method is not allowed here")));
}
