<?php

namespace App\Controllers;
use Kernel\MpdParser\MpdParser;
use Kernel\Response\Response;
use Kernel\Controller;
use Kernel\Files\File;
use Kernel\Auxiliary;
use App\Models\VideoMetadataModel;
use App\Models\FileUploadModel;
use App\Models\VideodataModel;
use App\DB;
use Kernel\Auth\Auth;
use Kernel\Files\TextToImage;

class UploadVideoController extends Controller\WebController
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = new DB\QueryDB();
    }

    public function index() : void
    {
        $this->view('upload_video_template');
    }

    public function upload() : void
    {
        $fileUploadModel = new FileUploadModel();
        $fileUploadModel->file_name = Auxiliary\getStringFromPost('file_name');
        $fileUploadModel->file_type = Auxiliary\getStringFromPost('file_type');
        $fileUploadModel->file_size = Auxiliary\getIntFromPost('file_size');
        $fileUploadModel->expect_data_from = Auxiliary\getIntFromPost('expect_data_from');
        $fileUploadModel->expect_data_to = Auxiliary\getIntFromPost('expect_data_to');
        $fileUploadModel->user_id = 1;//$this->user_id;

        // get file
        $file = new File('file_data', '../files/temp/');
        if($file->isLoaded() === false){
            Response::sendArray(array('result'=>false, 'message'=>'File is not loaded'));
        }
        
        $resFileModel = $this->dbh->findVideoUpload($fileUploadModel);

        if($resFileModel === NULL)
        {
            $file->createFolder();
            $this->dbh->addVideoUpload($fileUploadModel);
            $resFileModel = $this->dbh->findVideoUpload($fileUploadModel);
            Response::sendObject($resFileModel);
        }
        
        if($file->hasData() === false){
            Response::sendObject($resFileModel);
        }

        $this->dbh->updateVideoUpload($resFileModel);
        $resSave = $file->save($this->_preparePartName($resFileModel->file_name, $resFileModel->last_updated_part+1));
        if($resSave === false){
            //restore data in db
        }

        $resFileModel = $this->dbh->findVideoUpload($fileUploadModel);
        if($resFileModel->process_state == 1)
        {
            if( ! $file->merge_parts($resFileModel) ){
                Response::sendArray(array('result'=>false, 'message'=>'error. file has deleted.'));
            }

            $this->dbh->removeVideoUpload($resFileModel);
                
            $file->convertMp4ToDash($resFileModel->file_name);
            $convFilesList = $file->getAllFilesFromFolder(null);

            $file->delete($resFileModel->file_name);


            //delete temp file jesli conv to dash OK
            $miniature = new TextToImage();
            $miniaturePath = $miniature->createImagePNG($resFileModel->file_name, 'media/images/ss/');

            $videoMetadata = new VideoMetadataModel();
            $videoMetadata->user_id = 1;//$this->user_id;
            $videoMetadata->title = "";//$resFileModel->file_name;
            $videoMetadata->description = "";//$resFileModel->file_name;
            $videoMetadata->image = $miniaturePath;
            $videoMetadata->access = 'public';
            $this->dbh->addVideoMetadata($videoMetadata);

            
            // get current state metadata
            $videoMetadata = $this->dbh->findVideoMetadata($videoMetadata);


            // set mpd file
            $videoMpd = MpdParser::getMpd($file->getFolder() . $convFilesList['mpd_file']);
            $videoMpd->video_id = $videoMetadata->id;
            $this->dbh->addVideoMpd($videoMpd);
            $file->delete($convFilesList['mpd_file']);
            unset($convFilesList['mpd_file']);

            
            // set all video parts
            $videoData = new VideodataModel();
            $videoData->video_id = $videoMetadata->id;
            foreach($convFilesList as $idx => $convFile)
            {
                //max_allowed_packet = 8M in my.ini
                $videoData->part = $idx;
                $videoData->data = $file->getBlob($convFile);
                $this->dbh->addVideoData($videoData);
                $file->delete($convFile);
            }
        }
        Response::sendObject($resFileModel);
    }

    public function save() : void
    {
        $file = new File('', '');
        $fUpModel = new FileUploadModel();

        $file_type = Auxiliary\getStringFromPost('tytul');
        $file_size = Auxiliary\getStringFromPost('opis');
        $miniatura = new File('miniatura', '../files/temp/');
        $part_name = Auxiliary\getStringFromPost('part_vid_name');
        $part_size = Auxiliary\getStringFromPost('part_vid_size');
        $part_type = Auxiliary\getStringFromPost('part_vid_type');

        $fUpModel->file_name = Auxiliary\getStringFromPost('part_vid_name');
        $fUpModel->file_size = Auxiliary\getIntFromPost('part_vid_size');
        $fUpModel->file_type = Auxiliary\getStringFromPost('part_vid_type');
        $fUpModel->user_id = 1;//$this->user_id;

        $resFileModel = $this->dbh->findVideoUpload($fUpModel);
        if($resFileModel->process_state == 1)
        {
            $this->dbh->removeVideoUpload($resFileModel);
        }

        $file->save("doopa.png");
        header('Location:/upload-video', true, 303);
    }

    private function _preparePartName(string $fileName, string $lastUpdatedPart) : string{
        return $fileName.".part_".$lastUpdatedPart;
    }
}