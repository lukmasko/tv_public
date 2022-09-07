<?php

namespace App\Controllers;
use Kernel\Controller;

class Error404Controller extends Controller\WebController
{
    public function index()
    {
        $this->dataView['cmd'] = 'Sorry 404 error...';
        $this->view('error_404_template');
    }
}