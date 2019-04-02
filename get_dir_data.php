<?php

$DIR_DATA = array(
	'battle' => array(
		'NAME' => 'Battle UI',
		'FOLDER' => 'ui/battle ui/',
		'FILES' => array()
	),
	'campaign' => array(
		'NAME' => 'Campaign UI',
		'FOLDER' => 'ui/campaign ui/',
		'FILES' => array()
	),
	'common' => array(
		'NAME' => 'Common UI',
		'FOLDER' => 'ui/common ui/',
		'FILES' => array()
	),
	'frontend' => array(
		'NAME' => 'Frontend UI',
		'FOLDER' => 'ui/frontend ui/',
		'FILES' => array()
	),
	'loading' => array(
		'NAME' => 'Loading UI',
		'FOLDER' => 'ui/loading_ui/',
		'FILES' => array()
	),
	'templates' => array(
		'NAME' => 'Templates',
		'FOLDER' => 'ui/templates/',
		'FILES' => array()
	),
	'export' => array(
		'NAME' => 'Export',
		'DIR' => __DIR__ .'/export/',
		'FILES' => array()
	)
);
unset($a);
foreach ($DIR_DATA as &$a){
	if (!isset($a['DIR'])){
		$a['DIR'] = __DIR__ .'/game/'. $a['FOLDER'];
	}
	else{
		$b = explode('/', $a['DIR']);
		if (empty($b[ sizeof($b) - 1 ])){ $b = $b[ sizeof($b) - 2 ]; }
		else{ $b = $b[ sizeof($b) - 1 ]; }
		$a['FOLDER'] = $b;
	}
}
unset($a);

$all = array();

unset($arr);
foreach (
	$DIR_DATA as $dir_key => &$arr
){
	$dir = $arr['DIR'];
	foreach (scandir($dir) as $file){
		if ($file === '.' || $file === '..' || is_dir($dir . $file) || strpos($file, '.') !== false){ continue; }
		
		$path = $dir . $file;
		
		$h = fopen($path, 'r');
		if (!$h){ continue; }
		
		$version = fread($h, 10);
		$v = (int)substr($version, 7);
		if ($v < 70 || $v >= 120){
			var_dump($v, $arr['FOLDER'], $file);
			continue;
		}
		
		$arr['FILES'][ $file ] = $v;
		$all[] = array($path, $file, $v);
		
		fclose($h);
	}
}
unset($arr);

return $all;