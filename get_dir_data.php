<?php

// $GAME = 'attila';
// $GAME = 'rome_2';
// $GAME = 'three_kingdoms';
// $GAME = 'thrones_of_britannia';
// $GAME = 'troy';
// $GAME = 'warhammer';
$GAME = 'warhammer_2';
$dir = 'ui';
// $dir = 'ui-'. $GAME;

$DIR_DATA = array(
	'battle' => array(
		'NAME' => 'Battle UI',
		'FOLDER' => $dir. '/battle ui/',
		'FILES' => array()
	),
	'campaign' => array(
		'NAME' => 'Campaign UI',
		'FOLDER' => $dir. '/campaign ui/',
		'FILES' => array()
	),
	'common' => array(
		'NAME' => 'Common UI',
		'FOLDER' => $dir. '/common ui/',
		'FILES' => array()
	),
	'frontend' => array(
		'NAME' => 'Frontend UI',
		'FOLDER' => $dir. '/frontend ui/',
		'FILES' => array()
	),
	'historical_battles' => array(
		'NAME' => 'Historical Battles',
		'FOLDER' => $dir. '/historical_battles/',
		'FILES' => array()
	),
	'loading' => array(
		'NAME' => 'Loading UI',
		'FOLDER' => $dir. '/loading_ui/',
		'FILES' => array()
	),
	'lcd' => array(
		'NAME' => 'LCD',
		'FOLDER' => $dir. '/lcd/',
		'FILES' => array()
	),
	'templates' => array(
		'NAME' => 'Templates',
		'FOLDER' => $dir. '/templates/',
		'FILES' => array()
	),
	'tech_trees' => array(
		'NAME' => 'Tech Trees',
		'FOLDER' => $dir. '/tech_trees/',
		'FILES' => array()
	)
);
$dirs = null;
if (!isset($GAME)){ $GAME = null; }
switch ($GAME){
case 'attila':
case 'rome_2':
	$dirs = array('battle', 'campaign', 'common', 'frontend', 'lcd', 'loading');
	break;
case 'troy':
case 'warhammer':
case 'warhammer_2':
	$dirs = array('battle', 'campaign', 'common', 'frontend', 'loading', 'templates');
	break;
case 'thrones_of_britannia':
	$dirs = array('battle', 'campaign', 'common', 'frontend', 'loading');
	break;
case 'three_kingdoms':
	$dirs = array('battle', 'campaign', 'common', 'frontend', 'historical_battles', 'loading', 'templates', 'tech_trees');
	break;
}
if ($dirs !== null){
	$unset = array_diff(array_keys($DIR_DATA), $dirs);
	foreach ($unset as $key){
		unset($DIR_DATA[ $key ]);
	}
}
$DIR_DATA['export'] = array(
	'NAME' => 'Export',
	'DIR' => __DIR__ .'/export/',
	'FILES' => array()
);
// $DIR_DATA['export_CSQ'] = array(
	// 'NAME' => 'Export CSQ',
	// 'DIR' => __DIR__ .'/export_CSQ/',
	// 'FILES' => array()
// );

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
foreach ($DIR_DATA as $dir_key => &$arr){
	$dir = $arr['DIR'];
	foreach (scandir($dir) as $file){
		if ($file === '.' || $file === '..' || is_dir($dir . $file) || strpos($file, '.') !== false){ continue; }
		
		$path = $dir . $file;
		
		$h = fopen($path, 'r');
		if (!$h){ continue; }
		
		$version = fread($h, 10);
		if (!feof($h)){
			$v = (int)substr($version, 7);
			if ($v < 70 || $v > 133){
				var_dump('Unsupported Version'. $v.': '. $arr['FOLDER'] . $file);
				continue;
			}
			
			$arr['FILES'][ $file ] = $v;
			$all[] = array($path, $file, $v);
		}
		
		fclose($h);
		$h = null;
	}
}
unset($arr);

return $all;