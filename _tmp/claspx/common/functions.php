<?php
// common/functions.php
const AUTH_JSON_FILE = 'auth.json';
const CODE_JSON_FILE =  'code.json';
const USER_JSON_FILE = 'user.json';
const JWT_FILE = 'jwt.txt';
const TOKEN_JSON_FILE = 'token.json';
const POST_JSON_FILE = 'post.json';
const INPUT_JSON_FILE = 'input.json';
const HEADER_B64_FILE = 'header_b64.json';
const PAYLOAD_B64_FILE = 'payload_b64.json';
const HEADER_FROM_B64_FILE = 'header_from_b64.json';
const PAYLOAD_FROM_B64_FILE = 'payload_from_b64.json';

const HEADER_DECODED_FILE = 'header_decoded.json';
const PAYLOAD_DECODED_FILE = 'payload_decoded.json';
const STORE_FILE = 'store.json';

const GPK_ROOT_DIR = 1;
const GPK_CACHE_DIR = 2;



$usersData = array();

function get_valid_index($array){
  foreach ($array as $key => $value){
    if( !isBlank($value) ){
      return $key;
    }
  }
  return -1;
}

function load_users(){
  global $usersData;

  $fpath = make_filepath(USER_JSON_FILE);
  [$users_data,$fpath,$state] = file_read($fpath);
  if( is_null($users_data) ){
    $users_data = array();
  }
  $users_data_obj = json_decode($users_data, true);
  if( !is_array($users_data_obj) ){
    $usersData = array();
  }
  else{
    $usersData = $users_data_obj;
  }
}
function store_users(){
  global $usersData;

  $json_str = json_encode($usersData, JSON_PRETTY_PRINT);
  $fpath = make_filepath(USER_JSON_FILE);
  file_write($fpath, $json_str);
}
function get_filtered_users($username, $password = null){
  global $usersData;

  if( !is_array($usersData) ){
    $usersData = array();
  }
  if( is_null($password) ){
    $filteredUsers = array_filter($usersData, function($user) use ($username)  {
      return $user['name'] === $username;
    } );
  }
  else{
    $filteredUsers = array_filter($usersData, function($user) use ($username, $password) {
        return $user['name'] === $username && password_verify($password, $user['password']);
      }
    );
  }
  return $filteredUsers;
}

function signup($username, $password){
  global $usersData;

  $filteredUsers = get_filtered_users($username);
  if (count($filteredUsers) == 0){
    $newUser = createUser($username, $password);
    store_users();
    return true;
  }
  else{
    $index = get_valid_index($filteredUsers);
    $user = $filteredUsers[$index];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    // echo "password: $password<br>";
    // echo $user['password'];
    if ( password_verify($password, $user['password']) ){
      return true; // すでに登録されているユーザー名
    }
    else{
      return false;
    }
  }
}

function unsubscribe(){
  global $usersData;

  $filteredUsers = get_filtered_users($_SESSION['user_name'], $_SESSION['user_password']);
  if (count($filteredUsers) > 0){
    $index = get_valid_index($filteredUsers);
    $user = $filteredUsers[$index];
    $user_id = $user['id'];
    deleteUser($user_id);
    logout();
    store_users();
    return true;
  }
  else{
    return false;
  }
}

function login(string $username, string $password){
  global $usersData;

  $ret = false;
  $filteredUsers = get_filtered_users($username, $password);
  if (count($filteredUsers) > 0){
    session_regenerate_id();
    $index = get_valid_index($filteredUsers);
    $users = $filteredUsers[$index];
    $_SESSION['user_id'] = $users['id'];
    $_SESSION['user_name'] = $users['name'];
    $ret = true;
  };
  return $ret;
}
function logout(){
  $_SESSION = array();
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(),"", time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );

    session_destroy();
  }
  unset($_SESSION['user_id']);
  unset($_SESSION['user_name']);
}

function query_user( $username ){
  global $usersData;
  if( is_string($usersData) ){
    $usersData = array();
  }
  $filteredUsers = array_filter($usersData, function($user) use ($username) {
    return $user['name'] === $username;
  });
  return $filteredUsers;
}
function check_user_by_username($username){
  $filteredUsers = query_user($username);
  return count($filteredUsers) > 0;
}
function get_userid($username){
  $filteredUsers = query_user($username);
  if( $filteredUsers !== null && is_array($filteredUsers) && count($filteredUsers) > 0 ){
    $index = get_valid_index($filteredUsers);
    return $filteredUsers[$index]['id'];
  }
  else{
    return -1;
  }
}

function make_username_and_password_and_code($name, $password, $code){
  return ["name" => $name, "password" => $password, "code" => $code];
}
function make_code($code){
  return ["code" => $code];
}
// ユーザー一覧を設定する関数
function setUsers($data) {
  global $usersData;
  $usersData = $data;
}

// ユーザー一覧を取得する関数
function getUsers() {
  global $usersData;
  return $usersData;
}

// 新規ユーザーを作成する関数
function createUser($username, $password) {
  global $usersData;
  if( is_string($usersData) ){
    $usersData = array();
  }
  $newUser = [
    'id' => count($usersData) + 1,
    'name' => $username,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'code'=> null,
    'token'=> null,
    'recovery_token'=> null,
  ];
  $usersData[] = $newUser;
  store_users();
  return $newUser;
}

// 特定のユーザー情報を更新する関数
function updateUser($userId, $data) {
    global $usersData;
    foreach ($usersData as $key => $user) {
        if ($user['id'] == $userId) {
            // 更新するフィールドのみを更新
            if (isset($data['name'])) {
                $usersData[$key]['name'] = $data['name'];
            }
            // 他の更新可能なフィールドがあればここに追加...
            if (isset($data['name'])) {
              $usersData[$key]['password'] = $data['password'];
            }
            if (isset($data['code'])) {
              $usersData[$key]['code'] = $data['code'];
            }
          store_users();
            return $usersData[$key]; // 更新されたユーザー情報を返す
        }
    }
    return false; // ユーザーが見つからない場合はfalseを返す
}

// 特定のユーザーを削除する関数
function deleteUser($userId) {
    global $usersData;
    foreach ($usersData as $key => $user) {
        if ($user['id'] == $userId) {
            unset($usersData[$key]);
            // 配列のキーを詰め直す
            $usersData = array_values($usersData);
            return true;
        }
    }
    return false; // ユーザーが見つからない場合はfalseを返す
}

// 実際にはここでデータベースへの接続やデータ操作を行う
// function connectToDatabase() { ... }
// function fetchUsersFromDatabase() { ... }
// function insertUserIntoDatabase($data) { ... }

function make_filepath($fname, $dir = null){
  // return __DIR__ . "/../" . $fname;
  if( $dir !== null ) {
    $fpath = $dir  . '/' . $fname;
  }
  else{
    $fpath = $fname;
  }
  return $fpath;
}
function file_read($fname){
  $state = 10;
  $size = 0;
  $content = "";
  $fpath = make_filepath($fname);
  if( file_exists($fpath) ){
    $state = 20;
    $size = filesize($fpath);
    $file = fopen($fpath, "r");
    // $fsize = filesize($fpath);
    if($size > 0){
      $state = 30;
      $content = fread($file, 10);
      // print_r(["name" => "file_read", "size" => $size, "content" => $content]);
    }
    fclose($file);
  }
  return [$content, $fpath, $state, $size];
}

function file_write($fname, $content){
  $file = null;
  $fpath = make_filepath($fname);
  try{
    if( file_exists($fpath) ){
      unlink($fpath);
    }
    $file = fopen($fpath, "w");
    // echo "2 fpath: $fpath<br>";
    if( $file !== false){
      fwrite($file, $content);
      fclose($file);  
    }
  }
  catch (Exception $e) {
    // その他の例外がキャッチされた場合の処理
    echo "予期せぬエラーが発生しました。<br>";
    echo "エラーメッセージ: " . $e->getMessage() . "<br>";
  }
  if($file === null) {
    // echo "$file === false";
    return [false, $fpath];
  }

  return [true, $fpath];
}

function isBlank($str){
  if( is_null($str) ){
    return true;
  }
  if( is_string($str) ){
    return( empty(trim($str)) );
  }
  return false;
}

function getCurrentTimeString() {
  return date('YmdHis');
}

function throwExceptionExample($condition) {
  if ($condition) {
    throw new Exception("An error occurred because the condition was true.");
  }
}

function convertToAbsolutePath($absolutePath) {
  $absolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $absolutePath); // パスの区切り文字を統一
  $absolutePath = realpath($absolutePath) ?: $absolutePath; // realpathがfalseを返す場合はそのまま使用
  return $absolutePath;
}

function ensureDirectoryExists($path, $permissions = 0755) {
  if (file_exists($path)) {
    return true;
  }

  // 親ディレクトリを取得
  $parentDir = dirname($path);

  // 親ディレクトリが存在しない場合は再帰的に作成
  if (!file_exists($parentDir)) {
    ensureDirectoryExists($parentDir, $permissions);
  }

  // 現在のディレクトリを作成
  $ret = false;
  try{
    if( !file_exists($path) ){
      $ret = mkdir($path, $permissions);
    }
  }
  catch(Exception $e){
    echo "Failed to create directory: $path";
  }
  return $ret;
}
/**
 * 文字列が数字のみで構成されているかを判定する関数
 *
 * @param string $string 判定する文字列
 * @return bool 文字列が数字のみの場合は true、そうでない場合は false
 */
function isNumericString(string $string): bool {
  // 正規表現を使用して、文字列が数字のみで構成されているかを判定
  return preg_match('/^[0-9]+$/', $string) === 1;
}

/**
 * 文字列の前後から空白文字を削除する関数
 *
 * @param string $string 空白を削除したい文字列
 * @return string 空白を削除した後の文字列
 */
function removeWhitespaceFromEnds(string $string): string {
  return trim($content, " \t\n\r\0\x0B."); // トリミング処理を強化
}


?>

