OneDrive CLI
============

Install
-------

Install this command line app by running this command from the onedrive-cli
directory:

. `deploy.bash`

Configure
---------

Configure the app by setting the tenant_id, client_id, and client_secret by
running these commands:

. `onedrive config tenant_id <value>`
. `onedrive config client_id <value>`
. `onedrive config client_secret <value>`

Setting up an application
-------------------------

In order to use this command line app, you'll need to have created and authorized
a microsoft graph application. Here is the general process for doing that:

. TENANT ID: Determine your tenant ID. It's probably {orgname}.onmicrosoft.com. The first part
  of your sharepoint hostname is probably your orgname. https://xyzorg.sharepoint.com/.

  If {orgname}.onmicrosoft.com does not work, you can find the not-human-readable-GUI version of
  your tennant id. You can find that GUID by opening an incogneto window and visiting your login page:
  https://xyzorg.sharepoint.com/ -- then, you'll see your tennant ID in the URL bar:
  https://login.microsoftonline.com/{TENANT_ID}/oauth2/authorize. Copy it into your
  code or config file.

. A NEW APP: Make a new app by visiting https://apps.dev.microsoft.com - then click "Add an APP".
  Give your app a name, and skip the "Let us help you get started". It's a lie, they won't really help.

. YOUR CLIENT ID: Your client id is your app id. From the app screen, you'll see it written under
  "Application ID". Copy that into your code or config file.

. YOUR CLIENT SECRET: Your client secret is a randomly generated password. Under "Application Secrets"
  click "Generate new password". That password will only be shown once. Copy it into your code or config
  file.

. APPLICATION PERMISSIONS: Give your application Application Permissions. I am not 100% which are needed,
  for now I used:
    . Directory Read All
    . Files Read All
    . Sites Read All
    . User Read All

. Get an admin to give your app permission. Do that by building this URL:
  https://login.microsoftonline.com/{tenant_id}/adminconsent?client_id={application id}&state={state}&redirect_uri={redirect uri}
  I have no idea if what {state} is, I just used "12345" and it worked. I also don't believe that {redirect_uri} is required. If it is you'll need to click "add platform" and set a redirect_uri.
  If you have trouble, this article will help:
  https://tsmatz.wordpress.com/2016/10/07/application-permission-with-v2-endpoint-and-microsoft-graph/

  (This link might also help: https://developer.microsoft.com/en-us/graph/docs/concepts/auth_v2_service )

. Use the offical microsoft php sdk located at:
  https://github.com/microsoftgraph/msgraph-sdk-php

. Use the following code to test it:

  ```
  $method = "/users/activistsite@ontariondp.ca";
  $guzzle = new \GuzzleHttp\Client();
  $url = 'https://login.microsoftonline.com/' . $config['TENANT_ID'] . '/oauth2/v2.0/token';
  $token = json_decode($guzzle->post($url, [
      'form_params' => [
          'client_id' => $config['ONEDRIVE_CLIENT_ID'],
          'client_secret' => $config['ONEDRIVE_CLIENT_SECRET'],
          'grant_type' => 'client_credentials',
          'scope' => 'https://graph.microsoft.com/.default'
      ],
  ])->getBody()->getContents());
  $accessToken = $token->access_token;
  $graph = new \Microsoft\Graph\Graph();
  $graph->setAccessToken($accessToken);
  $user = $graph->createRequest("GET", "$method")
          ->setReturnType( \Microsoft\Graph\Model\User::class )
          ->execute();
  print_r($user);
  ```
  
