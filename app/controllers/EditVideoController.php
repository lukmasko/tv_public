<?php

namespace App\Controllers;
use Kernel\Controller;
use Kernel\Files\File;
use Kernel\Auxiliary;
use App\Models\FileUploadModel;
use App\Models\VideoMetadataModel;
use App\Models\VideodataModel;
use Kernel\Response\Response;
use App\DB;

class EditVideoController extends Controller\WebController
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = new DB\QueryDB();
    }

    public function index() : void
    {
        $this->dataView['items'] = $this->getTop10();
        $this->view('show_edit_video_list_template');
    }

    public function edit($video_id) : void
    {
        $vmd = new VideoMetadataModel();
        $vmd->user_id = 1;//$this->user_id;
        $vmd->id = $video_id;

        $vmd = $this->dbh->findVideoMetadata($vmd);
        $this->dataView['vidid'] = $video_id;
        $this->dataView['title'] = $vmd->title;
        $this->dataView['description'] = $vmd->description;
        $this->dataView['full_path_image'] = sprintf("/media/images/ss/%s", $vmd->image);
        $this->view('edit_video_template');
    }

    public function save() : void
    {
        $vmd = new VideoMetadataModel();
        $vmd->user_id = 1;//$this->user_id;

        $videoID = Auxiliary\getStringFromPost('vidid');
        $file_type = Auxiliary\getStringFromPost('tytul');
        $file_size = Auxiliary\getStringFromPost('opis');
        $miniatura = new File('miniatura', 'media/images/ss/');
        

        //$resFileModel = $this->dbh->findVideoUpload($fUpModel);

        //$this->dbh->updateVideoUpload();

        //$miniatura->save("raw.png");
        //header('Location:/edit-video', true, 303);
    }

    private function getTop10() : array 
    {
        $result = array();

        $search = new VideoMetadataModel();
        $search->access = 'private';

        foreach($this->dbh->findVideoMetadataArray($search) as $item){
            $result[] = $item;
        }
        return $result;
    }

    private function _preparePartName(string $fileName, string $lastUpdatedPart) : string{
        return $fileName.".part_".$lastUpdatedPart;
    }
}