<?php

namespace App\Controllers;
use Kernel\Controller;

class NoPermissionController extends Controller\WebController
{
    public function index()
    {
        $this->dataView['cmd'] = 'Sorry you dont have permission...';
        $this->view('no_permission_template');
    }
}