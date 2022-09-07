<?php

namespace App\Controllers;
use Kernel\Controller\WebController;
use Kernel\Auxiliary;
use Kernel\Validator\Validator;
use Kernel\Auth\Auth;
use Kernel\Logger\Logger;

class RegisterController extends WebController
{
    public function __construct(){
    }

    public function index()
    {
        $login = Auxiliary\getStringFromPost('login');
        $email = Auxiliary\getStringFromPost('email');
        $password = Auxiliary\getStringFromPost('password');
        $accept = Auxiliary\getStringFromPost('accept');

        if( Validator::checkEmail($email) === false ){
            return;
        }
        if( Validator::checkLogin($login) === false ){
            return;
        }
        if( Validator::checkPassword($password) === false ){
            return;
        }
        if( Validator::isBoolean($accept) === false ){
            return;
        }
        if( Auth::check_email_is_free_db($email) === false ){
            return;
        }
        if( Auth::check_login_is_free_db($login) === false ){
            return;
        }
        
        Logger::write('coś się stanęło...');

        Logger::open_buffer();
        Logger::write('wpis przy otwartym buforze 1');
        Logger::write('wpis przy otwartym buforze 2');
        Logger::write('wpis przy otwartym buforze 3');
        Logger::write('wpis przy otwartym buforze 4');
        Logger::write('wpis przy otwartym buforze 5');
        Logger::write('coś się stanęło...');
        Logger::flush();
        //Auth::create_user($email, $login, $password);
    }

    public function confirm()
    {

    }

}