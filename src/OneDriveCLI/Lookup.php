<?php
namespace OneDriveCLI;
class Lookup {

  public static function item($args) {

    $path = trim(current($args),'/');
    $path_parts = explode('/',$path);
    $drive_name = array_shift($path_parts);
    $path = implode('/',$path_parts);

    $drive_id = Lookup::get_drive_id_by_name($drive_name);
    $item_id = Lookup::get_item_id_by_path($drive_id,$path);
    $data = \OneDriveAPI\Client::item($drive_id,$item_id);
    return $data;

  }
  public static function items($args) {

    $path = trim(current($args),'/');
    $path_parts = explode('/',$path);
    $drive_name = array_shift($path_parts);
    $path = implode('/',$path_parts);

    $drive_id = Lookup::get_drive_id_by_name($drive_name);
    $item_id = Lookup::get_item_id_by_path($drive_id,$path);
    $data = \OneDriveAPI\Client::items($drive_id,$item_id);
    return $data;

  }
  public static function get_drive_id_by_name($drive_name) {

    // load drives list
    $config = \ConfigCLI\Config::get(array('drives'));

    // get drive id
    $drive_id = @$config['drives'][$drive_name] ?: null;

    // throw error if drive not found
    if (empty($drive_id)) {
      $message = array();
      $message[] = "Please configure 'drives.".$drive_name."' to continue: ";
      $message[] = '  . onedrive config drives.'.$drive_name.' <drive_id>';
      $message[] = '';
      $message[] = "Already configured drives: ";
      if (!empty($config['drives']))
        foreach($config['drives'] as $drive_name => $drive_id)
          $message[] = '  . '.$drive_name;
      throw new \Exception(implode("\n",$message));
    }

    // return drive id
    return $drive_id;

  }
  public static function get_item_id_by_path($drive_id,$path) {

    // break path into parts
    $path_parts = explode('/',$path);
    $current_item_id = null;

    // walk path parts getting items
    $walked_parts = array();
    foreach($path_parts as $part) {
      if (empty($part)) continue;
      $walked_parts[] = $part;
      $partkey =  \OneDriveCLI\Format::normalize_key($part);
      $sub_items = Lookup::get_keyed_sub_items($drive_id,$current_item_id);
      $current_item_id = $sub_items[$partkey]['id'];
      if (empty($current_item_id))
        throw new \Exception("No such path: ".implode('/',$walked_parts));
    }

    // treat last item as item id and return
    $item_id = $current_item_id;
    return $item_id;

  }
  public static function get_keyed_sub_items($drive_id,$item_id) {

    $list = \OneDriveAPI\Client::items($drive_id,$item_id);
    $return = array();
    foreach($list as $item) {
      $sortkey = \OneDriveCLI\Format::normalize_key($item['name']);
      $return[$sortkey] = array(
        'name' => $item['name'],
        'id' => $item['id'],
      );
    }
    ksort($return);
    return $return;

  }

}
