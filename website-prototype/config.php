<?php
define ("LIB", "lib/");

define ("DOMAIN",   "http://localhost/");
define ("SKINNING_LOCAL", "true");

define ("RENDERER", DOMAIN."html/");
define ('SKIN', 'skin');

// define ('CTN_TPLBASE', 'skins/'.SKIN.'/');
define ('CTN_TPLBASE', SKIN.'/');
define ('CTN_WEBBASE', 'http://'.DOMAIN.'/'.$skin.'/');
$domain = DOMAIN;

define ('CONTAINERS_DIR', 'containers/');
define ('STACKS_DIR',     'stacks/');
define ('MODULES_DIR',    'modules/');
define ('IMAGES_DIR',     'images/');

GLOBAL $admin;
$admin = false;
GLOBAL $root;
$root = './';


include_once (LIB.'yaml/yaml.php');
include_once (LIB.'mustache/mustache.php');
include_once (LIB.'markdown/markdown.php');
include_once (LIB.'common/tools.php');
include_once (LIB.'containerist/ctn.php');
include_once (LIB.'path/path.php');

