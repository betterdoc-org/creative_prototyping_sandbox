<?php

/**
 * checks if a stack exists
 *
 * @param boolean $id
 * @return boolean
 */

$stack_id = $stack_id ? $stack_id : $_GET['id'];

if (!$stack_id) {
	print 'error: no stack ID provided';
	die;
}

$filepath = $C->stacks[$stack_id];
$result = ($filepath) ? '1' : '0';
print $result;
