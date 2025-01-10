<?php
// verify-token.php
require 'common/store.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Google\Auth\OAuth2;
use Dotenv\Dotenv; // Add this line to use Dotenv

function getBaseDir(){
    return __DIR__;
}
function getAbsolutePath($path){
    return getBaseDir() . '/' . $path;
}

function validateJWT($token, $CLIENT_ID, $jwks) { // 修正: 引数を追加
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
    } catch (Exception $e) {
        return false;
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
if ($input) {
    $path = getAbsolutePath('a.txt');
    $fileitem = new FileItem($path, "file");
    $fileitem->write($input);
}
else{
    $path = getAbsolutePath('b.txt');
    $fileitem = new FileItem($path, "file");
    $fileitem->write("empty input");
}

if($input){
    try{
        $data = json_decode($input, true, 512);
    }
    catch (Exception $e) {
        $path = getAbsolutePath('c.txt');
        $fileitem = new FileItem($path, "file");
        $fileitem->write($e->getMessage());
        http_response_code(400);
        echo json_encode(['success' => false, 'error 1 vt3' => $e->__toString()]);
        exit;
    }
}

// トークンが存在するか確認
if (!isset($data['token'])) { // 修正: コメントアウトを解除
    http_response_code(400);
    echo json_encode(['success' => false, 'error 2' => 'No token provided', 'error2 2 vt3' => array_keys($data)]);
    exit;
}

$token = $data['token'];

// Googleの公開鍵を取得
$kind = "google_public_keys";

$store = new Store();
[$fileitem, $content, $jwtrelated] = $store->load($kind);
$jwks_backup = $content;
$jwks = json_decode($content, true);

try {
    $ret = validateJWT($token, $CLIENT_ID, $jwks); // 修正: 引数を追加
} catch (Exception $e) {
    $ret = false;
}

$xmes = 1;
if (!$ret) {
    [$jwt_fileitem, $content] = $store->getAndStoreJwtStoreDirectory($jwtrelated->jwt_dir_fileitem); // 修正: メソッド呼び出し
    if (strlen(trim($content)) == 0) {
        $jwks = json_decode($jwks_backup, true);
        $jwks = json_decode($content, true);
        $ret = true;
    } else {
        $ret = false;
        $xmes = 2;
    }
}

if ($ret) {
    echo json_encode(['success' => true, 'payload 3 vt3' => $jwks]);
} else {
    http_response_code(401);
    echo json_encode(['success vt3' => false, 'xmes vt3 4' => $xmes]);
}
?>
