<?php

namespace Core;

use App\Config;
use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;

abstract class Controller
{

    protected $route_params = [];

    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    public function view($view, $args = [])
    {
        extract($args, EXTR_SKIP);
        $file = dirname(__DIR__) . "/App/Views/$view";
        if (is_readable($file)) {
            require $file;
        } else {
            echo "$file not found";
        }
    }
    public function json($data){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
//        yuxaridakilar muveqqeti comment
//        if($_SERVER['REQUEST_METHOD'] != strtoupper($allowed_access_method)){
//            echo json_encode(array("message" => "Wrong Request Method"));
//            return;
//        }

//        header("Access-Control-Allow-Methods: ".strtoupper($allowed_access_method)."");
//        header("Access-Control-Allow-Methods: POST");

        header("Access-Control-Max-Age: 3600");
        //        yuxaridaki 1dene muveqqeti comment
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//        getde bidene origin variydi yuxaridakilardan
        header("Access-Control-Allow-Headers: access");
        //        yuxaridaki 1dene muveqqeti comment
//        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Allow-Credentials: true");
        //        yuxaridaki 1dene muveqqeti comment

//        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");

        echo json_encode($data);
    }

    public function getEncodedJWT($data,$key){
        $token = array(
            "iss" => $_SERVER['SERVER_NAME'],
            "aud" => Router::getInstance()->currentURL(),
            "iat" => Carbon::now(),
            "nbf" => Carbon::now(),
            "exp" => Carbon::now()->addDays(1),
            "data" => $data
        );
        $token = array(
            "exp" => Carbon::now()->addDays(1),
            "data" => $data
        );

        $jwt = JWT::encode($token, $key);
        return $jwt;
    }

    public function get_headers($function_name='getallheaders'){

        $all_headers=array();

        if(function_exists($function_name)){ 

            $all_headers=$function_name();
        }
        else{

            foreach($_SERVER as $name => $value){

                if(substr($name,0,5)=='HTTP_'){

                    $name=substr($name,5);
                    $name=str_replace('_',' ',$name);
                    $name=strtolower($name);
                    $name=ucwords($name);
                    $name=str_replace(' ', '-', $name);

                    $all_headers[$name] = $value; 
                }
                elseif($function_name=='apache_request_headers'){

                    $all_headers[$name] = $value; 
                }
            }
        }


        return $all_headers;
    }

    public function isAuthorized($requestData=null,$email=Config::ADMIN_EMAIL){
        $clientJWTHeader=array_key_exists('Authorization',$this->get_headers())===true ? 
        (strpos($this->get_headers()['Authorization'],'Bearer ')!==false ? 
        trim(substr($this->get_headers()['Authorization'],7)) : $this->get_headers()['Authorization'] ) :false;

        $user=User::getUserByEmail($email);
        // isset($requestData->jwt) && 
        if(isset($user) && !empty($user) && $clientJWTHeader!==false){
            if($clientJWTHeader===$user->api_token){
                return true;
            }
//            list($jwt) = sscanf( getallheaders()['Authorization']->toString(), 'Authorization: Bearer %s');
//            && isset($jwt)
            // $token=null;
            // try{
            //     $token = JWT::decode($requestData['jwt'], $user->api_token);
            // }
            // catch (\Exception $e){
            // }

            // if (isset($token['data']['csrf_token']) && hash_equals($_SESSION['csrf_token'],$token['data']['csrf_token'])) {
            //     if ($_SESSION['csrf_token_expire']!==0 && time() >= $_SESSION['csrf_token_expire']) {
            //         // reload
            //         return false;
            //     } else {
            //         // do
            //     }
            // }
            // if(isset($token)){
            //     if(isset($token['data']) && isset($token['data']['email']) && $token['data']['email']===$user->email){
            //         return true;
            //     }
            // }
        }
        return false;
    }

    public function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    public function back() {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
