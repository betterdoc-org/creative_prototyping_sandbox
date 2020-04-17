<?php

class Path {
    function Path ($string) {
        $this->original = $string;
        $this->full = rtrim($string, "/");
        $this->structure();
    }
    
    function structure () {
        $parts = explode ('/', $this->full);
        $this->parts  = ($parts) ? $parts : $this->full;
        $this->count  = count($this->parts);
        $this->first  = $this->parts[0];
        $this->second = $this->parts[1];
        $this->third  = $this->parts[2];
        $this->fourth = $this->parts[3];
        $this->last   = array_pop($parts);
        $this->nolast = implode($parts, '/');
        $parts2 = $this->parts;
        unset ($parts2[0]);
        $this->nofirst = implode($parts2, '/');
        $this->name    = $this->name();
        $this->suffix  = $this->suffix();
    }
    
    function do_droppath () {
        if (ends_with('.ctn', $this->last)) {
            $this->droppath = $this->nolast;
            $this->name = $this->parts [$this->count-2];
            $this->command = str_replace ('.ctn', '', $this->last);
        } else {
            $this->name = $this->last;
            $this->droppath = $this->last;
        }
    }

    function suffix () {
        $parts = explode (".", $this->last);
        return array_pop ($parts);
    }

    function name () {
        $parts = explode (".", $this->last);
        return array_shift ($parts);
    }
}
