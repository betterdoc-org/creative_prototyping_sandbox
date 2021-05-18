<?php

/**
 * returns a stack's source
 *
 * @param string $id of the stack
 * @return string CTN list
 */

$id = $id ? $id : $_GET['id'];
$id = $id ? $id : $arguments[0];

if (!$id) {
	print 'Error in: stack / stack'."\n";
	print 'No stack ID provided';
	die;
}

$filepath = $C->stacks[$id];
if (!$filepath) {
	print 'no stack yet.';
} else {
	$source = file_get_contents ($filepath);
	$stack = new CTN_single ($source);
	$stack->site = DOMAIN.$repo.'/';
	$stack->renderer = RENDERER;
	foreach ($stack->origins as $i=>$origin) {
		if (in_str ('http:', $origin)) {

		} else {
			if (in_str ('?', $origin)) {
				$parts = explode('?', $origin);
				$parts[0] = $parts[0].'.ctn';
				$origin = implode ('?', $parts);
			} else {
				$origin = $origin.'.ctn';
			}
		}
		$stack->origins[$i] = $origin;
	}

	print $stack->structure ();
}