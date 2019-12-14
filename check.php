<?php
// This file is used to check, whether dumpFile
// is dumping the same contents it was feed on

echo '<pre style="word-break: break-all; white-space: pre-wrap;">';

$all = include('get_dir_data.php');
include 'class.php';

$grouped = array();
$grouped['ok'] = array();
$grouped['error'] = array();
$grouped['error_reconstruct'] = array();

if (isset($DIR_DATA['templates'])){
	$rest_keys = array_combine(array_keys($DIR_DATA), array_fill(0, sizeof($DIR_DATA), null));
	unset($rest_keys['templates']);
	$DIR_DATA = array_merge(
		array(
			'templates' => $DIR_DATA['templates']
		),
		array_intersect_key($DIR_DATA, $rest_keys)
	);
}

foreach ($DIR_DATA as $dir_key => $arr){
	if (in_array($dir_key, array('export'))){ continue; }
	// if (!in_array($dir_key, array('templates'))){ continue; }
	
	$dir = $arr['DIR'];
	
	foreach ($arr['FILES'] as $file => $v){
		$path = $dir . $file;
		
		// if ($file !== 'CTM_mortuary_cult'){ continue; }
		// if ($file !== 'effect_bar'){ continue; }
		
		var_dump($path);
		if ($h){ fclose($h); }
		$h = fopen($path, 'r');
		if (!$h){ continue; }
		
		$version = fread($h, 10);
		$v = (int)substr($version, 7);
		// if ($v === 121){var_dump($v);}
		fseek($h, -10, SEEK_CUR);
		// if ($v < 70 || $v >= 80){ continue; }
		// if ($v < 80 || $v >= 90){ continue; }
		// if ($v < 90 || $v >= 100){ continue; }
		// if ($v < 100 || $v >= 110){ continue; }
		// if ($v < 110 || $v >= 120){ continue; }
		// if ($v !== 119){ continue; }
		// if ($v < 70 || $v >= 100){ continue; }
		
		// ++$processed;
		// if ($processed < 100){ continue; }
		// if ($processed > 100){ break 2; }
		
		try{
			$type = 'error';
			$uic = new UIC();
			$uic->read($h);
			$len = ftell($h);
			fseek($h, 0, SEEK_SET);
			$content = fread($h, $len);
			
			file_put_contents('_check.tmp', $uic->dumpFile());
			
			$type = 'error_reconstruct';
			
			fclose($h);
			$h = fopen('_check.tmp', 'r');
			fseek($h, 0, SEEK_END);
			$len = ftell($h);
			fseek($h, 0, SEEK_SET);
			$new = fread($h, $len);
			if ($content !== $new){
				$scon = strlen($content);
				$snew = strlen($new);
				$slen = min($scon, $snew);
				for ($i = 0; $i < $slen; ++$i){
					if ($content[ $i ] !== $new[ $i ]){
						break;
					}
				}
				var_dump($i);
				// var_dump(substr($content, $i - 40),
					// substr($new, $i - 40));
				throw new Exception('reconstruct');
			}
			
			$type = 'ok';
		} catch (Exception $e){
			// continue;
			$grouped[ $type ][ $path ] = $uic->debug();
			foreach ($e->getTrace() as $trace){
				echo '<b>', $trace['file'], '</b> on line <b>', $trace['line'], "</b>\r\n";
			}
			break 2;
		}
		
		// $grouped[ $type ][ $path ] = $uic->debug();
	}
}


@unlink('_check.tmp');
var_dump($grouped);










