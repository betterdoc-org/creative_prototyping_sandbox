<?php
/**
 * outputs the context of a container
 *
 * @param string $id / the container
 * @return string
 */



// set old and new container name
$id = ($id) ? $id : $_GET['id'];

if (!$id) {
	print 'error: no id given';
	die;
}

// check errors
if (!$C->containers[$id]) {
	print 'error: container not found.';
	die;
}

GLOBAL $C;
// get the context (container's directory)
$idpath = $C->containers[$id];
$_path = new Path ($idpath);
$context = $_path->parts[$_path->count-2]; // the second last part of the path

print $context;