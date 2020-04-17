<?php

// outputs the container source

$id = $id ? $id : $_GET['id'];

$raw = $raw ? $raw : $_GET['raw'];
$raw = $raw ? $raw : false;

if (!$id) {
	print 'error: no container ID provided';
	die;
}

$filepath = $C->containers[$id];
if (!$filepath) {
	print 'empty.';
} else {
  $source = file_get_contents ($filepath);
  if ($raw) {
    print $source;
  } else {
    $vars = $_GET;

    foreach ($path as $key=>$value) {
      $vars[$key] = $value;
    }
    foreach ($request as $key=>$value) {
      $vars[$key] = $value;
    }

    // ...
    $source_structured = yaml_read ($source);

    // data injection from other containers, 
    // which are specified in the "data: " var 
    // of the container source.
    if ($source_structured['data']) {
      $source_to_structure = mustache ($source, $vars);
      $source_structured = yaml_read ($source_to_structure);
      $data_source = $source_structured['data'];
      // if there is only one data source, 
      // then make it the first of a data array
      if (!is_array($data_source)) {
        $data_source_   = $data_source;
        $data_source    = array ();
        $data_source[0] = $data_source_;
      }
      $previous_source = false;
      // work the data array
      foreach ($data_source as $data_origin) {
        $data_id = extract_id ($data_origin);
        // check if the data origin has parameters
        $parts_ = explode ('?', $data_origin);
        $params = array ();
        if ($parts_[1]) {
          // data origin has parameters. turn them into variables.
          $params_ = explode ('&', $parts_[1]);
          foreach ($params_ as $param) {
            $param_parts = explode ('=', $param);
            $params[$param_parts[0]] = $param_parts[1];
          }
        }
        // previous source should be given as an input
        // i add it to the parameters array, and pass it over to the ctn mod.
        array_unshift ($params, ['input'=>$previous_source]);
        $data_source = $C->ctn ($data_id, $params);
        $data = new CTN_single ($data_source);
        foreach ($data as $key=>$value) {
          $data_vars[$key] = $value;
        }
        $previous_source = $data_source;
      }
      $vars = array_merge ($data_vars, $vars);
      $source = mustache ($source, $vars);
    } else {
      $source = mustache ($source, $vars);
    }

    // templating
    if ($source_structured['template']) {
      $tpl_id = extract_id ($source_structured['template']);
      $tpl    = $C->ctn ($tpl_id, ['raw'=> true]);
      $source_ctn = new CTN_single ($source);
      if ($data_vars) {
        foreach ($data_vars as $key=>$value) {
          $source_ctn->$key = $value;
        }
      }
      $source = mustache ($tpl, $source_ctn);
    }

    echo $source;
  }
}