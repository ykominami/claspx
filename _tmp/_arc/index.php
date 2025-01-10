<?php
// api/index.php
// 共通関数などの読み込み
require_once './common/functions.php';
define("DEFAULT_VALUE", -1);

load_users();

// セッションを開始
session_start();

// リクエストメソッドの取得
$method = $_SERVER['REQUEST_METHOD'];

// リクエストパスの取得 (例: /api/users -> users)
$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$cmd = array_shift($path); // 先頭(claspx)を削除

// レスポンスヘッダーの設定
header("Content-Type: application/json");

// リクエストパスに応じた処理
switch ($path[0]) {
  case 'login':
    handleLoginRequest($method);
    break;
  case 'redirect':
    handleRedirectRequest($method);
    break;
  case 'signup':
    handleSignupRequest($method);
    break;
  case 'posturl':
    handlePosturlRequest($method);
    break;
  case 'verify-token':
    handleVerifyTokenRequest($method);
    break;
  case 'index.php':
    handleCodeRequest($method);
    break;
/*
  case 'products':
    // 他のリソースへの処理を追加...
    break;
*/
  default:
    http_response_code(404);
    // echo json_encode($_SERVER);
    // echo json_encode($_POST);
    echo json_encode(['message' => 'Resource not found|' . $cmd . "|" . $path[0]]);
    break;
}
function loginx($user, $password, $kind, $default_value = DEFAULT_VALUE){
  if ($user !== $default_value && $password !== $default_value) {
    $ret = login($user, $password);
    if( $ret === false ){
      http_response_code(400); // Bad Request
      echo json_encode( "11 L-". $kind . " | 16 Invalid username or password" );
    }
    else{
      http_response_code(201); // Created
      echo json_encode( ['user' => $user, '17 password' => $password] );
    }
  }
  else{
    http_response_code(400); // Bad Request
    echo json_encode( "L-". $kind . " | 2 Bad Request" . "|user=". $user. "|password=" . $password );
  }
}
/**
 * 配列の各要素を処理した値をもつ配列を返す
 *
 * @param array $array 処理対象の配列
 * @param callable $callback 各要素に適用するコールバック関数
 * @return array 処理された値をもつ配列
 */
function array_map_custom($keys, $array, $default_value = DEFAULT_VALUE) {
  $result = [];
  foreach ($keys as $key => $value) {
    if( isset($array[$value]) ){
      $result[$key] = $array[$value];
    }
    else{
      $result[$key] = $default_value;
    }
    // $result[$key] = $array[$value];
  }
  return $result;
}
function signupx($user, $password){
  if ($user !== -1 && $password !== -1) {
    $ret = signup($user, $password);
    if( $ret === false ){
      http_response_code(400); // Bad Request
      echo json_encode( "12 S-POST | 16 Invalid username or password|" . "user=" . $user . "|" . "password=" . $password );
    }
    else{
      http_response_code(200); // Created
      echo json_encode( ['user' => $user, '17 password' => $password] );
    }
  }
  else{
    http_response_code(400); // Bad Request
    echo json_encode( "S-POST | 2 Bad Request" . "|user=". $user. "|password=" . $password );
  }
}
// handleCodeRequest($method);
// Singupソースへのリクエストを処理する関数
function handleSignupRequest($method) {
  $code = DEFAULT_VALUE;
  $user = DEFAULT_VALUE;
  $password = DEFAULT_VALUE;
  switch ($method) {
    case 'GET':
      [$user, $password] = array_map_custom(['user', 'password'], $_GET, DEFAULT_VALUE);
      signupx($user, $password);
      break;
    case 'POST':
      [$user, $password] = array_map_custom(['user', 'password'], $_POST, DEFAULT_VALUE);
      signupx($user, $password);
      break;
    default:
      http_response_code(405); // Method Not Allowed
      echo json_encode(['message' => 'L-default | Method not allowed']);
      break;
  }
  // http_response_code(201); // Created
  // echo json_encode( array('Login get_code' => $code) );
}

// handleCodeRequest($method);
// Loginソースへのリクエストを処理する関数
function handleLoginRequest($method) {
  $code = DEFAULT_VALUE;
  $user = DEFAULT_VALUE;
  $password = DEFAULT_VALUE;

  switch ($method) {
    case 'GET':
      [$user, $password, $code] = array_map_custom(['user', 'password', 'code'], $_GET, DEFAULT_VALUE);
      loginx($user, $password, "GET", DEFAULT_VALUE);
      break;
    case 'POST':
      [$user, $password] = array_map_custom(['user', 'password'], $_POST, DEFAULT_VALUE);
      loginx($user, $password, "POST", DEFAULT_VALUE);

      break;
    default:
      http_response_code(405); // Method Not Allowed
      echo json_encode(['message' => 'L-default | Method not allowed']);
      break;
  }
}
// Redirectソースへのリクエストを処理する関数
function handleRedirectRequest($method) {
  $code = DEFAULT_VALUE;
  $user = DEFAULT_VALUE;
  $password = DEFAULT_VALUE;
  switch ($method) {
    case 'GET':
      [$code] = array_map_custom(['code'], $_GET, DEFAULT_VALUE);
      http_response_code(201); // Created
      echo json_encode( array('R-get | code' => $code) );
      break;
    case 'POST':
      [$user, $password, $code] = array_map_custom(['user', 'password', 'code'], $_POST, DEFAULT_VALUE);
      if ($code !== DEFAULT_VALUE && $user !== DEFAULT_VALUE && $password !== DEFAULT_VALUE) {
        if (check_user_by_username($user)) {
          $userId = get_userid($user);
          $data = make_code($code);
          updateUser($userId, $data);
        }
        else{
          $userData = make_username_and_password_and_code($user, $password, $code);
          $newUser = createUser($userData);
        }
        store_users();

        http_response_code(200); // OK
        echo json_encode( "R-POST | Update User's code" );
      }
      else{
        http_response_code(400); // Bad Request
        echo json_encode( "5 R-POST | Invalid username or password or code" );
      }
      break;
    default:
      http_response_code(405); // Method Not Allowed
      echo json_encode(['message' => 'R-default | Method not allowed']);
      break;
     //
  }
//  http_response_code(201); // Created
//  echo json_encode( array('Redirect get_code' => $code) );
}
// codeソースへのリクエストを処理する関数
function handleCodeRequest($method) {
  $code = DEFAULT_VALUE;
  $user = DEFAULT_VALUE;
  $password = DEFAULT_VALUE;
  switch ($method) {
    case 'GET':
      [$code] = array_map_custom(['code'], $_GET, DEFAULT_VALUE);
      http_response_code(201); // Created
      echo json_encode( array('L-get | code' => $code) );
      break;
    case 'POST':
      [$user, $password, $code] = array_map_custom(['user', 'password', 'code'], $_POST, DEFAULT_VALUE);
      if ($code) {
        http_response_code(201); // Created
        add_file(CODE_JSON_FILE, "code", $code);
      } else {
        http_response_code(400); // Bad Request
        echo json_encode( "6 L-POST | ABC" );
      }
      break;
    default:
      http_response_code(405); // Method Not Allowed
      echo json_encode(['message' => 'L-default | Method not allowed']);
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
        echo json_encode(['message' => '7 Failed to create user']);
      }
      break;
    case 'PUT':
        // 特定のユーザー情報を更新
        // 特定のユーザーIDをリクエストURLから取得。
        // 例： /api/users/1 -> $userId = 1;
        $userId = isset($path[1]) ? intval($path[1]) : null;
        if ($userId === null) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => '8 User ID is required for update']);
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
            echo json_encode(['message' => '9 Failed to update user or user not found']);
        }
        break;
    case 'DELETE':
        // 特定のユーザーを削除
        // 例： /api/users/1 -> $userId = 1;
        $userId = isset($path[1]) ? intval($path[1]) : null;

        if ($userId === null) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => '11 User ID is required for deletion']);
            break;
        }

        // ユーザーを削除する処理
        $success = deleteUser($userId);
        if ($success) {
            http_response_code(204); // No Content (削除が成功した場合、通常はコンテンツを返さない)
        } else {
            http_response_code(400); // Bad Request or 404 Not Found depending on your logic
            echo json_encode(['message' => '11 Failed to delete user or user not found']);
        }
        break;
    default:
      http_response_code(405); // Method Not Allowed
      echo json_encode(['message' => 'Method not allowed']);
      break;
  }
}
// Posturlリソースへのリクエストを処理する関数
function handlePosturlRequest($method){
  if( isset($_SESSION['user_name']) ){
    [$user, $password, $code] = array_map_custom(['user', 'password', 'code'], $_GET, DEFAULT_VALUE);
    $username = $_SESSION['user_name'];
    http_response_code(201); // Created
    echo json_encode($username);
  }
  else{
    http_response_code(400); // Bad Request
    $value = '100 $_SESSION[user_name] = ';
    echo json_encode(['message' => $value] );
    echo json_encode($_SESSION );
  }
}
// VerifyTokenソースへのリクエストを処理する関数
function handleVerifyTokenRequest($method) {
  switch ($method) {
    case 'GET':
      [$code] = array_map_custom(['code'], $_GET, DEFAULT_VALUE);
      http_response_code(201); // Created
      echo json_encode( array('R-get | code' => $code) );
      break;
    case 'POST':
      [$token] = array_map_custom(['token'], $_POST, DEFAULT_VALUE);
      $jwt = json_decode($token);

      $post_str = json_encode($_POST);
      file_write(POST_JSON_FILE, $post_str);
      file_write(TOKEN_JSON_FILE, $token);
      $jwt_src = json_encode($jwt);
      file_write(JWT_FILE, $jwt_src);

      http_response_code(300); // JWT Token
      // echo json_encode($token);
      echo json_encode($token);
      break;
    default:
      http_response_code(405); // Method Not Allowed
      echo json_encode(['message' => 'R-default | Method not allowed']);
      break;
     //
  }
//  http_response_code(201); // Created
//  echo json_encode( array('Redirect get_code' => $code) );
}

?>
