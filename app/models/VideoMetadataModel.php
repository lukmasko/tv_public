<?php

namespace App\Models;

use Kernel\Model\IModel;

class VideoMetadataModel implements IModel
{
    public $id;
    public $user_id;
    public $title;
    public $description;
    public $image;
    public $access;
}