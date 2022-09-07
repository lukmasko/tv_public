<?php

namespace App\Controllers;
use Kernel\Controller;
use App\DB;
use App\Models\VideoMpdModel;
use Kernel\Auxiliary;
use Kernel\Response\Response;
use App\Models\VideoDataModel;

class GetVideoPartController extends Controller\ApiController
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = new DB\QueryDB();
    }

    public function index()
    {
        $v = Auxiliary\getIntFromGet('v');
        $p = Auxiliary\getIntFromGet('p');

        if(is_null($v) || !is_numeric($v))
            Response::sendArray(array('result'=>false));

        if(is_null($p))
            $this->send_mpd($v);

        if(is_numeric($p))
            $this->send_part($v, $p);

        Response::sendArray(array('result'=>false));
    }

    private function send_part($vid, $part) : void
    {
        $m = new VideoDataModel();
        $m->video_id = $vid;
        $m->part = $part;
        $meta = $this->dbh->findVideoData($m);
        if($meta == null)
            Response::sendBuffer("");
        Response::sendBuffer($meta->data);
    }

    private function send_mpd($vid) : void
    {
        $m = new VideoMpdModel();
        $m->video_id = $vid;
        $meta = $this->dbh->findVideoMpd($m);
        Response::sendObject($meta);
    }
}