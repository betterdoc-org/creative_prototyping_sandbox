<?php

$stack_id = $id ? $id : $_GET['id'];

if (!$stack_id) {
  print "mod: skinner-payload\nAborted. No Stack ID provided.";
  die;
}

$source = $C->stack (['id'=>$stack_id]);
$source = mustache ($source, $request);
$stack = new CTN_single ($source);

$html = '';

foreach ($stack->origins as $origin) {
  $nid = extract_name ($origin);
  if (in_str ("?", $origin)) {
    //
    // OUTDATED:
    // this merges the parameters of that url into the common _GET array
    // it's dirty, since
    // if two containers need both a parameter with the same key but different values,
    // this solution will not work
    // $parts = parse_url($origin);
    // parse_str($parts['query'], $query);
    // $_GET = array_merge ($_GET, $query);
    //
    // REWORKED INSTEAD.
    $parts = parse_url($origin);
    parse_str($parts['query'], $query);
    $parameters = $query;
  } else {
    $parameters = false;
  }
  print $C->skinner_container (['id'=>$nid, 'parameters'=>$parameters]);
}
