<?php

// displays an image.

$id = $id ? $id : $_GET['id'];
$id = $id ? $id : $request->last;

// $img_src = DOMAIN.$request->repo.'/'.IMAGES_DIR.$id;

$file = $request->repo.'/'.IMAGES_DIR.$id;
if (is_file ($file)) {
  $img_src = DOMAIN.$file;
} else {
  $img_src = 'http://drop.ctn.io/k/blog/'.$id;
}

// print $img_src;

header("Location: $img_src");