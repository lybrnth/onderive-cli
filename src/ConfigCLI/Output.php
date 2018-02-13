<?php
namespace ConfigCLI;
class Output {

  public function formatted_json($data) {

    // turn array into pretty printed json
    $output = json_encode($data,JSON_PRETTY_PRINT);
    return $output;

  }
  
}
