<?php
include_once 'header.php';
include_once 'footer.php';

function show_template(array $data)
{
    viewHeader($data);
    
    echo '<div id="playlist">';
    foreach($data['items'] as $key => $value)
    {
        $image_path = sprintf("/media/images/ss/%s", $value->image);
        $title = $value->title;
        $link = sprintf("/edit-video/%s", $value->id);

        if($key%3 == 0) echo '<div id="row">';
            echo '<div id="item-container">';
                echo '<a href="'. $link .'">';
                    echo '<img src="'. $image_path .'">';
                echo '</a>';
                echo '<span>'. $title .'</span>';
            echo '</div>';
        if($key%3 == 2) echo '</div>';
    }
    echo '</div>';

    viewFooter($data);
}