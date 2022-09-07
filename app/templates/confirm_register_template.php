<?php
include_once 'header.php';
include_once 'footer.php';

function show_template(array $data)
{
    viewHeader($data);

    $description = $data['description'];
    $title = $data['title'];
    $img = $data['full_path_image'];
    $vidid = $data['vidid'];
    
    echo 'confirm...
    ';

    viewFooter($data);
}