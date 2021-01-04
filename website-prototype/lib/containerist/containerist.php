<?php

class CTN_page extends CTN {

	function CTN_page ($source = false) {
		$this->tplbase = (CTN_TPLBASE AND defined(CTN_TPLBASE)) ? CTN_TPLBASE : '../skin.ctn.io/';
		$this->webbase = (CTN_WEBBASE AND defined(CTN_WEBBASE)) ? CTN_WEBBASE : 'http://skin.ctn.io/';
		$this->divider = "\n---\n";
		$this->set_type ('page');
		if ($source) $this->input_source ($source);
	}

	function structure () {
		if (begins_with('ctn: page', $this->source)) {
			$areas = $this->_source_areas();
			$this->structured = Spyc::YAMLLoad("---\n".$areas[0]);
			$cargolist_str = $areas[1];
		} else {
			$cargolist_str = $this->source;
		}
		$cargolist = explode("\n", trim($cargolist_str));
		foreach ($cargolist as $origin) {
			$this->add ($origin);
		}
		if (!$this->structured['renderer']) {
			$this->renderer('http://ctn.io/');
		}
		$this->is_structured = true;
	}
	
	function renderer ($url) {
		$this->structured['renderer'] = $url;
	}

	function add ($origin) {
		if (!$this->structured['containers']) $this->structured['containers'] = array();
		if (!$this->i) $this->i = 0;
		$container->origin = $origin;
		$container->i = $this->i++;
		if (begins_with('http://', $origin)) $container->absolute = true;
		array_push($this->structured['containers'], $container);
		unset ($container);
	}

	function plain () {
		$output = "ctn: page\n";
		if ($this->structured['site']) {
			$output .= 'site: '.$this->structured['site']."\n";
		}
		if ($this->structured['renderer']) {
			$output .= 'renderer: '.$this->structured['renderer']."\n";
		}
		$output .= "---\n";
		foreach ($this->structured['containers'] as $c) {
			$output .= $c->origin."\n";
		}
		return $output;
	}
    
	function html () {
        $tplsrc = $this->tplbase.$this->type.'.html';
        $this->tpl = file_get_contents($tplsrc);
		$this->structured['stylesheet'] = $this->webbase.$this->type.".css";
        foreach ($this->structured['containers'] as $i => $c) {
            if (in_str ('://', $c->origin)) {
                $skin_origin = $c->origin;
            } else {
                $skin_origin = $c->origin;
            }
            $this->structured['containers'][$i]->origin = $skin_origin;
        }
        $this->structured['source'] = str_replace ('%23', '#', $this->source);
		$m = new Mustache;
		return $m->render($this->tpl, $this->structured);
	}
}





class CTN_set {
	
	function CTN_set ($source = false) {
		if ($source) $this->input_source ($source);
	}
	
	function input_source ($source) {
		$this->source = $source;
		$this->structure();
	}

	function structure () {
		$this->ctns_data = explode("\n--- CTN\n", $this->source);
		$this->structured = Spyc::YAMLLoad("---\n".array_shift($this->ctns_data));
		$this->ctns = array ();
		foreach ($this->ctns_data as $ctn_data) {
			$ctn = new CTN ();
			$ctn->input_source($ctn_data);
			if ($this->structured['stylesheet']) {
				$ctn->structured['stylesheet'] = $this->structured['stylesheet'];
			}
			array_push($this->ctns, $ctn);
			unset ($ctn);
		}
	}
	
	function output () {
		$output = "";
		foreach ($this->ctns as $ctn) {
			$output .= $ctn->output();
		}
		return $output;
	}
}





class CTN {
	var $structured  = array();
	var $type = false;
	
	// methods
	function CTN ($origin = false) {
		$this->tplbase = (CTN_TPLBASE) ? CTN_TPLBASE : '/var/www/skin.ctn.k/';
		$this->webbase = (CTN_WEBBASE) ? CTN_WEBBASE : 'http://skin.ctn.k/';
		$this->divider = "\n---\n";
		if ($origin) {
			$this->origin = $origin;
			$this->load();
			$this->structure();
		}
	}
	
	function input_source ($source) {
		$this->source = $source;
		$this->is_loaded = true;
		$this->structure();
	}

	function load_url ($url) {
		$url = ltrim ($url, "http://");
		$this->source = utf8_encode(request ('http://'.$url));
		$this->is_loaded = true;
	}

	function load_file ($file) {
		$this->source = utf8_encode(file_get_contents ($file));
		$this->is_loaded = true;
	}

	function load ($origin = false) {
		if ($origin) $this->origin = $origin;
		if (in_str('http:/', $this->origin)) {
			$this->load_url ($this->origin);
		} else {
			$this->load_file($this->origin);
		}
	}

	function structure () {
		if (!$this->is_loaded) $this->load();
		if (!begins_with('ctn: ', $this->source)) {
			$this->source = "ctn: standard".$this->divider.$this->source;
		}
		$areas = $this->_source_areas ();

		$this->structured   = Spyc::YAMLLoad("---\n".$areas[0]);
		$this->type         = $this->structured['ctn'];
		$this->unstructured = $areas[1];

		$this->is_structured = true;
	}
	
	function _source_areas () {
		$areas_ = explode($this->divider, $this->source);
		$areas = array();
		$areas[0] = array_shift($areas_);
		$areas[1] = implode($this->divider, $areas_);

		return $areas;
	}

	function set_type($type) {
		$this->type = $type;
		$this->structured['ctn'] = $type;
	}

	function plain () {
		$output  = ltrim(Spyc::YAMLDump($this->structured), "---\n");
		$output .= "\n---\n";
		$output .= ltrim(Spyc::YAMLDump($this->unstructured), "---\n");
		return $output;
	}

	function obj () {
		header ("Content-Type: text/plain;charset=utf-8");
		print_r ($this);
	}

	function html () {
		// html template
		$tplsrc = $this->structured['template'];
		if ($tplsrc) {
			$tpl = (in_str('http://', $tplsrc)) ? request ($tplsrc) : file_get_contents($tplsrc);
		} else {
			if (!$this->tpl) $this->tpl = file_get_contents($this->tplbase.$this->type.'.html');
			if (!$this->tpl) $this->tpl = file_get_contents($this->tplbase.'standard.html');
		}
		// style sheet
		if (!$this->structured['stylesheet']) {
			$this->structured['stylesheet'] = $this->webbase.$this->type.".css";
		}
		if ($this->structured['markdown']) {
			for ($i=0; $i<count ($this->structured['items']); $i++)  {
				$this->structured['items'][$i]['text']  = Markdown($this->structured['items'][$i]['text']);
				$this->structured['items'][$i]['title'] = Markdown($this->structured['items'][$i]['title']);
			}
		}
		$output_arr = $this->structured;
		if ($this->unstructured) {
			$output_arr['unstructured'] = Markdown($this->unstructured);
		} 
		$m = new Mustache;
		return $m->render($this->tpl, $output_arr);
	}

	function output () {
		if ($_GET['plain'] == true) {
#			header ("Content-Type: text/plain;charset=utf-8");
			return $this->plain();
		} else if ($_GET['obj'] == true) {
			return $this->obj();
		} else {
#			header ("Content-Type: text/html;charset=utf-8");
			return $this->html();
		}
	}
}
