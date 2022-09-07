<?php
include_once 'header.php';
include_once 'footer.php';

function show_template(array $data)
{
    viewHeader($data);
    
    echo 'No permission ERROR';

    viewFooter($data);
}