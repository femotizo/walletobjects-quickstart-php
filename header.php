<?php
/**
 * Copyright 2013 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

set_include_path(get_include_path() . PATH_SEPARATOR . 'google-api-client/src');
/**
 * Configuration file.
 */
require_once 'config.php';

/**
 * Google client library class.
 */
require_once 'google-api-client/src/Google/Client.php';

/**
 * Google wallet object service.
 */
require_once 'google-api-client/src/Google/Service/Walletobjects.php';

/**
 * Generates wob payload.
 */
require_once 'utils/wob_payload.php';

/**
 * Autoloading classes when their object is required.
 */
function __autoload($class_name) {
  if (is_file('verticals/'.$class_name . '.php')) {
    require_once 'verticals/'.$class_name . '.php';
  }
}

$client = new Google_Client();
// Set application name.
$client->setApplicationName(APPLICATION_NAME);
// Set Api scopes.
$client->setScopes(array(SCOPES));
// Set your cached access token. Remember to replace $_SESSION with a
// real database or memcached.
session_start();

if (isset($_SESSION['service_token'])) {
  $client->setAccessToken($_SESSION['service_token']);
}
// Load the key in PKCS 12 format (you need to download this from the
// Google API Console when the service account was created.
$key = file_get_contents(SERVICE_ACCOUNT_PRIVATE_KEY);
$cred = new Google_Auth_AssertionCredentials(
    SERVICE_ACCOUNT_EMAIL_ADDRESS,
    array(SCOPES),
    $key
);
$client->setAssertionCredentials($cred);
if($client->getAuth()->isAccessTokenExpired()) {
  $client->getAuth()->refreshTokenWithAssertion($cred);
}
$_SESSION['service_token'] = $client->getAccessToken();

// Wallet object service instance.
$service = new Google_Service_Walletobjects($client);
