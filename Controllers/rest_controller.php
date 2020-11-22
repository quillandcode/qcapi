<?php
namespace Controllers;

class RestController {
    public $_allow = array();
    public $_content_type = "application/json";
    public $_request = array();

    private $_method = "";
    private $_code = 200;

    public function __construct() {
        // Get inputs from the request
        $this->inputs();
    }
    
    //Public method for accessing the api.
    //This method dynmically calls the method based on the query string
    public function processApi() {
    		$func = strtolower(trim(str_replace("/","",$this->_request['action'])));
        if((int)method_exists($this,$func) > 0) {
            $this->$func();
        } else {
            // If the method not exist with in this class, response would be "Page not found".
            $this->response('',404);
        }
    }
	
	public function getReferer() {
        return $_SERVER['HTTP_REFERER'];
    }

    public function response($data, $status) {
        $this->_code = ($status) ? $status : 200;
        $this->setHeaders();
        echo $data;
        exit ;
    }

    public function jsonResponse($data, $status) {
        $this->response($this->json($data), $status);
    }

    private function getStatusMessage() {
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        return ($status[$this->_code]) ? $status[$this->_code] : $status[500];
    }

    public function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    function my_json_decode($s) {
        // $s = str_replace(
        //     array('"',  "'"),
        //     array('\"', '"'),
        //     $s
        // );
        $s = preg_replace('/(\w+):/i', '"\1":', $s);
        // var_dump(sprintf($s));
        return json_decode(sprintf($s));
    }

    private function inputs() {
        switch($this->getRequestMethod()) {
            case "GET":
                $this->_request = $_GET;
            break;
            case "POST":
            case "DELETE":
            case "PUT":
                $putdata = file_get_contents("php://input");
                // var_dump($_REQUEST);
                // $putdata = str_replace("=", ":", $putdata);
                // $putdata = str_replace("&", ",", $putdata);
                // $putdata = "{".urldecode($putdata)."}";
                // $this->my_json_decode($putdata);
                // $this->_request = (array)json_decode($putdata, true);
                $this->_request = $_POST;
            break;
            default:
                $this->response('', 406);
            break;
        }
    }

    private function setHeaders() {
        header("HTTP/1.1 " . $this->_code . " " . $this->getStatusMessage());
        header("Content-Type:" . $this->_content_type);
    }

	//Encode array into JSON
    protected function json($data)
    {
        if(is_array($data) || is_object($data)) {
            return json_encode($data);
        }
    }
}
?>