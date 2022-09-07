<?php

namespace App\Controllers;
use Kernel\Controller\WebController;
use Kernel\Auxiliary;
use Kernel\Validator\Validator;
use Kernel\Auth\Auth;
use Kernel\Session\Cookie;

class LoginController extends WebController
{
    public function __construct(){
    }

    public function index()
    {
        $login = Auxiliary\getStringFromPost('login');
        $password = Auxiliary\getStringFromPost('password');

        $loginByEmail = false;
        if( Validator::checkEmail($login) ){
            $loginByEmail = true;
        }
        if( !$loginByEmail && Validator::checkLogin($login))
            Validator::checkLogin($login);
        Validator::checkPassword($password);

        $uid = Auth::check($login, $password);
        if($uid === null){
            return;
        }

        $session_hash = Cookie::generateHash($uid);
        Cookie::setHash($session_hash);
        Auth::set_session_db(1, $session_hash, $session_hash);
    }

}