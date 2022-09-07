<?php

function viewHeader(array $data){
    echo '<!DOCTYPE html>';
    echo '<html>';
        echo '<head>';
            echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';
            echo '<script type="text/javascript" src="/media/scripts/upload-video-worker.js"></script>';
            echo '<script type="text/javascript" src="/media/scripts/get-video.js"></script>';
            echo '<link href="/media/styles/main.css" media="screen" rel="stylesheet" type="text/css" />';
        echo '</head>';
        echo '<body>';

        echo '
        <nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
              <a class="nav-link" href="/">TV-Online</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="/upload-video">Upload</a>
            </li>
          </ul>
        </div>
      </nav>
        ';
}        