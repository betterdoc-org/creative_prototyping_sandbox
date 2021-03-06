<?php
$origin = $id ? $id : $_GET['origin'];

// normalise origin concerning 'http://'
if (!in_str ('http://', $origin)) {
  $origin = str_replace ('http:/', '', $origin);
} else {
  $origin = str_replace ('http://', '', $origin);
}
$origin = 'http://'.$origin;

// set node skin
$q_ = str_replace (DOMAIN, '', $origin);
$request = new Containerist_Request ($q_);
// print_r ($request);


// header
header ("Access-Control-Allow-Origin: *");
header ("Content-Type: text/html;charset=utf-8");

$params = explode('&', $_SERVER['QUERY_STRING']);
array_shift ($params);
if ($params) {
	$params_str = "?";
	foreach ($params as $param) {
		$params_str .= $param . "&";
	}
	$params_str = rtrim($params_str, "&");
} else {
	$params_str = "";
}

if ($origin) {
  $source = file_get_contents ($origin.$params_str);

	// any CTN
  $ctn = new CTN ($source);
  $ctn->tplbase = $request->skin.'/';
  $ctn->webbase = DOMAIN.$request->skin.'/';
  if ($_GET['css']) $ctn->stylesheet = $_GET['css'];
  print $ctn->skin();

} else {
	header ("Content-Type: text/plain;charset=utf-8");
	print 'no origin provided.';
}
