<?php
echo '<pre style="word-break: break-all; white-space: pre-wrap;">';

$all = include('get_dir_data.php');
include 'class.php';


$stime = microtime(true);

$data_all = array();
$gr_versions = array();
$gr_im = array();
$grouped = array();
$grouped['ok'] = array();
$grouped['error'] = array();
$grouped_2 = array();
$gr_id0 = array();
$gr_t = array();

$processed = 0;

$extract = array();
function extractData($uic, $level = 0){
	// if ($uic instanceof UIC){
		$GLOBALS['extract'][] = str_repeat("•\t", $level) . $uic->name;
	// }
	// if ($uic instanceof UIC_Template){
		// foreach ($uic->template as $child){
			// $GLOBALS['extract'][] = str_repeat("•\t", $level + 1) . $child->name;
		// }
	// }
	foreach ($uic->child as $child){
		extractData($child, $level + 1);
	}
}

foreach ($DIR_DATA as $dir_key => $arr){
	if (in_array($dir_key, array('export'))){ continue; }
	// if (!in_array($dir_key, array('tech_trees'))){ continue; }
	
	$dir = $arr['DIR'];
	
	foreach ($arr['FILES'] as $file => $v){
		$path = $dir . $file;
		
		// if ($file !== 'CTM_mortuary_cult'){ continue; }
		// if ($file !== 'effect_bar'){ continue; }
		
		// var_dump($path);
		if ($h){ fclose($h); }
		$h = fopen($path, 'r');
		if (!$h){ continue; }
		
		$version = fread($h, 10);
		$v = (int)substr($version, 7);
		fseek($h, -10, SEEK_CUR);
		
		// if ($v < 70 || $v >= 80){ continue; }
		// if ($v < 80 || $v >= 90){ continue; }
		// if ($v < 90 || $v >= 100){ continue; }
		// if ($v < 100 || $v >= 110){ continue; }
		// if ($v < 110 || $v >= 120){ continue; }
		// if ($v !== 119){ continue; }
		// if ($v < 70 || $v >= 100){ continue; }
		
		++$processed;
		// if ($processed < 100){ continue; }
		// if ($processed > 100){ break; }
		
		$has = array(
			'bgs' => false,
			'funcs' => false,
			'list' => false,
			'hlist' => false,
			'rlist' => false,
			'table' => false
		);
		$type = 'ok';
		try{
			$uic = new UIC();
			$uic->read($h);
		} catch (Exception $e){
			$type = 'error';
			// continue;
			$grouped[ $type ][ $path ] = $uic->debug();
			foreach ($e->getTrace() as $trace){
				echo '<b>', $trace['file'], '</b> on line <b>', $trace['line'], "</b>\r\n";
			}
			break 2;
		}
		
		// if ($type === 'error'){ continue; }
		// if ($type === 'ok'){ continue; }
		
		// extractData($uic);
		
		// if ($has['bgs'] && !$has['funcs']){
		if (1 || $has['table']){
			// $grouped[ $type ][ $path ] = null;
			// $grouped[ $type ][ $path ] = $uic->debug();
		}
	}
}

// $extract = array_unique($extract);
// var_dump($extract);return;

// var_dump(array_keys($grouped['ok']));
var_dump($grouped);





