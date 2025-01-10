<?php
// verify-token.php
require 'common/store.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Google\Auth\OAuth2;
use Dotenv\Dotenv; // Add this line to use Dotenv

header('Content-Type: application/json');

function getBaseDir()
{
  return __DIR__;
}
function getAbsolutePath($path)
{
  return getBaseDir() . '/' . $path;
}

function validateJWT($token, $CLIENT_ID, $jwks)
{ // 修正: 引数を追加
  try {
    // トークンのヘッダーからkidを取得
    $tokenParts = explode('.', $token);
    if (count($tokenParts) != 3) {
      throw new Exception('Invalid token format');
    }
    $header = json_decode(base64_decode($tokenParts[0]), true);
    if (!isset($header['kid'])) {
      throw new Exception('No kid found in token header');
    }
    $kid = $header['kid'];

    // 対応する公開鍵を見つける
    if (!isset($jwks['keys'])) {
      throw new Exception('No keys found in JWKS');
    }

    $key = null;
    foreach ($jwks['keys'] as $jwk) {
      if ($jwk['kid'] === $kid) {
        $key = JWK::parseKey($jwk);
        break;
      }
    }

    if (!$key) {
      throw new Exception('Public key not found for the given kid');
    }

    // JWTを検証
    $decoded = JWT::decode($token, $key);

    // トークンのクレームを検証
    if ($decoded->aud !== $CLIENT_ID) {
      throw new Exception('Invalid audience');
    }

    if ($decoded->iss !== 'https://accounts.google.com' && $decoded->iss !== 'accounts.google.com') {
      throw new Exception('Invalid issuer');
    }

    // 有効期限を確認
    $currentTime = time();
    if ($decoded->exp < $currentTime) {
      throw new Exception('Token has expired');
    }
    return [true, $decoded];
  } catch (Exception $e) {
    return [false, null];
  }
}


function getJWT()
{


  // リクエストボディを取得
  $input = file_get_contents('php://input');
  if ($input) {
    $path = getAbsolutePath('a.txt');
    $fileitem = new FileItem($path, "file");
    $fileitem->write($input);
  } else {
    $path = getAbsolutePath('b.txt');
    $fileitem = new FileItem($path, "file");
    $fileitem->write("empty input");
  }
  return $input;
}


  function puvlicKey($store, $jwtrelated, $jwt_content)
  {
    $jwks_backup = $content;
    $jwks = json_decode($content, true);

    try {
      $ret = validateJWT($content, $_ENV['GCP_CLIENT_ID'], $jwks); // 修正: 引数を追加
    } catch (Exception $e) {
      $ret = false;
    }

    $xmes = 1;
    if (!$ret) {
      [$jwt_fileitem, $content] = $store->getAndStoreJwtStoreDirectory($jwtrelated->jwt_dir_fileitem); // 修正: メソッド呼び出し
      if (strlen(trim($content)) == 0) {
        $jwks = json_decode($content, true);
        $ret = true;
      } else {
        $ret = false;
        $xmes = 2;
      }
    }
  }
  function output($ret, $jwks, $xmes){
    // header('Content-Type: application/json');

    if ($ret) {
      echo json_encode(['success' => true, 'payload 3  vt4' => $jwks]);
    } else {
      http_response_code(401);
      echo json_encode(['success vt3' => false, 'xmes vt4 4' => $xmes]);
    }
  }


  function getGooglePublicKeyx(){
    // Googleの公開鍵を取得
    $kind = "google_public_keys";

    $store = new Store();
    [$fileitem, $content, $jwtrelated] = $store->load($kind);
    // print_r($jwtrelated);

    $google_public_key_json = "";

    if ($jwtrelated->state) {
      echo "T \n";
      $google_public_key__json = $jwtrelated->getJwtContent();
    }
    else{
      $google_public_key__json = $store->getAndSaveGoolePublicKey($jwtrelated);
      // print_r($jwtrelated);
    }
    return  [$store, $jwtrelated, $google_public_key__json];
  }
  
  // Load .env file
  $dotenv = Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  // クライアントIDを取得
  $CLIENT_ID = $_ENV['GCP_CLIENT_ID']; // Use environment variable

  // from Google login
  $jwt_token = getJWT();

  // from Goolge public key
  [$store, $jwtrelated, $google_public_key_json] = getGooglePublicKeyx();

  $jwks = json_decode($google_public_key_json, true);

  [$ret, $decoded_jwt] = validateJWT($jwt_token, $CLIENT_ID, $jwks);
  if ($ret) {
    echo "Token is valid\n";
    echo "User ID: " . $decoded_jwt->sub() . "\n";
    echo "User Name: " . $decoded_jwt->name() . "\n";
  } else {
    echo "Token is invalid\n";
  }

// validateJWT($token, $CLIENT_ID, $jwks)
$xmes = "XMES";
output($ret, $jwks, $xmes)
?>