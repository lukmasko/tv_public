<?php
include_once 'header.php';
include_once 'footer.php';

function show_template(array $data)
{
    viewHeader($data);

    echo '<section class="py-5 text-center container">
    <div class="row py-lg-5">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="fw-light">Upload video</h1>
        <p class="lead text-muted">Please update your video in this place</p>


        <div class="custom-file">
          <input type="file" class="custom-file-input" id="upload_file" required onchange="loadFile(this)">
          <label class="custom-file-label" for="upload_file">Choose file...</label>
          <div class="invalid-feedback">Example invalid custom file feedback</div>
        </div>
        <div class="progress">
          <div class="progress-bar" id="progress_bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div>
          <span id="percentage_value" style="padding-left:7px;"></span>
        </div>
      </div>
    </div>
  </section>';
    
    /*echo '
        <div id="upv_load">
            <p><input type="file" id="upload_file" style="color: transparent;" onchange="loadFile(this)" /></p>
            <p><progress max="100" value="0" id="progress_bar"></progress><span id="percentage_value" style="padding-left:7px;"></span></p>
        </div>

        <div id="upv_send">
            <form name="upv_form" id="loadfileform_id" enctype="multipart/form-data" method="post" action="/save-video" >
                <p>Tytuł: <input type="text" placeholder="Wprowadź tytuł filmu" name="upv_title"></p>
                <p>Opis: <textarea name="upv_description" placeholder="Wprowadź opis filmu"></textarea></p>
                <input type="submit" name="" value="Save"/>
            </form>
        </div>';*/

    viewFooter($data);
}