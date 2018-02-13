<?php
namespace OneDriveAPI;
class Client {

  public static function connect() {

    // load credentials
    $config = \ConfigCLI\Config::get(array(
      'client_id',
      'client_secret',
      'tenant_id',
      'access_token'
    ));

    // confirm required config params are present
    $missing = array();
    if (empty($config['client_id'])) $missing[] = 'client_id';
    if (empty($config['client_secret'])) $missing[] = 'client_secret';
    if (empty($config['tenant_id'])) $missing[] = 'tenant_id';
    if (!empty($missing)) {
      $message = array();
      $message[] = "Please configure '".implode("','",$missing)."' settings to continue: ";
      foreach($missing as $field)
        $message[] = '  . onedrive config '.$field.' <value>';
      throw new \ConfigCLI\ConfigException(implode("\n",$message));
    }

    // get a new access token
    $guzzle = new \GuzzleHttp\Client();
    $url = 'https://login.microsoftonline.com/' . $config['tenant_id'] . '/oauth2/v2.0/token';
    $token = json_decode($guzzle->post($url, [
        'form_params' => [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            //'resource' => 'https://graph.microsoft.com/',
            'grant_type' => 'client_credentials',
            'scope' => 'https://graph.microsoft.com/.default'
        ],
    ])->getBody()->getContents());
    $config['access_token'] = $token->access_token;
    \ConfigCLI\Config::set('access_token',$config['access_token']);

    return !empty($config['access_token']);

  }

  public static function call($method,$reconnect_if_needed=TRUE) {

    try {
      $graph = Client::get_api();
      $response = $graph->createRequest("GET", "$method")->execute();
      $data = $response->getBody();
    }
    catch (\GuzzleHttp\Exception\ClientException $e) {
      $response = $e->getResponse();
      $data = $response->getBody();
      $decoded = json_decode($data,true);
      $error_code = $decoded['error']['code'];
      if ($error_code=='InvalidAuthenticationToken' && $reconnect_if_needed) {
          Client::connect();
          Client::call($method,FALSE);
      } else {
        throw $e;
      }
    }
    //$data = json_decode(json_encode($data),TRUE);
    return $data;

  }
  public static function drive_search($sitename) {

    // load credentials
    $config = \ConfigCLI\Config::get(array(
      'tenant_id',
    ));

    // confirm required config params are present
    $missing = array();
    if (empty($config['tenant_id'])) $missing[] = 'tenant_id';
    if (!empty($missing)) {
      $message = array();
      $message[] = "Please configure '".implode("','",$missing)."' settings to continue: ";
      foreach($missing as $field)
        $message[] = '  . onedrive config '.$field.' <value>';
      throw new \ConfigCLI\ConfigException(implode("\n",$message));
    }

    // build method from tenant_id and sitename
    $sharepoint_id = current(explode('.',$config['tenant_id'])).'.sharepoint.com';
    $method = '/sites/'.$sharepoint_id.':/sites/'.$sitename.':/drive';

    $graph = Client::get_api();
    $response = $graph->createRequest("GET", "$method")->execute();
    $data = $response->getBody();
    return $data;

  }
  public static function item($drive_id,$item_id) {

    $method = "/drives/".$drive_id."/items/".$item_id;
    $data = Client::call($method);
    return $data;

  }
  public static function items($drive_id=null,$parent_id=null) {

    if (empty($drive_id)) {

      // load drive id
      $config = \ConfigCLI\Config::get(array(
        'drive_id',
      ));

      // confirm required config params are present
      $missing = array();
      if (empty($config['drive_id'])) $missing[] = 'drive_id';
      if (!empty($missing)) {
        $message = array();
        $message[] = "Please configure '".implode("','",$missing)."' settings to continue: ";
        foreach($missing as $field)
          $message[] = '  . onedrive config '.$field.' <value>';
        throw new \ConfigCLI\ConfigException(implode("\n",$message));
      }
      $drive_id = $config['drive_id'];

    }
    if (empty($parent_id)) $parent_id = 'root';

    $method = "/drives/".$drive_id."/items/".$parent_id."/children";
    $data = Client::call($method);
    return $data['value'];

  }
  public static function get_api() {

    // load credentials
    $config = \ConfigCLI\Config::get(array(
      'access_token'
    ));

    // get access token if needed
    if (empty($config['access_token'])) {
      Client::connect();
    }

    // get and return graph object
    $graph = new \Microsoft\Graph\Graph();
    $graph->setAccessToken($config['access_token']);
    return $graph;

  }

}
