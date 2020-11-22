<?php
// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

use Controllers as Controllers;

require_once('vendor/autoload.php');
require_once('helper_functions.php');

// Load congroller based on api key
if(isset(getallheaders()['api_key']) && getallheaders()['api_key']) {
    $api_key = getallheaders()['api_key'];
    switch($api_key) {
        case 'I6HXMFOgVE3mIDMW':
            require_once('config/chore_boar_config.php');
            require_once('Controllers/chore_boar_controller.php');
            $api = new Controllers\ChroeBoarController();
            break;
        default:
            http_response_code(404);
            echo "Not Found.";
            die();
    }
    
    // Set up Activerecord
    ActiveRecord\Config::initialize(function($cfg)
    {
        $cfg->set_model_directory('models');
        $db_user = Config::$database['db_name'];
        $db_pass = Config::$database['db_password'];
        $db_host = Config::$database['db_host'];
        $db_name = Config::$database['db_name'];
        $cfg->set_connections(
            array(
                // 'development' => 'mysql://username:password@localhost/development_database_name',
                // 'test' => 'mysql://username:password@localhost/test_database_name',
                'production' => "mysql://$db_user:$db_pass@$db_host/$db_name"
            )
        );
        $cfg->set_default_connection('production');
    });
} else {
    http_response_code(404);
    echo "Not Found.";
    die();
}

// Process the request and return the results
$api->processApi();

?>