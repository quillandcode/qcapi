<?php
namespace Controllers;
require_once "rest_controller.php";
require_once "models\user.php";

class ChroeBoarController extends \Controllers\RestController {
    
    public function __construct() {
        // Init parent contructor
        parent::__construct();
    }

    public function test() {
        $user = \User::find_by_id(1);
        $this->response($user->to_json(), 200);
    }

    public function sign_up() {
        $new_user = \User::create(array(
            'first_name'    => $_request['first_name'],
            'last_name'     => $_request['last_name'],
            'email'         => $_request['email'],
            'password'      => better_crypt($_request['password']),
            'hash'          => better_crypt($hash = md5(rand(0,1000)))
        ));

        // Query the db to get the user object minus the password and hash fields
        $user = \User::find_by_id(
            $new_user->id,
            array('select' => 'first_name, last_name, email, enabled')
        );

        // Return the user object
        $this->response($user->to_json);
    }
}
?>