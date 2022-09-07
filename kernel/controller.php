<?php
namespace Kernel\Controller;
use Kernel\View;

interface RunableController
{
    //public function run();
}


abstract class WebController implements RunableController
{
    protected $user_id;
    protected $dataView = array();
    protected $args;

    protected function view($template)
    {
        $file = sprintf("../app/templates/%s.php", $template);
        include $file;

        show_template($this->dataView);
    }
}


abstract class ApiController implements RunableController
{
    //public abstract function run();
}