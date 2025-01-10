<?php
// api/functions.php
define("AUTH_JSON_FILE", 'auth.json');
define("CODE_JSON_FILE", 'code.json');
define("USER_JSON_FILE", 'user.json');

// 仮のユーザーデータ
/*
$usersData = [
  ['id' => 1, 'name' => 'John Doe'],
  ['id' => 2, 'name' => 'Jane Doe'],
];
*/
$usersData = array();

function load_users(){
  global $usersData;

  $fpath = make_filepath(USER_JSON_FILE);
  [$usersData,$fpath, $state] = file_read($fpath);
  $usersData = json_decode($usersData, true);
}
function store_users(){
  global $usersData;

  $json_str = json_encode($usersData, JSON_PRETTY_PRINT);
  $fpath = make_filepath(USER_JSON_FILE);
  file_write($fpath, $json_str);
}
function get_filtered_users($username, $password){
  global $usersData;

  $hash = password_hash($password, PASSWORD_DEFAULT);
  $filteredUsers = array_filter($usersData, function($user) use ($username, $hash) {
    if ($user['name'] === $username && password_verify($user['password'], $hash ){
      return true;
    }
  });
  return $filteredUsers;
}

function signup($username, $password){
  global $usersData;

  // ユーザーデータの読み込み
  load_users();

  $filteredUsers = get_filtered_users($username, $password);
  if (count($filteredUsers) == 0){
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
  else{
    return false; // すでに登録されているユーザー名
  }
}

function unsubscribe(){
  global $usersData;

  // ユーザーデータの読み込み
  load_users();

  $filteredUsers = get_filtered_users($_SESSION['user_name'], $_SESSION['user_password']);
  if (count($filteredUsers) > 0){
    $user = $filteredUsers[0];
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

  // ユーザーデータの読み込み
  load_users();

  $ret = false;
  $filteredUsers = get_filtered_users($username, $password);
  if (count($filteredUsers) > 0){
    $_SESSION['user_id'] = $filteredUsers[0]['id'];
    $_SESSION['user_name'] = $filteredUsers[0]['username'];
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
  unset($_SESSION['id']);
  unset($_SESSION['username']);
}
function make_username($name){
  return ["name" => $name];
}

// ユーザー一覧を取得する関数
function getUsers() {
  global $usersData;
  return $usersData;
}

// 新規ユーザーを作成する関数
function createUser($data) {
  global $usersData;
  // 簡易的なバリデーション
  if (empty($data['name'])) {
    return false;
  }
  if (empty($data['password'])) {
    return false;
  }
  $newUser = [
    'id' => count($usersData) + 1,
    'name' => $data['name'],
    'password' => $data['password'],
  ];
  $usersData[] = $newUser;
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


function file_write($fname, $content){
  $fpath = __DIR__ . "/" . $fname;
  $file = fopen($fpath, "w");
  if( $file !== false ){
    fwrite($file, $content);
    fclose($file);
  }
}

function isBlank($str){
  return( empty(trim($str)) );
}
?>

function hashString($string, $algorithm = 'sha256') {
  // ハッシュ値を生成
  $hash = hash($algorithm, $string);

  return $hash;
}
