<?php

namespace App\DB;
use Kernel\DB;

use App\Models\FileUploadModel;
use App\Models\VideoMetadataModel;
use App\Models\VideoDataModel;
use App\Models\VideoMpdModel;
use Kernel\SQLBuilder;

class QueryDB
{
    private const MAX_PART_SIZE = 333000;
    private $dbh;

    public function __construct(){
        $this->dbh = DB\DbMySql::getInstance();
    }

    public function findVideoUpload(FileUploadModel $inputModel) : ?FileUploadModel
    {
        $resFileModel = new FileUploadModel();

        $stmt = $this->dbh->prepare('SELECT * FROM video_upload WHERE user_id=:user_id AND file_size=:file_size AND file_name=:file_name AND file_type=:file_type;');
        $stmt->bindParam(':user_id', $inputModel->user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':file_size', $inputModel->file_size, \PDO::PARAM_INT);
        $stmt->bindParam(':file_name', $inputModel->file_name, \PDO::PARAM_STR);
        $stmt->bindParam(':file_type', $inputModel->file_type, \PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch();

        if($res === false)
            return NULL;

        $resFileModel->id = intval($res['id']);
        $resFileModel->user_id = intval($res['user_id']);
        $resFileModel->file_name = $res['file_name'];
        $resFileModel->file_type = $res['file_type'];
        $resFileModel->file_size = intval($res['file_size']);
        $resFileModel->part_size = intval($res['part_size']);
        $resFileModel->expect_data_from = intval($res['expect_data_from']);
        $resFileModel->expect_data_to = intval($res['expect_data_to']);
        $resFileModel->parts_count = intval($res['parts_count']);
        $resFileModel->last_updated_part = intval($res['last_updated_part']);
        $resFileModel->date_update = intval($res['date_update']);
        $resFileModel->percent = intval($res['percent']);
        $resFileModel->process_state = intval($res['process_state']);

        return $resFileModel;
    }

    public function addVideoUpload(FileUploadModel $inputModel) : bool
    {
        $maxPartSize = self::MAX_PART_SIZE;
        $partsCount = $this->_calculatePartsCount($inputModel->file_size, $maxPartSize);
        $expect_data_from = $this->_calculateNextDataFrom($inputModel->file_size, $maxPartSize, 0);
        $expect_data_to = $this->_calculateNextDataTo($inputModel->file_size, $maxPartSize, 0);
        $last_updated_part = 0;
        $dateUpdate = time();
        $percent = 0;
        $process_state = 0;

        $user_id = 1;
        
        $statement = $this->dbh->prepare('INSERT INTO `video_upload`(`id`, `user_id`, `file_name`, `file_type`, `file_size`, `part_size`, `parts_count`, `last_updated_part`, `expect_data_from`, `expect_data_to`, `date_update`, `percent`, `process_state`) VALUES (NULL, :user_id, :file_name, :file_type, :file_size, :part_size, :parts_count, :last_updated_part, :expect_data_from, :expect_data_to, :date_update, :percent, :process_state);');
        $statement->bindParam(':user_id', $inputModel->user_id, \PDO::PARAM_INT);
        $statement->bindParam(':file_name', $inputModel->file_name, \PDO::PARAM_STR);
        $statement->bindParam(':file_type', $inputModel->file_type, \PDO::PARAM_STR);
        $statement->bindParam(':file_size', $inputModel->file_size, \PDO::PARAM_INT);
        $statement->bindParam(':part_size', $maxPartSize, \PDO::PARAM_INT);
        $statement->bindParam(':expect_data_from', $expect_data_from, \PDO::PARAM_INT);
        $statement->bindParam(':expect_data_to', $expect_data_to, \PDO::PARAM_INT);
        $statement->bindParam(':parts_count', $partsCount, \PDO::PARAM_INT);
        $statement->bindParam(':last_updated_part', $last_updated_part, \PDO::PARAM_INT);
        $statement->bindParam(':date_update', $dateUpdate, \PDO::PARAM_INT);
        $statement->bindParam(':percent', $percent, \PDO::PARAM_INT);
        $statement->bindParam(':process_state', $process_state, \PDO::PARAM_INT);
        $statement->execute();

        return true;
    }

    public function findVideoMetadataArray(VideoMetadataModel $inputModel)
    {
        $statement = $this->dbh->prepare('SELECT * FROM video_metadata WHERE `access`=:access ORDER BY id;');
        $statement->bindParam(':access', $inputModel->access, \PDO::PARAM_STR);
        $statement->execute();

        while(($row = $statement->fetch()))
        {
            $meta = new VideoMetadataModel();
            $meta->id = $row['id'];
            $meta->title = $row['title'];
            $meta->description = $row['description'];
            $meta->image = $row['image'];
            yield $meta;
        }
    }

    public function updateVideoMetadata(VideoMetadataModel $inputModel) : bool
    {
        return false;
    }

    public function updateVideoUpload(FileUploadModel $inputModel) : bool
    {
        $new_last_updated_part = intval($inputModel->last_updated_part) + 1;
        $dateUpdate = time();
        $process_state = 0;
        $percent = floor(($new_last_updated_part * 100) / $inputModel->parts_count);
        if( $new_last_updated_part == intval($inputModel->parts_count) ){
            $percent = 100;
            $process_state = 1;
        }

        $expect_data_from = $this->_calculateNextDataFrom($inputModel->file_size, $inputModel->part_size, $new_last_updated_part);
        $expect_data_to = $this->_calculateNextDataTo($inputModel->file_size, $inputModel->part_size, $new_last_updated_part);

        $statement = $this->dbh->prepare('UPDATE `video_upload` SET `expect_data_from`=:expect_data_from, `expect_data_to`=:expect_data_to, `last_updated_part`=:last_updated_part, `percent`=:percent, `process_state`=:process_state WHERE user_id=:user_id AND file_size=:file_size AND file_name=:file_name AND file_type=:file_type;');
        $statement->bindParam(':expect_data_from', $expect_data_from, \PDO::PARAM_INT);
        $statement->bindParam(':expect_data_to', $expect_data_to, \PDO::PARAM_INT);
        $statement->bindParam(':last_updated_part', $new_last_updated_part, \PDO::PARAM_INT);
        $statement->bindParam(':percent', $percent, \PDO::PARAM_INT);
        $statement->bindParam(':process_state', $process_state, \PDO::PARAM_INT);
        $statement->bindParam(':user_id', $inputModel->user_id, \PDO::PARAM_INT);
        $statement->bindParam(':file_name', $inputModel->file_name, \PDO::PARAM_STR);
        $statement->bindParam(':file_type', $inputModel->file_type, \PDO::PARAM_STR);
        $statement->bindParam(':file_size', $inputModel->file_size, \PDO::PARAM_INT);
        $statement->execute();

        return true;
    }

    public function removeVideoUpload(FileUploadModel $inputModel) : bool
    {
        $stmt = $this->dbh->prepare('DELETE FROM video_upload WHERE user_id=:user_id AND file_size=:file_size AND file_name=:file_name AND file_type=:file_type;');
        $stmt->bindParam(':user_id', $inputModel->user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':file_size', $inputModel->file_size, \PDO::PARAM_INT);
        $stmt->bindParam(':file_name', $inputModel->file_name, \PDO::PARAM_STR);
        $stmt->bindParam(':file_type', $inputModel->file_type, \PDO::PARAM_STR);
        $stmt->execute();

        return true;
    }

    /**/
    private function _calculatePartsCount(int $fileSize, int $maxPartSize) : int{
        return (int)($fileSize / $maxPartSize) + (($fileSize % $maxPartSize) == 0 ? 0 : 1);
    }

    private function _calculateNextDataFrom(int $fileSize, int $maxPartSize, int $lastUpdatedPart) : int{
        $shift = $lastUpdatedPart * $maxPartSize;
        return ($shift < $fileSize) ? $shift : $fileSize;
    }

    private function _calculateNextDataTo(int $fileSize, int $maxPartSize, int $lastUpdatedPart) : int{
        $shift = $lastUpdatedPart * $maxPartSize + $maxPartSize;
        return ($shift < $fileSize) ? $maxPartSize : ($fileSize + $maxPartSize) - $shift;
    }

    /**/
    public function addVideoMetadata(VideoMetadataModel $inputModel) : bool
    {       
        $statement = $this->dbh->prepare('INSERT INTO `video_metadata`(`id`, `user_id`, `title`, `description`, `image`, `access`) VALUES (NULL, :user_id, :title, :description, :image, :access);');
        $statement->bindParam(':user_id', $inputModel->user_id, \PDO::PARAM_INT);
        $statement->bindParam(':title', $inputModel->title, \PDO::PARAM_STR);
        $statement->bindParam(':description', $inputModel->description, \PDO::PARAM_STR);
        $statement->bindParam(':image', $inputModel->image, \PDO::PARAM_STR);
        $statement->bindParam(':access', $inputModel->access, \PDO::PARAM_STR);
        $statement->execute();

        return true;
    }

    public function findVideoData(VideoDataModel $model) : ?VideoDataModel
    {
        $resModel = new VideoDataModel();

        $sqlQuery = SQLBuilder\getQuery_Where($model, 'video_data');
        $stmt = $this->dbh->prepare($sqlQuery);

        if(!is_null($model->video_id))
            $stmt->bindParam(':video_id', $model->video_id, \PDO::PARAM_INT);
        if(!is_null($model->part))
            $stmt->bindParam(':part', $model->part, \PDO::PARAM_INT);

        $stmt->execute();
        $res = $stmt->fetch();

        if($res === false)
            return NULL;

        $resModel->video_id = intval($res['video_id']);
        $resModel->part = $res['part'];
        $resModel->data = $res['data'];
        
        return $resModel;
    }

    public function findVideoMpd(VideoMpdModel $model) : ?VideoMpdModel
    {
        $resModel = new VideoMpdModel();

        $sqlQuery = SQLBuilder\getQuery_Where($model, 'video_mpd');
        $stmt = $this->dbh->prepare($sqlQuery);

        if(!is_null($model->video_id))
            $stmt->bindParam(':video_id', $model->video_id, \PDO::PARAM_INT);
        if(!is_null($model->width))
            $stmt->bindParam(':width', $model->width, \PDO::PARAM_INT);
        if(!is_null($model->height))
            $stmt->bindParam(':height', $model->height, \PDO::PARAM_INT);

        $stmt->execute();
        $res = $stmt->fetch();

        if($res === false)
            return NULL;

        $resModel->id = intval($res['id']);
        $resModel->video_id = intval($res['video_id']);
        $resModel->codecs = $res['codecs'];
        $resModel->width = intval($res['width']);
        $resModel->height = intval($res['height']);
        $resModel->duration = $res['duration'];
        $resModel->mime_type = $res['mime_type'];

        return $resModel;
    }

    public function findVideoMetadata(VideoMetadataModel $model) : ?VideoMetadataModel
    {
        $resModel = new VideoMetadataModel();

        $sqlQuery = SQLBuilder\getQuery_Where($model, 'video_metadata');
        $stmt = $this->dbh->prepare($sqlQuery);

        if(!is_null($model->id))
            $stmt->bindParam(':id', $model->id, \PDO::PARAM_INT);
        if(!is_null($model->user_id))
            $stmt->bindParam(':user_id', $model->user_id, \PDO::PARAM_INT);
        if(!is_null($model->title))
            $stmt->bindParam(':title', $model->title, \PDO::PARAM_STR);
        if(!is_null($model->description))
            $stmt->bindParam(':description', $model->description, \PDO::PARAM_STR);
        if(!is_null($model->image))
            $stmt->bindParam(':image', $model->image, \PDO::PARAM_STR);
        if(!is_null($model->access))
            $stmt->bindParam(':access', $model->access, \PDO::PARAM_STR);
            
        $stmt->execute();
        $res = $stmt->fetch();

        if($res === false)
            return NULL;

        $resModel->id = intval($res['id']);
        $resModel->user_id = intval($res['user_id']);
        $resModel->title = $res['title'];
        $resModel->description = $res['description'];
        $resModel->image = $res['image'];
        $resModel->access = $res['access'];

        return $resModel;
    }

    public function addVideoData(VideodataModel $inputModel) : bool
    {
        $statement = $this->dbh->prepare('INSERT INTO `video_data` (`video_id`, `part`, `data`) VALUES (:video_id, :part, :data);');
        $statement->bindParam(':video_id', $inputModel->video_id, \PDO::PARAM_INT);
        $statement->bindParam(':part', $inputModel->part, \PDO::PARAM_INT);
        $statement->bindParam(':data', $inputModel->data, \PDO::PARAM_LOB);
        $statement->execute();
        
        return true;
    }

    public function addVideoMpd(VideoMpdModel $inputModel) : bool
    {
        $statement = $this->dbh->prepare('INSERT INTO `video_mpd` (`id`, `video_id`, `codecs`, `width`, `height`, `duration`, `mime_type`) VALUES (NULL, :video_id, :codecs, :width, :height, :duration, :mime_type);');
        $statement->bindParam(':video_id', $inputModel->video_id, \PDO::PARAM_INT);
        $statement->bindParam(':codecs', $inputModel->codecs, \PDO::PARAM_STR);
        $statement->bindParam(':width', $inputModel->width, \PDO::PARAM_INT);
        $statement->bindParam(':height', $inputModel->height, \PDO::PARAM_INT);
        $statement->bindParam(':duration', $inputModel->duration, \PDO::PARAM_STR);
        $statement->bindParam(':mime_type', $inputModel->mime_type, \PDO::PARAM_STR);
        $statement->execute();
        return true;
    }
}