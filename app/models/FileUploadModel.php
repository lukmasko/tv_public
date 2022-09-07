<?php

namespace App\Models;

use Kernel\Model\IModel;

class FileUploadModel implements IModel
{
    public $id;
    public $user_id;
    public $file_name;
    public $file_type;
    public $file_size;
    public $part_size;
    public $parts_count;
    public $expect_data_from;
    public $expect_data_to;
    public $last_updated_part;
    public $date_update;
    public $percent;
    public $process_state;
}