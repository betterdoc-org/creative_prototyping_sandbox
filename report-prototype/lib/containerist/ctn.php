<?php

define ('CTN_LIB', '../lib/');
// define ('CTN_TPLBASE', './');

// ---------------------------------------------------

// include_once (CTN_LIB.'common/tools.php');
// include_once (CTN_LIB.'yaml/yaml.php');
// include_once (CTN_LIB.'mustache/mustache.php');
// include_once (CTN_LIB.'markdown/markdown.php');


// ---------------------------------------------------

class CTN {
  function CTN ($source) {
    if ($source) {
      // convert old CTN format. that's dirty.
      $source = str_replace ('ctn: ', 'CTN: ', $source);
      // converting done.
      $this->sources ($source);
    }
  }
  
  function sources ($source) {
    if (!begins_with ('CTN: ', $source)) {
      $this->sources[0] = "CTN: standard\n---\n".$source;
    } else {
      $this->sources = explode ("CTN:", $source);
      $pre = array_shift ($this->sources);
      foreach ($this->sources as $key=>$value) {
        $this->sources[$key] = 'CTN: '.$value;
      }
    }
  }
  
  function skin () {
    foreach ($this->sources as $source) {
      $ctn = new CTN_single ($source);
      if ($this->webbase) $ctn->webbase = $this->webbase;
      if ($this->tplbase) $ctn->tplbase = $this->tplbase;
      $output .= $ctn->skin()."\n\n";
      unset ($ctn);
    }
    return $output;
  }
}


class CTN_single {
  
  function CTN_single ($source) {
    if ($source) $this->source ($source);
    $this->_setBases ();
  }
  
  function source ($source) {
    $this->source = $source;
    if (!begins_with ('CTN:', $source)) {
      // if this is just plain standard markdown container
      $this->CTN = 'standard';
      $this->markdown = $source;
      $this->md_count = '1';
    } else {
      $divider = in_str ("\r\n", $source) ? "\r\n" : "\n";
      $parts = explode ("---".$divider, $source);
      // save the structured variables to the object
      $yaml = yaml_read (array_shift ($parts));
      foreach ($yaml as $key => $value) {
        $this->$key = $value;
      }
      // if is stack
      if ($this->CTN == 'stack') {
        // save container origins
        $this->origins = array_filter(explode ("\n", $parts[0]));
      } else if ($this->CTN == 'html') {
        $this->html_source = $parts[0];
      } else {
        // save unstructured markdown texts
        $this->md_count = 0;
        if ($parts) {
          foreach ($parts as $key => $value) {
            $var = ($key == 0) ? "markdown" : "markdown".$key;
            $this->$var = $value;
            $this->md_count++;
          }
        }
      }
    }
  }
  
  function _setBases () {
    if (!$this->tplbase) $this->tplbase = '../skin.ctn.io/squareone/';
    if (!$this->webbase) $this->webbase = 'http://skin.ctn.io/squareone/';
  }

  // for stacks only
  function add ($origin) { 
    array_push ($this->origins, $origin);
  }
  
  function structure () {
    $output_obj = $this;
    $output = "";
    if ($this->CTN == 'stack') {
      $output .= 'CTN: stack'."\n";
      if ($this->site)     $output .= 'site: '.$this->site."\n";
      if ($this->renderer) $output .= 'renderer: '.$this->renderer."\n";
      $output .= "---\n";
      foreach ($this->origins as $o) {
        $output .= $o."\n";
      }
    } else {
      $markdowns = array ();
      foreach ($output_obj as $key=>$value) {
        if (begins_with ('markdown', $key)) {
          array_push ($markdowns, $value);
          unset ($output_obj->$key);
        }
      }
      $output = yaml_ctn ($output_obj);
      foreach ($markdowns as $md) {
        $output .= "\n---\n".$md;
      }
    }
    return $output;
  }

  function skin () {
    $output = $this;
    //
    if ($this->CTN == 'stack') {
      $output->containers = array ();
      foreach ($this->origins as $i=>$origin) {
        if (in_str ('http://', $origin)) {
          $c->origin = $origin;
        } else {
          $c->origin = $this->site.$origin;
        }
        $c->i = $i;
        array_push ($output->containers, $c);
        unset ($c);
      }
    } else if ($this->CTN == 'source') {
      $output->source = $this->source;
    } else {
      for ($i=0; $i<$this->md_count; $i++) {
        $md = ($i == 0) ? "markdown" : "markdown".$i;
        $output->$md = Markdown ($this->$md);
      }
    }

    // do the markdown on items
    if ($this->markdown AND $this->items) {
      foreach ($this->items as $i=>$item) {
        $this->items[$i]['text'] = Markdown ($item['text']);
      }
    }
    // 
    if (!$this->stylesheet) $this->stylesheet = $this->webbase.$this->CTN.'.css';
    //

    if ($this->CTN == 'html') {
      return $this->html_source;
    } else {
      $tplfile = $this->tplbase.$this->CTN.'.html';
      $tpl = file_get_contents ($tplfile);
      $m = new Mustache ();
      return $m->render ($tpl, $output);
    }
  }
}
