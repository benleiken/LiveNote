<?php
session_start();
require_once("lib/autoload.php");
require_once("lib/Thrift.php");
require_once("lib/transport/TTransport.php");
require_once("lib/transport/THttpClient.php");
require_once("lib/protocol/TProtocol.php");
require_once("lib/protocol/TBinaryProtocol.php");
require_once("lib/packages/Errors/Errors_types.php");
require_once("lib/packages/Types/Types_types.php");
require_once("lib/packages/UserStore/UserStore.php");
require_once("lib/packages/UserStore/UserStore_constants.php");
require_once("lib/packages/NoteStore/NoteStore.php");
require_once("lib/packages/Limits/Limits_constants.php");
function Save()
{
    $username = trim($_SESSION['user1']);
    $password = trim($_SESSION['pass1']);
    $consumerKey = "bleiken-4421";
    $consumerSecret = "6a143060486eaca8";

    $evernoteHost = "sandbox.evernote.com";
    $evernotePort = "443";
    $evernoteScheme = "https";
    $userStoreHttpClient =
      new THttpClient($evernoteHost, $evernotePort, "/edam/user", $evernoteScheme);
    $userStoreProtocol = new TBinaryProtocol($userStoreHttpClient);
    $userStore = new UserStoreClient($userStoreProtocol, $userStoreProtocol);
    $versionOK =
    $userStore->checkVersion("PHP EDAMTest",
			   $GLOBALS['UserStore_CONSTANTS']['EDAM_VERSION_MAJOR'],
			   $GLOBALS['UserStore_CONSTANTS']['EDAM_VERSION_MINOR']);
    if ($versionOK == 0) {
    exit(1);
}
// Authenticate the user
try {
  $authResult = $userStore->authenticate($username, $password, $consumerKey, $consumerSecret);
} catch (edam_error_EDAMUserException $e) {
  // See http://www.evernote.com/about/developer/api/ref/UserStore.html#Fn_UserStore_authenticate
  $parameter = $e->parameter;
  $errorCode = $e->errorCode;
  $errorText = edam_error_EDAMErrorCode::$__names[$errorCode];

  echo "Authentication failed (parameter: $parameter errorCode: $errorText)\n";

  if ($errorCode == $GLOBALS['edam_error_E_EDAMErrorCode']['INVALID_AUTH']) {
    if ($parameter == "consumerKey") {
      if ($consumerKey == "en-edamtest") {
        echo "You must replace \$consumerKey and \$consumerSecret with the values you received from Evernote.\n";
      } else {
        echo "Your consumer key was not accepted by $evernoteHost\n";
        echo "This sample client application requires a client API key. If you requested a web service API key, you must authenticate using OAuth as shown in sample/php/oauth\n";
      }
      echo "If you do not have an API Key from Evernote, you can request one from http://dev.evernote.com/documentation/cloud/\n";
    } else if ($parameter == "username") {
      echo "You must authenticate using a username and password from $evernoteHost\n";
      if ($evernoteHost != "www.evernote.com") {
        echo "Note that your production Evernote account will not work on $evernoteHost,\n" .
             "you must register for a separate test account at https://$evernoteHost/Registration.action\n";
      }
    } else if ($parameter == "password") {
      echo "The password that you entered is incorrect\n";
    }
  }

  echo "\n";
  exit(1);
}

$user = $authResult->user;
$authToken = $authResult->authenticationToken;

$parts = parse_url($authResult->noteStoreUrl);
if (!isset($parts['port'])) {
  if ($parts['scheme'] === 'https') {
    $parts['port'] = 443;
  } else {
    $parts['port'] = 80;
  }
}
$noteStoreHttpClient = 
  new THttpClient($parts['host'], $parts['port'], $parts['path'], $parts['scheme']);
$noteStoreProtocol = new TBinaryProtocol($noteStoreHttpClient);
$noteStore = new NoteStoreClient($noteStoreProtocol, $noteStoreProtocol);


$filename = trim($_POST['title']);


$note = new edam_type_Note();
$note->title = trim($_POST['title']);
$note->content =
  '<?xml version="1.0" encoding="UTF-8"?>' .
  '<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">' .
  '<en-note>' .
  strip_tags($_POST['input'], '<p>').
  '</en-note>';

// When note titles are user-generated, it's important to validate them
$len = strlen($note->title);
$min = $GLOBALS['Limits_CONSTANTS']['EDAM_NOTE_TITLE_LEN_MIN'];
$max = $GLOBALS['Limits_CONSTANTS']['EDAM_NOTE_TITLE_LEN_MAX'];
$pattern = '#' . $GLOBALS['Limits_CONSTANTS']['EDAM_NOTE_TITLE_REGEX'] . '#'; // Add PCRE delimiters
if ($len < $min || $len > $max || !preg_match($pattern, $note->title)) {
  print "\nInvalid note title: " . $note->title . '\n\n';
  exit(1);
}

$createdNote = $noteStore->createNote($authToken, $note);

}

if(isset($_POST['doSave']) && $_POST['doSave']){
  Save();
}

function Login()
{

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $consumerKey = "bleiken-4421";
    $consumerSecret = "6a143060486eaca8";

    $evernoteHost = "sandbox.evernote.com";
    $evernotePort = "443";
    $evernoteScheme = "https";
    $userStoreHttpClient =
      new THttpClient($evernoteHost, $evernotePort, "/edam/user", $evernoteScheme);
    $userStoreProtocol = new TBinaryProtocol($userStoreHttpClient);
    $userStore = new UserStoreClient($userStoreProtocol, $userStoreProtocol);
    $versionOK =
    $userStore->checkVersion("PHP EDAMTest",
			   $GLOBALS['UserStore_CONSTANTS']['EDAM_VERSION_MAJOR'],
			   $GLOBALS['UserStore_CONSTANTS']['EDAM_VERSION_MINOR']);
    if ($versionOK == 0) {
    exit(1);
}
// Authenticate the user
try {
  $authResult = $userStore->authenticate($username, $password, $consumerKey, $consumerSecret);
} catch (edam_error_EDAMUserException $e) {
  // See http://www.evernote.com/about/developer/api/ref/UserStore.html#Fn_UserStore_authenticate
  $parameter = $e->parameter;
  $errorCode = $e->errorCode;
  $errorText = edam_error_EDAMErrorCode::$__names[$errorCode];

  echo "Authentication failed (parameter: $parameter errorCode: $errorText)\n";

  if ($errorCode == $GLOBALS['edam_error_E_EDAMErrorCode']['INVALID_AUTH']) {
    if ($parameter == "consumerKey") {
      if ($consumerKey == "en-edamtest") {
        echo "You must replace \$consumerKey and \$consumerSecret with the values you received from Evernote.\n";
      } else {
        echo "Your consumer key was not accepted by $evernoteHost\n";
        echo "This sample client application requires a client API key. If you requested a web service API key, you must authenticate using OAuth as shown in sample/php/oauth\n";
      }
      echo "If you do not have an API Key from Evernote, you can request one from http://dev.evernote.com/documentation/cloud/\n";
    } else if ($parameter == "username") {
      echo "You must authenticate using a username and password from $evernoteHost\n";
      if ($evernoteHost != "www.evernote.com") {
        echo "Note that your production Evernote account will not work on $evernoteHost,\n" .
             "you must register for a separate test account at https://$evernoteHost/Registration.action\n";
      }
    } else if ($parameter == "password") {
      echo "The password that you entered is incorrect\n";
    }
  }

  echo "\n";
  exit(1);
}

$user = $authResult->user;
$authToken = $authResult->authenticationToken;
$_SESSION['loggedin'] = true;


}
?>
