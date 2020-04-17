<?php

$id = $id ? $id : $_GET['id'];

if (!$id) {
  print "mod: skinner-container\nAborted. No ID provided.";
  print_r ($request);
  die;
}

// get source
$source = $C->ctn ($id, $parameters);
if ($source == 'empty.') {
  if ($C->mod_exists ($id)) {
    $source = $C->mod ($id);
  } 
//   // let's ignore the prefills for a moment.
  
//   // else {
//   //   $source = mod ('storefill-prefills', $id, $_GET['stack']);
//   // }
}

// header
header ("Access-Control-Allow-Origin: *");
header ("Content-Type: text/html;charset=utf-8");

// any CTN
$ctn = new CTN ($source);
$ctn->tplbase = $request->skin.'/';
$ctn->webbase = DOMAIN.$request->skin.'/';
if ($_GET['css']) $ctn->stylesheet = $_GET['css'];
print $ctn->skin();

