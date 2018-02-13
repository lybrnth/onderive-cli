<?php
namespace ConfigCLI;
class Config {

    public function file() {

      $script_location = $_SERVER['SCRIPT_FILENAME'];
      $script_location = realpath($script_location);
      $script_location = dirname($script_location);
      $filename = $script_location.'/config.json';
      return $filename;

    }
    public function read() {

      $filename = Config::file();
      $json = @file_get_contents($filename) ?: '[]';
      $array = @json_decode($json,TRUE) ?: array();
      return $array;

    }
    public function write($array) {

      $filename = Config::file();
      $normalized = json_decode(json_encode($array),TRUE);
      $pretty = json_encode($array,JSON_PRETTY_PRINT);
      file_put_contents($filename,$pretty);

    }
    public function get($key) {

      if (is_array($key)) return Config::get_array($key);
      $array = Config::read();
      $keyparts = explode('/',strtr($key,array('.'=>'/')));
      $lastpart = array_pop($keyparts);
      $current = $array;
      foreach($keyparts as $part) {
        if (!is_array($current[$part]))
          return null;
        $current = $current[$part];
      }
      if (!array_key_exists($lastpart,$current))
        return null;
      $value = $current[$lastpart];
      return $value;

    }
    public function get_array($keys) {

      if (!is_array($keys)) return Config::get($keys);
      $return = array();
      foreach($keys as $key) {
        $return[$key] = Config::get($key);
      }
      return $return;

    }
    public function set($key,$set) {

      $array = Config::read();
      $keyparts = explode('/',strtr($key,array('.'=>'/')));
      $lastpart = array_pop($keyparts);
      $current = &$array;
      foreach($keyparts as $part) {
        if (!is_array($current[$part]))
          $current[$part] = array();
        $current = &$current[$part];
      }
      $current[$lastpart] = $set;
      Config::write($array);

    }
    public function clear($key) {

      $array = Config::read();
      $keyparts = explode('/',strtr($key,array('.'=>'/')));
      $lastpart = array_pop($keyparts);
      $current = &$array;
      foreach($keyparts as $part) {
        if (!is_array($current[$part]))
          return null;
        $current = &$current[$part];
      }
      if (!array_key_exists($lastpart,$current))
        return null;
      unset($current[$lastpart]);
      Config::write($array);

    }

}
