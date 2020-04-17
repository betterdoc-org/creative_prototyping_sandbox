<?php

/**
 * Lists all containers in this repo, in 'static' context per default
 *
 * @param string $context (optional) the context / directory of which containers should be listed
 * @return sring CTN list
 */

// set context
$context = $context ? $context : $_GET['context'];
$context = $context ? $context : 'static';

// set sorting (default = true)
$sort = $sort ? $sort : $_GET['sort'];
$sort = $sort ? $sort : $param2;
$sort = $sort ? $sort : true;

// get list of containers
if ($context == 'static') {
  $list = $C->static_containers;
} else {
  $list = $C->get_containers_by_context ($context);
}

// set keys to values, ignore hidden (staring with '_')
$container_ids = array ();
foreach ($list as $key=>$value) {
  if (!is_hidden ($key)) {
    array_push($container_ids, $key);
  }
}
if ($sort) sort ($container_ids);

// display list
$ctn->CTN = 'list';
$ctn->items = $container_ids;
print yaml_ctn ($ctn);
