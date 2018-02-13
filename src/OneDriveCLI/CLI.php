<?php
namespace OneDriveCLI;
class CLI {

  public static function cli($argv) {

    // process arguments
    $args = array_slice($argv,1);
    $op = array_shift($args);
    $op_method = 'op_'.strtr($op,'-','_');

    // call method for operation if there is one
    if (method_exists( self::class, $op_method )) {
      try {
        call_user_func_array( self::class . '::' . $op_method, array($args) );
        return;
      }
      catch( \GuzzleHttp\Exception\ClientException $e ) {
        $message = $e->getResponse()->getBody();
        print trim($message)."\n";
        return;
      }
      catch( \Exception $e ) {
        $message = $e->getMessage();
        print \OneDriveCLI\Output::border_box($message);
        return;
      }
    }

    // if we get this far, show usage info
    CLI::op_usage($args);

  }
  public static function op_usage($args) {

    // print usage info
    $output = array();
    $output[] = "USAGE: worklog [op] [arg1] [arg2]";
    $output[] = "OPS:";
    $methods = get_class_methods( self::class );
    foreach($methods as $method) {
      if (!preg_match('/^op_[a-z]+/',$method)) continue;
      $op_name = strtr(preg_replace('/^op_/','',$method),'_','-');
      $output[] = "  . ".$op_name;
    }
    print \OneDriveCLI\Output::border_box($output);

  }
  public static function op_config($args) {

    // run op config from config cli
    \ConfigCLI\CLI::op_config($args);

  }
  public static function op_connect($args) {

    // print output from demo function
    $connected = \OneDriveAPI\Client::connect($args[0]);
    $output = $connected ? 'Connected' : 'Failed';
    print \OneDriveCLI\Output::border_box($output);

  }
  public static function op_call($args) {

    // print output from demo function
    $data = \OneDriveAPI\Client::call($args[0]);
    $output = \OneDriveCLI\Output::formatted_json($data);
    print trim($output)."\n";

  }
  public static function op_drive_search($args) {

    // print output from demo function
    $data = \OneDriveAPI\Client::drive_search($args[0]);
    $data = array( \OneDriveCLI\Model::model_drive($data) );
    $output = \OneDriveCLI\Output::whitespace_table($data);
    $output = \OneDriveCLI\Output::border_box($output);
    print trim($output)."\n";

  }
  public static function op_drive_search_raw($args) {

    // print output from demo function
    $data = \OneDriveAPI\Client::drive_search($args[0]);
    $output = \OneDriveCLI\Output::formatted_json($data);
    print trim($output)."\n";

  }
  public static function op_item($args) {

    // print output from demo function
    $data = \OneDriveCLI\Lookup::item($args);
    $data = array( \OneDriveCLI\Model::model_item($data) );
    $output = \OneDriveCLI\Output::whitespace_table($data);
    $output = \OneDriveCLI\Output::border_box($output);
    print trim($output)."\n";

  }
  public static function op_item_raw($args) {

    // print output from demo function
    $data = \OneDriveCLI\Lookup::item($args);
    $output = \OneDriveCLI\Output::formatted_json($data);
    print trim($output)."\n";

  }
  public static function op_items($args) {

    // print output from demo function
    $data = \OneDriveCLI\Lookup::items($args);
    $data = \OneDriveCLI\Model::model_items($data);
    $output = \OneDriveCLI\Output::whitespace_table($data);
    $output = \OneDriveCLI\Output::border_box($output);
    print trim($output)."\n";

  }
  public static function op_items_raw($args) {

    // print output from demo function
    $data = \OneDriveCLI\Lookup::items($args);
    $output = \OneDriveCLI\Output::formatted_json($data);
    print trim($output)."\n";

  }
  public static function op_download($args) {

    // print output from demo function
    $data = \OneDriveCLI\Lookup::item($args);
    $download_link = \OneDriveCLI\Model::model_download_link($data);
    $output = file_get_contents($download_link);
    print trim($output)."\n";

  }
  public static function op_download_link($args) {

    // print output from demo function
    $data = \OneDriveCLI\Lookup::item($args);
    $download_link = \OneDriveCLI\Model::model_download_link($data);
    $output = $download_link;
    print trim($output)."\n";

  }


}
