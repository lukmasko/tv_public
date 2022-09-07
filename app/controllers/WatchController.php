<?php

namespace App\Controllers;
use Kernel\Controller;

class WatchController extends Controller\WebController
{
    public function index()
    {
        $this->dataView['cmd'] = 'watch view...';
        $this->view('watch_video_template');
    }
}
