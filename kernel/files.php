<?php

namespace Kernel\Files;
use Kernel\Auxiliary;
use App\Models\FileUploadModel;

class File
{
    private $file;
    private $folder;

    public function __construct($key, $pathToFolder)
    {
        $this->file = Auxiliary\getFile($key);
        $this->folder = $pathToFolder;
    }

    public function isLoaded() : bool {
        if($this->file === null)
            return false;
        return true;
    }

    public function hasData() : bool {
        if($this->file === null || $this->file['size'] <= 0)
            return false;
        return true;
    }

    public function save($newName) : bool
    {
        $fullpath = $this->folder . $newName;

        if( is_uploaded_file($this->file['tmp_name']) !== true )
            return false;

        if( move_uploaded_file($this->file['tmp_name'], $fullpath) !== true )
            return false;

        if( file_exists($fullpath) !== true )
            return false;

        if( filesize($fullpath) <= 0 )
            return false;

        return true;
    }

    public function merge_parts(FileUploadModel $fileModel) : bool
    {
        $fd = fopen($this->folder . $fileModel->file_name, "w");
        for($i=1; $i<=$fileModel->parts_count; $i++)
        {
            $part = $this->folder . $fileModel->file_name . ".part_" . $i;
            $data = file_get_contents($part);
            fwrite($fd, $data);
        }
        fclose($fd);

        if(filesize($this->folder . $fileModel->file_name) != $fileModel->file_size)
            return false;

        if(file_exists($this->folder . $fileModel->file_name) !== true)
            return false;

        //delete all parts
        for($it=1; $it<=$fileModel->parts_count; $it++)
        {
            if($this->delete($fileModel->file_name. ".part_" . $it) === false)
                return false;
        }

        return true;
    }

    public function createFolder() : bool
    {
        if(is_dir($this->folder) === true)
            return true;            
        return mkdir($this->folder);
    }

    public function delete($fname) : bool
    {
        $path = $this->folder . $fname;
        return unlink($path);
    }

    public function getBlob($fname) : ?string
    {
        $path = $this->folder . $fname;
        $blob = file_get_contents($path);
        return ($blob) ? $blob : null;
    }

    public function getAllFilesFromFolder() : array
    {
        $res = array();

        $files = scandir($this->folder, SCANDIR_SORT_ASCENDING);
        foreach($files as $file)
        {
            if(strcmp($file, ".") == 0 || strcmp($file, "..") == 0){
                continue;
            }
            
            if(strcmp($file, "mpdesc.mpd") == 0){
                $index = 'mpd_file';
            }
            else if(strncmp($file, "seg_init", 8) == 0){
                $index = 0;
            }
            else{
                $output = preg_split('/(_|\.)/', $file);
                $index = intval($output[1]);
            }

            $res[$index] = $file;
        }

        ksort($res);
        return $res;
    }

    public function convertMp4ToDash($videoName) : bool
    {
        //https://bitmovin.com/mp4box-dash-content-generation-x264/
        $output = null;
        $retval = null;
        $pathToInputVideo = $this->folder . $videoName;
        $partSize = 5000;
        $prefixSegmentName = 'seg_';
        $mpdFileName = 'mpdesc.mpd';
        $command = sprintf("mp4box -dash %d -frag %d -rap -segment-name %s \"%s\" -out \"%s%s\"",
                           $partSize,
                           $partSize,
                           $prefixSegmentName,
                           $pathToInputVideo,
                           $this->folder,
                           $mpdFileName);

        $result = exec($command, $output, $retval);
        return ($result === false) ? false : true;
    }

    public function getFolder() : string
    {
        return $this->folder;
    }
}