<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// api/index.php
// 共通関数などの読み込み
require_once 'common/functions.php';
define("DEFAULT_VALUE", -1);

load_users();

// セッションを開始
session_start();

function get_google_public_key_list(){
  $url = 'https://www.googleapis.com/oauth2/v3/certs'; // 取得したいURLを指定
  $content = file_get_contents($url); 
  return json_decode($content, true);
}
$google_public_key_list = get_google_public_key_list();

function get_google_public_key($kid){
  global $google_public_key_list;
  $content = $google_public_key_list[$kid];
  return $content;
}
echo $content; // 取得したコンテンツを表示function get_google_public_key(){

echo $content; // 取得したコンテンツを表示
  $google_public_key = get_google_public_key(); 
}
// リクエストパスの取得 (例: /api/users -> users)
// $path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
// $cmd = array_shift($path); // 先頭(claspx)を削除

$input_string = file_get_contents('php://input');
file_write(INPUT_JSON_FILE, $input_string);

$decoded_header="";
$decoded_payload="";
try {
  list($headersB64, $payloadB64, $sig) = explode('.', $input_string);
  file_write(HEADER_B64_FILE, $headersB64);
  $headers_from_b64 = base64_decode($headersB64);
  file_write(HEADER_FROM_B64_FILE, $headers_from_b64);
  $header  = json_decode($headers_from_b64, true);
  print_r($header);

  $header_array = (array) $header;
  $kid = $header_array['kid'];
  $alg = $header_array['alg'];

  $payload_from_b64 = base64_decode($payloadB64);
  file_write(PAYLOAD_FROM_B64_FILE, $payload_from_b64);
  $payload = json_decode($payload_from_b64, true);
  if( count($payload) == 0 ){
    $payload_array = explode(',', $payload_from_b64);
    array_pop($payload_array);
    $payload_from_b64_fix = implode(",", $payload_array) . "}";
    // print_r($payload_from_b64_fix);
    // echo "\n===\n";
    $payload = json_decode($payload_from_b64_fix, true);
  }

  $jwt = JWT::decode($input_string, new Key($kid, $alg), $headers = new stdClass());
  // print_r($jwt);
  // print_r($headers);
} catch (Exception $e) {
  header("Content-Type: application/json");
  http_response_code(400); // Bad Request
  echo json_encode( array('error vt' => 'vt Invalid JWT Token') );
  return;
  //
  exit;
}

// $decoded = JWT::decode($json_string,'', array(''));
// $header = $decoded.header;

// $idToken = json_decode($json_string, true);

// レスポンスヘッダーの設定
header("Content-Type: application/json");

// VerifyTokenソースへのリクエストを処理する関数

$post_str = json_encode($_POST);
file_write(POST_JSON_FILE, $post_str);
// $idToken_str = json_encode($idToken);
$header_str = json_encode($decoded_header);
file_write(TOKEN_JSON_FILE, $header_str);
// $jwt_src = json_encode($idToken, JSON_PRETTY_PRINT);
// file_write(JWT_FILE, $json_src);
$payload_str = json_encode($payload);
// file_write(JWT_FILE, $payload_str);
file_write(JWT_FILE, $payload_str);
// file_write(JWT_FILE, $payloadB64);

http_response_code(200); // JWT Token
// echo json_encode($token);
echo $payload_str;

//  http_response_code(201); // Created
//  echo json_encode( array('Redirect get_code' => $code) );


?>
