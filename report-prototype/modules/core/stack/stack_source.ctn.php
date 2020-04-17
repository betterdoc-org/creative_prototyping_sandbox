<?php
$id = $id ? $id : $_GET['id'];
$id = $id ? $id : $node->stack_id;

if (ends_with ('.stack.ctn', $path->full)) {
  $temp_path = str_replace ('.stack.ctn', '', $path->full);
  $node = new Containerist_Request ($temp_path);
  // print_r ($temp_node);
  $stack_source = $C->stack (['id'=>$node->stack_id]);
} else {
  $stack_source = $C->stack (['id'=>$request->stack_id]);
}

$stack = new CTN_single ($stack_source);
// print_r ($stack->origins);

foreach ($stack->origins as $origin) {
  // check if the data origin has parameters
  $parts_ = explode ('?', $origin);
  $params = array ();
  if ($parts_[1]) {
    // data origin has parameters. turn them into variables.
    $params_ = explode ('&', $parts_[1]);
    foreach ($params_ as $param) {
      $param_parts = explode ('=', $param);
      $params[$param_parts[0]] = $param_parts[1];
    }
  }
  $id = extract_name ($origin);
  print $C->ctn ($id, $params);

  print "\n\n\n";
}