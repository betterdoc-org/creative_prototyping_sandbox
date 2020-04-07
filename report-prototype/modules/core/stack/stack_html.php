<?php

// outputs the container structure

$id = $id ? $id : $_GET['id'];
$skinning_local = $skinning_local ? $skinning_local : $_GET['skinning_local'];
$skinning_local = $skinning_local ? $skinning_local : $request->skinning_local;

if (!$id) {
	print 'error: no stack ID provided';
	die;
}


if (!$C->stacks[$id]) {
	print 'no stack yet.';
} else {
	$source          = mod ('stack', $id);
	$stack           = new CTN_single ($source);
  $stack->tplbase  = $request->skin.'/';
  $stack->webbase  = DOMAIN.$request->skin.'/';
  $stack->skinbase = DOMAIN.$request->skin.'/';

	$stack->site     = $stack->site.$id.'/';
  if ($admin) {
    $stack->admin  = $request->stack_slug.'.edit';
  }

  // local skinning?
  if ($skinning_local) {
    unset ($stack->renderer);
    $stack->renderer_suffix = '.html';
    // print_r ($stack);
    // die;
  }

	header ("Content-Type: text/html;charset=utf-8");
	print $stack->skin();
}