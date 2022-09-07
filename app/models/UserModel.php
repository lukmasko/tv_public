<?php

namespace App\Models;

use Kernel\Model\IModel;

class UserModel implements IModel
{
    public $id;
    public $email;
    public $login;
    public $password;
    public $roles;
    public $firstname;
    public $secondname;
    public $postcode;
    public $signup_date;
    public $status;
}