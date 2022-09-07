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
    
    echo '
        <form method="post" action="/edit-video" enctype="multipart/form-data">
        <div class="form-group">
        <input class="form-control btn-block" type="text" placeholder="Wprowadź tytuł filmu" name="tytul" value="'.$title.'">
        <textarea class="form-control" placeholder="Wprowadź opis filmu" name="opis">'.$description.'</textarea>

        <label for="exampleFormControlFile1">Dodaj miniaturkę</label>
        <img src="'.$img.'">
        <input type="file" class="form-control-file" id="exampleFormControlFile1" name="miniatura">

        <button type="submit" class="btn btn-primary">Submit</button>
        </div>

        <input type="hidden" name="vidid" id="vidid" value="'.$vidid.'">
        </form>
    ';

    viewFooter($data);
}