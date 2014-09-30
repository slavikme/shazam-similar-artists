<?php

require_once 'check-config.php';

requireApiKey();

switch ( $_SERVER["REQUEST_METHOD"] ) {
    case "GET":
        if ( !empty($_GET["artist_id"]) && is_numeric($_GET["artist_id"]) ) {
            $artist_id = $_GET["artist_id"];
            $similar = $db->getSimilarByArtistId($artist_id);
        } elseif ( !empty($_GET["id"]) && is_numeric($_GET["id"]) ) {
            $id = $_GET["id"];
            $similar = $db->getSimilar($id);
        } else {
            http_response_code(400);
            die(json_encode(array("error"=>"A valid 'artist_id' or 'id' must be provided")));
        }
        die(json_encode($similar));
        break;
    
    default:
        http_response_code(405);
        die(json_encode(array("error"=>"This method is not allowed here")));
}
