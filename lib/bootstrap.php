<?php
/**
 * A bootstrap for the Dropbox SDK usage examples.
 * Changed to use 
 * @link https://github.com/BenTheDesigner/Dropbox/tree/master/examples
 */

if (PHP_SAPI === 'cli') {
    exit('bootstrap.php must not be run via the command line interface');
}
if(basename($_SERVER['REQUEST_URI']) == 'bootstrap.php'){
    exit('bootstrap.php does nothing on its own. Please see the examples provided');
}

// Set error reporting
error_reporting(1);
ini_set('display_errors', 'On');
ini_set('html_errors', 'On');
// Register a simple autoload function
spl_autoload_register(function($class){
    $class = str_replace('\\', '/', $class);
    @include ('lib/dropbox/'.$class.'.php');
    @include ('../' . $class . '.php');
    //require_once('../' . $class . '.php');
});

// Set your consumer key, secret and callback URL
$key    = DROPBOX_API_KEY;
$secret = DROPBOX_API_SECRET;

// Check whether to use HTTPS and set the callback URL
$protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
$callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Instantiate the Encrypter and storage objects
$encrypter = new \Dropbox\OAuth\Storage\Encrypter(ENCRYPTER_KEY);

// User ID assigned by your auth system (used by persistent storage handlers)
$userID = 1;
// Instantiate the database data store and connect
$storage = new \Dropbox\OAuth\Storage\PDO($encrypter, $userID);
$storage->connect(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
// Create the consumer and API objects
$OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
$dropbox = new \Dropbox\API($OAuth, "dropbox");
