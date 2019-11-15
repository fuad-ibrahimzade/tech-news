<?php

namespace App\Controllers;

use App\Models\User;
use Core\Router;
use \Core\View;

class Home extends \Core\Controller
{

    public function index($requestData=null)
    {
        return $this->view('Home/index.php', ['title'=>'Technews'] );
    }

    public function unknownPage($requestData=null)
    {
        return $this->index();
    }

    public function login($requestData=null)
    {
        if(isset($_POST['loginid'])){
            $email = htmlspecialchars($_POST['loginid']);
            $password = htmlspecialchars($_POST['loginpsw']);
            $userfound=User::getUserByEmail($email);
            if(isset($userfound)){
                $_SESSION['login']=$email;
                return true;
            }
            else{return false;}

        }
        if(!isset($_SESSION['login'])){return false;}else{return true;}
    }
}
