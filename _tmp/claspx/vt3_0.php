<?php
// verify-token.php
require 'common/store.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Google\Auth\OAuth2;
use Dotenv\Dotenv; // Add this line to use Dotenv
//
function validateJWT(string $key){
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
        return true;
        // 必要に応じてユーザー情報を処理
        echo json_encode(['success' => true, 'payload' => $decoded]);
    } catch (Exception $e) {
        return false;
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

}

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

// クライアントIDを設定
$CLIENT_ID = $_ENV['GCP_CLIENT_ID']; // Use environment variable

// リクエストボディを取得
$input = file_get_contents('php://input');
if (!$input) {
  $fileitem = new FileItem('a.txt', "file");
  $fileitem->write($input);
}
$data = json_decode($input, true);

// トークンが存在するか確認
if (!isset($data['token'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No token provided']);
    exit;
}

$token = $data['token'];

// Googleの公開鍵を取得
$kind = "google_public_keys";

$store = new Store();
[$fileitem, $content, $jwtrelated] = $store->load($kind);
$jwks_backup = $content;
$jwks = json_decode($content, true);

try{
    $ret = validateJWT($jwks);
}
catch(Exception $e){
    $ret = false;
}

$xmes = 1;
if(!$ret){
    [$jwt_fileitem, $content] = $store->getAndStoreJwtStoreDirectory();($jwtrelated->db_dir_item);
    if( strlen(trim($content)) == 0 ){
        $jwks = json_decode($jwks_backup, true);
        $jwks = json_decode($content, true);
        $ret = true;
    }
    else{
        $ret = false;
        $xmes = 2;
    }
}

if($ret){
    echo json_encode(['success' => true, 'payload' => $jwks]);
}
else{
    http_response_code(401);
    echo json_encode(['success' => false, 'xmes vt3' => $xmes]);
}
// echo json_encode(['success' => false, 'error' => $e->getMessage()]);
?>
