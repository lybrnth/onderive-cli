<?php
namespace ConfigCLI;
class CLI {

  public static function cli($argv) {

    // set timezone
    date_default_timezone_set('America/Montreal');

    // process arguments
    $args = array_slice($argv,1);
    $op = array_shift($args);

    // call method for operation if there is one
    $op_method = 'op_'.$op;
    if (method_exists(get_called_class(),$op_method)) {
      call_user_func_array(get_called_class().'::'.$op_method,array($args));
      return;
    }

    // if we get this far, show usage info
    CLI::op_usage($args);

  }
  public static function op_config($args) {

    if (count($args)==0) {
      $array = \ConfigCLI\Config::read();
      $output = \ConfigCLI\Output::formatted_json($array);
    }
    else if (count($args)==1) {
      $key = array_shift($args);
      $value = \ConfigCLI\Config::get($key);
      $output = $value;
    }
    else if (count($args)==2) {
      if ($args[0]=='rm') {
        $op = array_shift($args);
        $key = array_shift($args);
        \ConfigCLI\Config::clear($key);
        $array = \ConfigCLI\Config::read();
        $output = \ConfigCLI\Output::formatted_json($array);
      } else {
        $key = array_shift($args);
        $set = array_shift($args);
        \ConfigCLI\Config::set($key,$set);
        $array = \ConfigCLI\Config::read();
        $output = \ConfigCLI\Output::formatted_json($array);
      }
    }
    print trim($output)."\n";

  }

}
