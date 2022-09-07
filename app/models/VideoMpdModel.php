<?php

namespace App\Models;
use Kernel\Model\IModel;

class VideoMpdModel implements IModel
{
    public $id;
    public $video_id;
    public $codecs;
    public $width;
    public $height;
    public $duration;
    public $mime_type;
}