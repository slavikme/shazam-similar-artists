<?php

class DB {
    /** @var DBO */
    private $dbh;
    public function __construct($host, $port, $name, $user, $pass = "") {
        try {
            $conn = new PDO("mysql:dbname=$name;host=$host;port=$port", $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: {$e->getMessage()})");
        }
        $this->dbh = $conn;
    }
    
    protected function createArtistTable() {
        $query = "
            CREATE TABLE `artists` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `shazam_id` BIGINT NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `shazam_id_UNIQUE` (`shazam_id` ASC))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;
        ";
        $count = $this->dbh->exec($query);
        if ( $count === FALSE ) {
            throw new Exception("Failed to create 'artists' table for unknown reason.");
        }
    }
    
    protected function createSimilarArtistsTable() {
        $query = "
            CREATE TABLE `similar` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `mbid` VARCHAR(40) NULL,
                `name` VARCHAR(255) NOT NULL,
                `lastfm_link` VARCHAR(255) NULL,
                `image_url` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `name_UNIQUE` (`name` ASC))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;
        ";
        $count = $this->dbh->exec($query);
        if ( $count === FALSE ) {
            throw new Exception("Failed to create 'similar' table for unknown reason.");
        }
    }
    
    protected function createArtistSimilarArtistsTable() {
        $query = "
            CREATE TABLE `artist_similar` (
                `artist_id` BIGINT NOT NULL,
                `similar_id` BIGINT NOT NULL,
            PRIMARY KEY (`artist_id`, `similar_id`),
            INDEX `id_idx` (`similar_id` ASC),
            CONSTRAINT `artist_id`
                FOREIGN KEY (`artist_id`)
                REFERENCES `artists` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `similar_id`
                FOREIGN KEY (`similar_id`)
                REFERENCES `similar` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;
        ";
        
        $count = $this->dbh->exec($query);
        if ( $count === FALSE ) {
            throw new Exception("Failed to create 'artist_similar' table for unknown reason.");
        }
    }
    
    public function createArtist($shazam_id, $artist_name) {
        $query = "
            INSERT INTO artists 
                (shazam_id, name) 
            VALUE (?, ?);
        ";
        $sth = $this->dbh->prepare($query);
        $sth->execute(array((int)$shazam_id, $artist_name));
        return $this->dbh->lastInsertId();
    }
    
    public function getArtistByShazamId($shazam_id) {
        $query = "
            SELECT *
            FROM artists
            WHERE shazam_id = ?;
        ";
        $sth = $this->dbh->prepare($query);
        $sth->execute(array((int)$shazam_id));
        return $sth->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getSimilarByShazamId($shazam_id) {
        $query = "
            SELECT s.* 
            FROM artists a 
            INNER JOIN artist_similar asm 
                ON a.id = asm.artist_id 
            INNER JOIN similar s 
                ON asm.similar_id = s.id
            WHERE a.shazam_id = ?;
        ";
        $sth = $this->dbh->prepare($query);
        $sth->execute(array((int)$shazam_id));
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSimilarByArtistId($artist_id) {
        $query = "
            SELECT s.* 
            FROM artist_similar asm
            INNER JOIN similar s 
                ON asm.similar_id = s.id
            WHERE asm.artist_id = ?;
        ";
        $sth = $this->dbh->prepare($query);
        $sth->execute(array((int)$artist_id));
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Fill an array of similar artists with existant IDs, for missing ones, 
     * will insert them.
     * @param type $similar_array
     */
    public function fillSimilarId(&$similar_array) {
        $quoted_names = array();
        foreach ( $similar_array as &$item ) {
            $item["id"] = null;
            $quoted_names[] = $this->dbh->quote($item["name"]);
        }
        $query = "
            SELECT *
            FROM similar
            WHERE name IN (" . implode(",", $quoted_names) . ");
        ";
        $sth = $this->dbh->prepare($query);
        $sth->execute();
        while ( $row = $sth->fetch(PDO::FETCH_ASSOC) ) {
            $similar_array[$row["name"]]["id"] = $row["id"];
        }
        $query = "
            INSERT 
            INTO similar
                (mbid, name, lastfm_link, image_url)
            VALUE
                (?, ?, ?, ?);
        ";
        $sth = $this->dbh->prepare($query);
        foreach ( $similar_array as &$item ) {
            if ( !empty($item["id"]) ) {
                continue;
            }
            $sth->execute(array($item["mbid"],$item["name"],$item["lastfm_link"],$item["image_url"]));
            $item["id"] = $this->dbh->lastInsertId();
        }
    }
    
    public function connectSimilarArtists($artist_id, $similar_array) {
        $insert_values = array();
        foreach ( $similar_array as $item ) {
            if ( empty($item["id"]) ) {
                continue;
            }
            $insert_values[] = "(" . implode(",",array($artist_id, $item["id"])) . ")";
        }
        $query = "
            INSERT IGNORE 
            INTO artist_similar
                (artist_id, similar_id)
            VALUES " . implode(",",$insert_values) . ";
        ";
        return $this->dbh->exec($query);
    }
    
    public function getArtist($id) {
        $query = "
            SELECT *
            FROM artists
            WHERE id = ?;
        ";
        $sth = $this->dbh->prepare($query);
        $sth->execute(array($id));
        return $sth->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getSimilar($id) {
        $query = "
            SELECT *
            FROM similar
            WHERE id = ?;
        ";
        $sth = $this->dbh->prepare($query);
        $sth->execute(array($id));
        return $sth->fetch(PDO::FETCH_ASSOC);
    }
}
