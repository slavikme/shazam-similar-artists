<?php

session_start();
//session_destroy();
//var_dump($_SESSION); die();
if ( file_exists("config.php") && !isset($_SESSION["install_success"]) ) {
    header("Location: index.php");
    die("<script>window.location='index.php'</script>");
}

require_once 'db.php';

class Install extends DB {
    
    private $db_host, $db_port, $db_name, $db_user, $db_pass;
    
    public function __construct($db_host, $db_port, $db_name, $db_user, $db_pass) {
        if ( !$this->isValidHost($db_host) ) {
            throw new Exception("The host to connect to a database is invalid");
        }
        if ( !$this->isValidPort($db_port) ) {
            throw new Exception("The port to connect to a database is invalid");
        }
        $this->db_host = $db_host;
        $this->db_port = $db_port;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        parent::__construct($db_host, $db_port, $db_name, $db_user, $db_pass);
    }
    public function saveConfigFile($api_key) {
        $name = addslashes($this->db_name);
        $user = addslashes($this->db_user);
        $pass = addslashes($this->db_pass);
        $file_content = <<<EOL
<?php

if ( basename(\$_SERVER["SCRIPT_FILENAME"]) == "config.php" ) { header("Location: index.php"); die(); }
if ( !isset(\$cfg) || !is_array(\$cfg) ) { \$cfg = array(); }
if ( !isset(\$cfg["db"]) || !is_array(\$cfg["db"]) ) { \$cfg["db"] = array(); }

\$cfg["db"]["host"] = "{$this->db_host}";
\$cfg["db"]["port"] = {$this->db_port};
\$cfg["db"]["name"] = "$name";
\$cfg["db"]["user"] = "$user";
\$cfg["db"]["pass"] = "$pass";
        
\$cfg["api_key"] = "$api_key";
EOL;
        
        if ( is_writeable(__DIR__) ) {
            file_put_contents(__DIR__."/config.php", $file_content);
            return true;
        }
        return $file_content;
    }
    
    public function createTables() {
        try {
            $this->createArtistTable();
            $this->createSimilarArtistsTable();
            $this->createArtistSimilarArtistsTable();
        } catch (Exception $e) {
            //ignore
        }
    }
    
    private function isValidHost($host) {
        return preg_match("/[0-9a-z.\-]+/i", $host);
    }
    
    private function isValidPort($port) {
        return is_numeric($port) && $port > 0 && $port <= 65535;
    }
    
    static public function isConfigFileExists() {
        return file_exists(__DIR__."/config.php");
    }
}

$db_host = "127.0.0.1";
$db_port = "3306";
$db_name = "ssal_db";
$db_username = "ssal_user";
$db_password = "";

$admin = new stdClass();
$admin->full_name = "";
$admin->email = "";
$admin->username = "";
$admin->password = "";

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    $db_host = $_POST["db"]["host"];
    $db_port = $_POST["db"]["port"];
    $db_name = $_POST["db"]["name"];
    $db_username = $_POST["db"]["username"];
    $db_password = $_POST["db"]["password"];
    
//    $admin = json_decode(json_encode($_POST["admin"]), false);
    
    $errors = [];
    $config_create_failed = false;
    $config_content = "";
    $success = false;
    
    try {
        $ins = new Install($db_host, $db_port, $db_name, $db_username, $db_password);
        $api_key = sha1("".rand(0,1000000).time());
        $config_content = $ins->saveConfigFile($api_key);
        if ( $config_content !== true ) {
            $_SESSION["config_missing"] = $config_content;
            $config_create_failed = true;
        }
        $ins->createTables();
        $_SESSION["install_success"] = $api_key;
        $success = true;
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

if ( !empty($_SESSION["install_success"]) ) {
    $success = true;
    $api_key = $_SESSION["install_success"];
}

if ( Install::isConfigFileExists() ) {
    unset($_SESSION["config_missing"]);
    unset($_SESSION["install_success"]);
}

if ( !empty($_SESSION["config_missing"]) ) {
    $config_create_failed = true;
    $config_content = $_SESSION["config_missing"];
}

?>
<html>
    <head>
        <title>Shazam Similar Artists - Installation</title>
        <link href='http://fonts.googleapis.com/css?family=Josefin+Sans&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="res/css/semantic.min.css">
        <link rel="stylesheet" type="text/css" href="res/css/install.css">
        <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.js"></script>
        <script src="res/js/semantic.min.js"></script>
    </head>
    <body>
        <div class="main-wrapper">
            <h1 class="ui header">Shazam Similar Artists - Installation</h1>
            <div class="ui divider"></div>
                
        <?php if ( empty($success) ): ?>
            
            <p>Welcome to Shazam Similar Artists application.<br />You are seeing this page, because this is the first time you are running the application on this machine and it is not configured yet.</p>
            <p>Please, fill up the following form to complete the installation and configurations.</p>
            <div class="ui divider"></div>
            <form method="post">
                <h3 class="ui header">Database Configurations</h3>
                <p>Configure here your DB connection details.</p>
                <div class="ui <?=!empty($errors)?"error ":""?>form segment">
                    <?php if ( !empty($errors) ): ?>
                    <div class="ui error message">
                        <div class="header">Fix the following errors and try again:</div>
                        <ul class="list">
                            <?php foreach ( $errors as $message ): ?>
                            <li><?=$message?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <div class="field disabled">
                        <label>Engine</label>
                        <div class="ui fluid selection dropdown disabled">
                            <div class="default text">MySQL</div>
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                <div class="item">MySQL</div>
                            </div>
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Host</label>
                            <input type="text" name="db[host]" value="<?=$db_host?>" />
                        </div>
                        <div class="field">
                            <label>Port</label>
                            <input type="number" name="db[port]" value="<?=$db_port?>" />
                        </div>
                    </div>
                    <div class="field">
                        <label>Database Name</label>
                        <input type="text" name="db[name]" value="<?=$db_name?>" />
                    </div>
                    <div class="field">
                        <label>Username</label>
                        <input type="text" name="db[username]" value="<?=$db_username?>" />
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" name="db[password]" placeholder="[ No Password ]" value="<?=$db_password?>" />
                    </div>
                </div>
                <?php /*
                <h3 class="ui header">Administrator</h3>
                <p>This section will create and administration user, to be able to control the data stored.</p>
                <div class="ui form segment">
                    <div class="field">
                        <label>Full Name</label>
                        <input type="text" name="admin[full_name]" value="<?=$admin->full_name?>" />
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="text" name="admin[email]" value="<?=$admin->email?>" />
                    </div>
                    <div class="field">
                        <label>Username</label>
                        <input type="text" name="admin[username]" value="<?=$admin->username?>" />
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Password</label>
                            <input type="password" name="password" value="<?=$admin->password?>" />
                        </div>
                        <div class="field">
                            <label>Repeat Password</label>
                            <input type="password" name="admin[password]" value="<?=$admin->password?>" />
                        </div>
                    </div>
                </div>
                 */ ?>
                <div class="ui grid">
                    <div class="row">
                        <div class="right aligned column">
                            <input class="ui blue submit button" type="submit" value="Next" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <script type="text/javascript">
            $('.ui.dropdown').dropdown();
            $('.field.disabled .ui.dropdown').dropdown('destroy');
        </script>
        <script type="text/javascript" src="res/js/install-form-validation.js"></script>
        
    <?php else: ?>
        
        <?php if ( !empty($config_create_failed) ): ?>
        
        <div class="ui warning message">
            <div class="header">
              You are almost done...
            </div>
            <p>Unfortunately, the system cannot save the config file for you, because 
                it doesn't have write permissions.</p>
            <p>
                But, it's very easy to fix. You just have to create a file called 
                '<b><i>config.php</i></b>' in the root of this project (must be 
                parallel to '<b><i>install.php</i></b>' file) with the following 
                content:
            </p>
            <pre class="ui instructive segment"><?=htmlentities($config_content)?></pre>
            <p>Once you've done, click "Next" to continue.</p>
        </div>
        
        <div class="ui grid">
            <div class="row">
                <div class="right aligned column">
                    <a class="ui blue submit button" href="javascript:location.reload()" />Next</a>
                </div>
            </div>
        </div>
        
        <?php else: ?>
        
        <div class="ui success message">
            <div class="header">
              Congratulations!
            </div>
            <p>The project has been successfully installed and configured.</p>
            <p>You can now start using the extension and the api.</p>
            <p>Your API key is:</p>
            <pre class="ui instructive segment"><?=$api_key?></pre>
            <p>You can also find it in the '<b><i>config.php</i></b>' file.</p>
        </div>
        
        <?php endif; ?>
        
    <?php endif;?>
    </body>
</html>
