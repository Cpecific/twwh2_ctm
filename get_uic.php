<?php

header('Content-type: application/json; charset=UTF-8');

include 'get_dir_data.php';

if (
!isset($_POST['KEY']) || !is_string($_POST['KEY']) || !isset($DIR_DATA[ $_POST['KEY'] ]) ||
!isset($_POST['FILE']) || !is_string($_POST['FILE'])
){
	throw new Exception('data');
}
$key = $_POST['KEY'];
$file = $_POST['FILE'];
$arr = $DIR_DATA[ $key ];
if (!isset($arr['FILES'][ $file ])){
	throw new Exception('data');
}

$h = fopen($arr['DIR'] . $file, 'r');
if (!$h){ throw new Exception('FILE'); }

include 'class.php';

$uic = new UIC();
$uic->read($h);
fclose($h);

echo json_encode($uic->dumpJS()); // , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE