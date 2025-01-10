<?php
// api/index.php
// 共通関数などの読み込み
require_once 'functions.php';


// リクエストメソッドの取得
$method = $_SERVER['REQUEST_METHOD'];

// リクエストパスの取得 (例: /api/users -> users)
$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
array_shift($path); // 'allapi5'を削除

// レスポンスヘッダーの設定
header("Content-Type: application/json");

// リクエストパスに応じた処理
/*
switch ($path[0]) {
  case 'code':
    handleCodeRequest($method);
    break;
  case 'users':
    handleUsersRequest($method);
    break;
  case 'products':
    // 他のリソースへの処理を追加...
    break;
  default:
    http_response_code(404);
    echo json_encode(['message' => 'Resource not found']);
    break;
}
*/
handleCodeRequest($method);
// codeソースへのリクエストを処理する関数
function handleCodeRequest($method) {
  $code = -1;
  switch ($method) {
    case 'GET':
      if( isset($_GET['code']) ){
        $code = $_GET['code'];
      }
      http_response_code(201); // Created
      echo json_encode( array('get_code' => $code) );
      break;
    case 'POST':
      if( isset($_POST['code']) ){
        $code = $_POST['code'];
      }
      if ($code) {
        http_response_code(201); // Created
        add_file(CODE_JAON_FILE, "code", $code);
        echo json_encode( array( "code" => $code ) );
      } else {
        http_response_code(400); // Bad Request
        echo json_encode( "ABC" );
      }
      break;
    default:
      http_response_code(405); // Method Not Allowed
      echo json_encode(['message' => 'Method not allowed']);
      break;
     //
  }
}

// usersリソースへのリクエストを処理する関数
function handleUsersRequest($method) {
  switch ($method) {
    case 'GET':
      // ユーザー一覧を取得
      $users = getUsers();
      echo json_encode($users);
      break;
    case 'POST':
      // 新規ユーザーを作成
      $data = json_decode(file_get_contents('php://input'), true);
      $newUser = createUser($data);
      if ($newUser) {
        http_response_code(201); // Created
        echo json_encode($newUser);
      } else {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Failed to create user']);
      }
      break;
    case 'PUT':
        // 特定のユーザー情報を更新
        // 特定のユーザーIDをリクエストURLから取得。
        // 例： /api/users/1 -> $userId = 1;
        $userId = isset($path[1]) ? intval($path[1]) : null;
        if ($userId === null) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'User ID is required for update']);
            break;
        }

        // リクエストボディから更新データを取得
        $data = json_decode(file_get_contents('php://input'), true);

        // ユーザー情報を更新する処理
        $updatedUser = updateUser($userId, $data);
        if ($updatedUser) {
            echo json_encode($updatedUser);
        } else {
            http_response_code(400); // Bad Request or 404 Not Found depending on your logic
            echo json_encode(['message' => 'Failed to update user or user not found']);
        }
        break;
    case 'DELETE':
        // 特定のユーザーを削除
        // 例： /api/users/1 -> $userId = 1;
        $userId = isset($path[1]) ? intval($path[1]) : null;

        if ($userId === null) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'User ID is required for deletion']);
            break;
        }

        // ユーザーを削除する処理
        $success = deleteUser($userId);
        if ($success) {
            http_response_code(204); // No Content (削除が成功した場合、通常はコンテンツを返さない)
        } else {
            http_response_code(400); // Bad Request or 404 Not Found depending on your logic
            echo json_encode(['message' => 'Failed to delete user or user not found']);
        }
        break;
    default:
      http_response_code(405); // Method Not Allowed
      echo json_encode(['message' => 'Method not allowed']);
      break;
  }
}
?>

