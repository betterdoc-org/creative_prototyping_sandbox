<?php

// outputs the container structure

$id = $id ? $id : $_GET['id'];

if (!$id) {
  print 'Error in skinner / stack'."\n";
  print 'No stack ID provided';
  die;
}

if (!$C->stacks[$id]) {
  print 'this stack does not exist yet';
} else {
  $source = $C->stack (['id' => $id]);
  $source = mustache ($source, $request);
  $stack  = new CTN_single ($source);
  $stack->tplbase  = $request->skin.'/';
  $stack->webbase  = DOMAIN.$request->skin.'/';
  $stack->skinbase = DOMAIN.$request->skin.'/';
  $stack->source   = $source;

  // render the payload
  $stack->payload_html = $C->skinner_payload (['id'=>$id]);
  // unset the origins
  unset ($stack->origins);

  if ($admin) {
    $stack->admin = $request->stack_last.'.edit';
  }
  // print_r ($stack->payload_html);
  header ("Content-Type: text/html;charset=utf-8");
  print $stack->skin(); 
}
