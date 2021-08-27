<?php
require_once('app/router.php');
require_once('app/controller.php');

// ロジック開始
$request_method = null;
$request_url = null;

$request_method = $_SERVER['REQUEST_METHOD'];
$request_url = $_SERVER['REQUEST_URI'];

// 値がとれなかったら不正なのでexit
if (empty($request_method) || empty($request_url)) {
  exit;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Accept, X-Requested-With, Origin, Content-Type, X-Powered-By');
header("Content-Type: application/json;");

$router = new Router($request_method, $request_url);
$controller = new Controller();

$router->resolve('GET', '/sentences', fn () => $controller->getSentenceList());
$router->resolve('POST', '/sentences', fn () => $controller->storeSentence());
// TODO: GETでパラメータが渡せなかったのでいったんPOST
$router->resolve('POST', '/words', fn () => $controller->getWordList());
$router->resolve('POST', '/words/pos', fn () => $controller->getPosList());

print json_encode(array('status' => 404, 'data' => [], 'msg' => 'not found.'), JSON_PRETTY_PRINT);
