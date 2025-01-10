<?php
// api/index.php
// 共通関数などの読み込み
require_once './common/functions.php';


// リクエストメソッドの取得
$method = $_SERVER['REQUEST_METHOD'];

// リクエストパスの取得 (例: /api/users -> users)
$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$cmd = array_shift($path); // 先頭を削除

// レスポンスヘッダーの設定
header("Content-Type: application/json");

handleLoginRequest($method);

// handleCodeRequest($method);
// Loginソースへのリクエストを処理する関数
function handleLoginRequest($method) {
  $code = -1;
  switch ($method) {
    case 'GET':
      if( isset($_GET['code']) ){
        $code = $_GET['code'];
      }
      http_response_code(201); // Created
      echo json_encode( array('L-get | code' => $code) );
      break;
    case 'POST':
      if( isset($_POST['code']) ){
        $code = $_POST['code'];
      }
      if ($code) {
        http_response_code(201); // Created
        $fpath = __DIR__ . '/' . CODE_JSON_FILE;
        // add_file($fpath, "code", $code);
        $json_str = "QWERTY";
        $ofile = fopen($fpath, "w");
        fwrite($ofile, $json_str);
        fclose($ofile);

        echo json_encode( array( "L-post | code" => $code ) );
      } else {
        http_response_code(400); // Bad Request
        echo json_encode( "L-POST | ABC" );
      }
      break;
    default:
      http_response_code(405); // Method Not Allowed
      echo json_encode(['message' => 'L-default | Method not allowed']);
      break;
     //
  }
  // http_response_code(201); // Created
  // echo json_encode( array('Login get_code' => $code) );
}
?>

