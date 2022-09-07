<?php

namespace App\Controllers;

use App\Models\VideoMetadataModel;
use Kernel\Controller;
use App\DB;
use Models\VideoMetadata;

class IndexController extends Controller\WebController
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = new DB\QueryDB();
    }

    public function index()
    {
        $this->dataView['items'] = $this->getTop10();
        $this->view('index_template');
    }

    private function getTop10() : array
    {
        $result = array();

        $search = new VideoMetadataModel();
        $search->access = 'public';
        foreach($this->dbh->findVideoMetadataArray($search) as $item){
            $result[] = $item;
        }
        return $result;
    }
}