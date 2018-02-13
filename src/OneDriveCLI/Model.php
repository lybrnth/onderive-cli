<?php
namespace OneDriveCLI;
class Model {

  public static function model_drive($data) {

    $modeled = array();
    $modeled['name'] = $data['name'];
    $modeled['url'] = $data['webUrl'];
    $modeled['id'] = $data['id'];
    return $modeled;

  }
  public static function model_drives($data) {

    $modeled = array();
    foreach($data as $drive) {
      $modeled[] = Model::model_drive($drive);
    }
    return $modeled;

  }
  public static function model_item($data) {

    $modeled = array();
    $modeled['name'] = $data['name'];
    $modeled['items'] = $data['folder']['childCount'];
    $modeled['modified'] = date('Y-m-d',strtotime($data['lastModifiedDateTime']));
    $modeled['id'] = $data['id'];
    return $modeled;

  }
  public static function model_items($data) {

    $modeled = array();
    foreach($data as $item) {
      $modeled[] = Model::model_item($item);
    }
    return $modeled;

  }
  public static function model_download_link($data) {

    $modeled = $data['@microsoft.graph.downloadUrl'];
    return $modeled;

  }

}
