<?php

require_once 'check-config.php';

header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

function getSimilarArtistsFromLastfm($artist_name) {
    $apikey = "649104dbc970cbc72a343fa37daaab71";
    $artist_name = urlencode($artist_name);
    $ch = curl_init("http://ws.audioscrobbler.com/2.0/?method=artist.getSimilar&api_key=$apikey&artist=$artist_name&format=json&limit=20");
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
    ));
    $response = curl_exec($ch);
    return json_decode($response);
}

switch ( $_SERVER["REQUEST_METHOD"] ) {
    case 'GET':
        if ( empty($_GET["szid"]) || empty($_GET["name"]) ) {
            http_response_code(400);
            die(json_encode(array("error"=>"'szid' and 'name' must be provided")));
        }
        $shazam_id = $_GET["szid"];
        $name = $_GET["name"];
        if ( !is_numeric($_GET["szid"]) ) {
            http_response_code(400);
            die(json_encode(array("error"=>"'szid' must be a valid Shazam ID")));
        }
        $artist = $db->getArtistByShazamId($shazam_id);
        $artist_id = (int)$artist["id"];
        $similar = array();
        
        if ( !$artist ) {
            $artist_id = $db->createArtist($shazam_id, $name);
            $similars = getSimilarArtistsFromLastfm($name);
            if ( isset($similars->similarartists->artist) && is_array($similars->similarartists->artist) ) {
                foreach ( $similars->similarartists->artist as $sartist ) {
                    $similar[$sartist->name] = array(
                        "mbid" => $sartist->mbid,
                        "name" => $sartist->name,
                        "lastfm_link" => $sartist->url,
                        "image_url" => $sartist->image[0]->{"#text"},
                    );
                }
                $db->fillSimilarId($similar);
                $db->connectSimilarArtists($artist_id, $similar);
            }
        }
        $similar = $db->getSimilarByShazamId($shazam_id);
//        var_dump([
//            "artist" => $artist,
//            "artist_id" => $artist_id,
//            "similar" => $similar,
//        ]);
        die(json_encode($similar));
        break;
    
    default:
        http_response_code(405);
        die(json_encode(array("error"=>"This method is not allowed here")));
}