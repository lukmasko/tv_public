<?php

namespace Kernel\Validator;

class Validator
{
    private function __construct(){
    }

    public static function checkEmail($email) : bool{
        return (filter_var($email, FILTER_VALIDATE_EMAIL) === false) ? false : true;
    }

    public static function checkLogin($login) : bool{
        return (preg_match("/^[a-zA-Z]{1}[a-zA-Z0-9]{5,31}$/", $login) === 1) ? true : false;
    }

    public static function checkPassword($password) : bool{
        // at least 8 chars, max 32 chars
        // at lease 1 upper char
        // at lease 1 lower char
        // at least 1 digit
        return (preg_match("/(?=^(?:[^A-Z]*[A-Z]){1})(?=^(?:[^a-z]*[a-z]){1})(?=^(?:\D*\d){1})(?=^(?:\w*\W){1})^[A-Za-z\d\W]{8,32}$/", $password) === 1) ? true : false;
    }

    public static function isBoolean($value) : bool{
        return (filter_var($value, FILTER_VALIDATE_BOOLEAN) === false) ? false : true;
    }
}