<?php
include_once 'header.php';
include_once 'footer.php';

function show_template(array $data)
{
    viewHeader($data);

    
    echo '<div>';
        echo '<video width="640" height="360" controls id="video" poster=""></video>';
    echo '</div>';

    echo '<script>
        window.addEventListener("load", function(){ 
            getMediaPresentationData(); 
        });
    </script>';

    viewFooter($data);
}