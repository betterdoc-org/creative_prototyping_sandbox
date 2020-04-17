<?php

header ("Access-Control-Allow-Origin: *");
header ("Content-Type: text/plain;charset=utf-8");
error_reporting(E_ERROR | E_PARSE);
include_once ('config.php');

// Base -------------------------------------------
GLOBAL $C;
GLOBAL $request;
$C = new Containerist_Base ();
$request = new Containerist_Request ($_GET['q']);
// print_r ($request);

echo $C->output();





class Containerist_Base {

  function __construct () {
    $this->containers = array ();
    $this->mods = array ();
    $this->conrainer_mods = array ();
    //
    $this->path = new Path ($_GET['q']);
    $this->admin = true;
    //
    $this->register_mods ();
    $this->register_stacks ();
    $this->register_containers ();
    //
    $this->static_containers = $this->get_containers_by_context ('static');
    $this->init_containers ();
  }

  // public function __call ($name, $arguments = false) {
  public function __call ($name, $arguments = false) {
    return $this->mod ($name, $arguments[0]);
  }

  private function init_containers () {
    foreach ($this->containers as $container_id=>$container_path) {
      $this->$container_id = function ($container_id, $arguments) {
        return $this->ctn($container_id, $arguments);
      };
    }
  }

  public function ctn ($id, $arguments = false) {
    $args = ($arguments) ? $arguments : array ();
    $args['id'] = $id;
    return $this->mod ('container', $args);
  }

  public function mod ($name, $arguments = false) {
    GLOBAL $request;
    $path = $this->path;
    $mod_path = $this->mods[$name];
    if (is_file ($mod_path)) {
      // what is the module's directory?
      $dir_path = new Path ($mod_path);
      array_pop ($dir_path->parts); // exclude the mod's name
      $module_dir = implode ('/', $dir_path->parts) . '/';
      //
      $C = $this;
      // begin collecting output
        ob_start();
        extract ($arguments);
        include ($arguments);
        include ($mod_path);
        $result = ob_get_clean(); // end collecting output
        return $result;
    } else {
      print 'no mod with name "' . $name . '" found';
      return false;
    }
  }

  // router
  public function output () {
    GLOBAL $request;
    $path = $this->path;
    $output = "";

    if ($path->suffix == 'mod' AND $this->admin) {
      $output = $this->mod ($path->name);
    }
    else if ($path->suffix == 'ctn') {
      if (in_str ('.stack', $path->full)) {
        $output = $this->source (['id' => $request->stack_id]);
      } else {
        $output = $this->ctn ($path->name);
      }
    }
    else if ($path->suffix == 'raw') {
        $output = $this->ctn ($path->name, ['raw'=>true]);
    }
    else if ($path->suffix == 'html') {
      $output = $this->skinner_container (['id'=>$path->name]);
    }
    else if ($path->suffix == 'stack') {
      $output = $this->stack (['id' => $request->stack_id]);
    }
    else if ($request->is_stack) {
      GLOBAL $request;
      header ("Content-Type: text/html;charset=utf-8");
      $output = $this->skinner_stack (['id' => $request->stack_id]);
    }
    return $output;
  }




  private function register_mods () {
    $modules_directory = 'modules';

    if (is_dir ($modules_directory)) {
      $dir = directory_to_array_recursive ($modules_directory);
      foreach ($dir as $key=>$filepath) {
        if (ends_with('.php', $filepath)) {
          $path = new Path ($filepath);
          $name = substr ($path->last, 0, -4); // delete '.php' from name
          // if this is a container mod
          if (ends_with ('.ctn', $name)) {
            $name = substr ($name, 0, -4); // delete '.ctn' from name
            $this->container_mods[$name] = $filepath;
          }
          // add this mod
          $this->mods[$name] = $filepath;
        }
      }
    }
  }
  private function register_stacks () {
    $stacks_directory = 'stacks';
    $this->stacks = array ();
    if (is_dir ($stacks_directory)) {
      $dir = directory_to_array_recursive ($stacks_directory);
      foreach ($dir as $key=>$filepath) {
        $path = new Path ($filepath);
        $name = substr ($path->last, 0, -4); 
        $this->stacks[$name] = $filepath;
      }
    }
  }
  private function register_containers () {
    $containers_directory = 'containers';
    $this->containers = array ();
    if (is_dir ($containers_directory)) {
      $dir = directory_to_array_recursive ($containers_directory);
      foreach ($dir as $key=>$filepath) {
        $path = new Path ($filepath);
        $name = substr ($path->last, 0, -4); 
        $this->containers[$name] = $filepath;
      }
    }
  }
  private function get_containers_by_context ($context = 'static') {
    $containers_directory = 'containers/'.$context;
    $containers = array ();
    if (is_dir ($containers_directory)) {
      $dir = directory_to_array_recursive ($containers_directory);
      foreach ($dir as $key=>$filepath) {
        $path = new Path ($filepath);
        $name = substr ($path->last, 0, -4); 
        $containers[$name] = $filepath;
      }
    }
    return ($containers);
  }

  public function stack_exists ($stack_id) {
    return $this->stacks[$stack_id];
  }
  
  public function mod_exists ($mod_id) {
    return $this->mods[$mod_id];
  }
}















class Containerist_Request {

  function Containerist_Request ($q) {
    $this->q = $q;

    $this->parts = explode ('/', $this->q);
    $this->count = count($this->parts);
    if (!$this->parts[$this->count-1]) {
      $this->end_with_slash = true;
      unset ($this->parts[$this->count-1]);
      $this->count = count($this->parts);
      $this->q_without_slash = implode ('/', $this->parts);
      $this->is_page = true;
    }
    $this->last = $this->parts[$this->count-1];

    $this->set_parts ();
    //$this->set_repo ();
    $this->is_image ();
    $this->set_type ();
    $this->find_stack ();
    $this->set_skin ();
  }
  function set_parts () {
    if ($this->parts[0]) $this->first = $this->parts[0];
    if ($this->parts[1]) $this->second = $this->parts[1];
    if ($this->parts[2]) $this->third = $this->parts[2];
    if ($this->parts[3]) $this->fourth = $this->parts[3];
  }

  function is_image () {
    if (ends_with ('.png',  $this->last)) $this->is_image = true;
    if (ends_with ('.gif',  $this->last)) $this->is_image = true;
    if (ends_with ('.svg',  $this->last)) $this->is_image = true;
    if (ends_with ('.jpg',  $this->last)) $this->is_image = true;
    if (ends_with ('.jpeg', $this->last)) $this->is_image = true;
  }

  function set_repo () {
    $this->repo = $this->first;
  }

  function set_type () {
    $parts_ = $this->parts;
    if (ends_with ('.stack.ctn', $this->last)) {
      $this->is_stack = true;
      $this->is_ctn = true;
    } else if (ends_with ('.ctn', $this->last)) {
      $this->is_ctn = true;
    } else if (ends_with ('.ctn.edit', $this->last)) {
      $this->is_ctn = true;
      $this->editable = true;
    } else if (ends_with ('.ctn.html', $this->last)) {
      $this->is_ctn = true;
    } else if (ends_with ('.stack', $this->last)) {
      $this->is_stack = true;
    } else if (ends_with ('.edit', $this->last)) {
      $this->is_stack = true;
      $this->editable = true;
      $this->last = substr ($this->last, 0, -5);
      $this->parts[$this->count-1] = $this->last;
    } else if (ends_with ('.mod', $this->last)) {
      $this->is_mod = true;
    } else {
      // $this->is_page = true;
      $this->is_stack = true;
    }
  }

  function find_stack () {
    GLOBAL $C;
    $this->stack_id = false;    // admit that we don't know the stack yet
    $parts = $this->parts;      // create a local copy of the parts of the url
    if (!$this->is_stack) {     // sometimes we know that that's a stack
      array_pop ($parts);       // leave the last part out
    } else if (ends_with ('.stack', $this->last) OR ends_with ('.stack.ctn', $this->last)) { // that's obviously a stack
      $parts[count($parts)-1] = extract_id ($parts[count($parts)-1]); // FIXIT what happens here????
      // print_r ($parts);
    }
    $this->stack_parts = $parts;  // set the parts of the stack

    $this->stack_slug  = implode ('/', $parts);   // creating the slug (= without the container_id or suffix)
    $parts_count = count ($parts);            // how many parts?
    $this->stack_parts_count = $parts_count;  // FIXIT what's that???
    $this->stack_last  = $parts[$parts_count-1];  // the last part often indicates container_id or stack_id

    // if path has two or more parts
    if ($parts_count > 1) {                    // if more than one part
      $stack_id = implode ('--', $parts);     // with more than one part, this is how the stack_id is put together
      $this->_set_stack_id ($stack_id);       // 
      for ($i = $parts_count-1; $i > 0; $i--) {
        $parts[$i] = '*';
        $stack_id = implode ('--', $parts);
        $this->_set_stack_id($stack_id);
      }

    // if path only has one part
    } elseif ($parts_count > 0) {
      if ($C->stack_exists ($parts[0])) {       // if e.g. "articles" is a defined stack instance
        $this->_set_stack_id ($stack_id);
      } else {                                  // if e.g. "macintosh-plus" is not a defined stack
        $this->_set_stack_id ('*');
      }
      $this->stack_last = $parts[0];

    // if path has nothing
    } else {                                    
      $this->_set_stack_id ('index');           // can only be index
    }
  }
  function _set_stack_id ($id_) {
    GLOBAL $C;
    if (!$this->stack_id) {
      if ($C->stack_exists ($id_) AND !$ignore_existing) {
        $this->stack_id = extract_name ($id_);
      }
    }
  }

  function set_skin () {
    // $repo_skin = $this->repo.'/skin';
    // if (is_dir ($repo_skin)) {
    //   $this->skin = $repo_skin;
    // } else {
    //   $this->skin = 'skins/'.SKIN;
    // }
    $this->skin = SKIN;
  }

}
