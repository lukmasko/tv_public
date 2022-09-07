<?php

namespace App\Models;
use Kernel\Model\IModel;

class VideoDataModel implements IModel
{
    public $video_id;
    public $part;
    public $data;
}