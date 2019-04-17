<?php

header('');

$mini = 2;

$exclude = array();
$exclude = array(
	'agent_subtypes' => array('data__'),
	'character_trait_levels' => array('data__'),
	'character_traits' => array('data__'),
	'effects' => array('data__'),
	'faction_political_parties_junctions' => array('data__'),
	'frontend_faction_leaders' => array('data__'),
	'frontend_factions' => array('data__'),
	'_political_parties_lords_defeated' => array('data__'),
	'political_parties' => array('data__'),
	'trait_categories' => array('data__'),
	'trait_level_effects' => array('data__'),
	'trait_to_antitraits' => array('data__'),
);

function has_object($a){
	if (is_array($a)){
		foreach ($a as $v){
			if (is_array($v)){
				return true;
			}
		}
	}
	return false;
}
function isAssoc(array $arr){
    if (array() === $arr){ return false; }
    return (array_keys($arr) !== range(0, sizeof($arr) - 1));
}
function my_print($a, $level, $mini){
	if ($a instanceof Ref){
		return $a;
	}
	if (is_string($a)){
		// return json_encode($a, 256);
		return '"'. str_replace(array("\\", "\"", "\r\n"), array("\\\\", "\\\"", "\\r\\n"), $a) .'"';
	}
	else if (!is_array($a)){
		return json_encode($a);
	}
	
	$new_level = $level ."\t";
	$str = '';
	$has_obj = has_object($a);
	
	if (isAssoc($a)){
		foreach ($a as $k => $v){
			if (!preg_match('#^[\w\d\_]+$#', $k)){ $k = '["'. addslashes($k) .'"]'; }
			$d = $k . ($mini ? '=' : ' = ') . my_print($v, $new_level, $mini);
			if ($mini === 2){
				$str .= ','. $d;
			}
			else if ($mini === 1){
				if ($has_obj){ $str .= ",\r\n". $d; }
				else{ $str .= ',' . $d; }
			}
			else{
				if ($has_obj){ $str .= ",\r\n". $level . $d; }
				else{ $str .= ', '. $d; }
			}
		}
	}
	else{
		foreach ($a as $v){
			$d = my_print($v, $new_level, $mini);
			if ($mini === 2){
				$str .= ','. $d;
			}
			else if ($mini === 1){
				if ($has_obj){ $str .= ",\r\n". $d; }
				else{ $str .= ','. $d; }
			}
			else{
				if ($has_obj){ $str .= ",\r\n". $level . $d; }
				else{ $str .= ', '. $d; }
			}
		}
	}
	if ($mini === 2){
		return '{'. mb_substr($str, 1) .'}';
	}
	else if ($mini === 1){
		if ($has_obj){ return '{'. mb_substr($str, 1) ."\r\n}"; }
		return '{'. mb_substr($str, 1) .'}';
	}
	if ($has_obj){ return '{'. mb_substr($str, 1) ."\r\n". mb_substr($level, 1) .'}'; }
	return '{'. mb_substr($str, 2) .'}';
}


echo '<pre style="word-break: break-all; white-space: pre-wrap;">';

$do_experimental = false;

// SCHEMA
if (1){
$tables_info = array(
	'agent_subtypes' => array(
		'DIR' => 'agent_subtypes_tables',
		'HEADER_SIZE' => 91,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'key',								'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'auto_generate',					'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'is_caster',						'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'small_icon',						'TYPE' => 'optstring',		'EXCLUDE' => true ),
			array( 'NAME' => 'associated_unit_override',		'TYPE' => 'optstring',		'EXCLUDE' => true ),
			array( 'NAME' => 'audio_voiceover_actor_group',		'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'show_in_ui',						'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'cap',								'TYPE' => 'int',			'EXCLUDE' => true ),
			array( 'NAME' => 'has_female_name',					'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'can_gain_xp',						'TYPE' => 'bool' ),
			array( 'NAME' => 'loyalty_is_applicable',			'TYPE' => 'bool',			'EXCLUDE' => true )
		)
	),
	'character_trait_levels' => array(
		'DIR' => 'character_trait_levels_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'key',					'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'level',				'TYPE' => 'int' ),
			array( 'NAME' => 'trait',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'threshold_points',	'TYPE' => 'int' )
		)
	),
	'character_traits' => array(
		'DIR' => 'character_traits_tables',
		'HEADER_SIZE' => 91,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'key',							'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'no_going_back_level',			'TYPE' => 'int' ),
			array( 'NAME' => 'hidden',						'TYPE' => 'bool' ),
			array( 'NAME' => 'precedence',					'TYPE' => 'int' ),
			array( 'NAME' => 'icon',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'ui',							'TYPE' => 'int' ),
			array( 'NAME' => 'pre_battle_speech_parameter',	'TYPE' => 'optstring',		'EXCLUDE' => true )
		)
	),
	// когда эффект ссылается на абилку, и мы ставим иконку картинки (unit_abilities)
	'effect_bonus_value_unit_ability_junctions' => array(
		'DIR' => 'effect_bonus_value_unit_ability_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'unit_ability',				'TYPE' => 'string_ascii' )
		)
	),
	'effects' => array(
		'DIR' => 'effects_tables',
		'HEADER_SIZE' => 91,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'effect',					'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'icon',					'TYPE' => 'optstring' ),
			array( 'NAME' => 'priority',				'TYPE' => 'int' ),
			array( 'NAME' => 'icon_negative',			'TYPE' => 'optstring' ),
			array( 'NAME' => 'category',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'is_positive_value_good',	'TYPE' => 'bool' )
		)
	),
	// тут мы получим faction для political_party
	'faction_political_parties_junctions' => array(
		'DIR' => 'faction_political_parties_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'faction_key',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'political_party_key',				'TYPE' => 'string_ascii' ),
		)
	),
	// здесь key = political_party_key
	'frontend_faction_leaders' => array(
		'DIR' => 'frontend_faction_leaders_tables',
		'HEADER_SIZE' => 91,
		'KEY' => array( 3 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'uniform',						'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'x_offset',					'TYPE' => 'int',			'EXCLUDE' => true ),
			array( 'NAME' => 'y_offset',					'TYPE' => 'int',			'EXCLUDE' => true ),
			array( 'NAME' => 'key',							'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'character_image',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'prelude_battle',				'TYPE' => 'optstring',		'EXCLUDE' => true ),
			array( 'NAME' => 'video',						'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'loading_screen_image',		'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'loading_screen_intro_video',	'TYPE' => 'optstring',		'EXCLUDE' => true ),
			array( 'NAME' => 'override_force_location_x',	'TYPE' => 'float',			'EXCLUDE' => true ),
			array( 'NAME' => 'override_force_location_y',	'TYPE' => 'float',			'EXCLUDE' => true ),
			array( 'NAME' => 'voiceover',					'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'show_full_intro_option',		'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'difficulty',					'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'political_party',				'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	// тут нам нужен sort_order для faction
	'frontend_factions' => array(
		'DIR' => 'frontend_factions_tables',
		'HEADER_SIZE' => 91,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'faction',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'video',						'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'sort_order',					'TYPE' => 'int' ),
		)
	),
	'_political_parties_lords_defeated' => array(
		'KEY' => array( 0 => 'unique', 1 => 'unique' )
	),
	'political_parties' => array(
		'DIR' => 'political_parties_tables',
		'HEADER_SIZE' => 91,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'key',							'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'playable',					'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'effect_bundle',				'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'initial_power',				'TYPE' => 'int',			'EXCLUDE' => true ),
			array( 'NAME' => 'campaign_key',				'TYPE' => 'string_ascii' )
		)
	),
	'trait_categories' => array(
		'DIR' => 'trait_categories_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'category',	'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'icon_path',	'TYPE' => 'optstring' )
		)
	),
	'trait_level_effects' => array(
		'DIR' => 'trait_level_effects_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'trait_level',		'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',			'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect_scope',	'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'value',			'TYPE' => 'float' )
		)
	),
	'trait_to_antitraits' => array(
		'DIR' => 'trait_to_antitraits_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'trait',		'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'antitrait',	'TYPE' => 'string_ascii' )
		)
	),
	'unit_abilities' => array(
		'DIR' => 'unit_abilities_tables',
		'HEADER_SIZE' => 91,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'key',							'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'supercedes_ability',			'TYPE' => 'optstring',		'EXCLUDE' => true ),
			array( 'NAME' => 'requires_effect_enabling',	'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'icon_name',					'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'overpower_option',			'TYPE' => 'optstring',		'EXCLUDE' => true ),
			array( 'NAME' => 'type',						'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'video',						'TYPE' => 'optstring',		'EXCLUDE' => true ),
			array( 'NAME' => 'uniqueness',					'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'is_unit_upgrade',				'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'is_hidden_in_ui',				'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'source_type',					'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	)
);
}

if (!isset($tables)){
	$tables = array();
}

foreach ($tables_info as $tbl_name => $tbl_data){
	$tbl_key = $tbl_data['KEY'];
	$tables[ $tbl_name ] = $_tbl_data = array();
	
	if (!isset($tbl_data['DIR'])){ continue; }
	
	$dir = __DIR__ .'/game/db/'. $tbl_data['DIR'] .'/';
	if (!is_dir($dir)){ continue; }
	
	foreach (scandir($dir, 1) as $file){
		if ($file === '.' || $file === '..' || is_dir($dir . $file)){ continue; }
		
		$_data = array();
		$path = realpath($dir . $file);
		// var_dump($tbl_name .' = '. $dir, $file);
		$h = fopen($dir . $file, 'r');
		if (!$h){ continue; }
		fread($h, $tbl_data['HEADER_SIZE'] - 4);
		$entries = unpack('V', fread($h, 4));
		$entries = $entries[1];
		
		for ($i = 0; $i < $entries; ++$i){
			$entry = array();
			
			foreach ($tbl_data['SCHEMA'] as $sch_idx => $sch_column){
				if ($sch_column['TYPE'] === 'string_ascii'){
					$length = unpack('v', fread($h, 2));
					$value = ($length[1] === 0 ? '' : fread($h, $length[1]));
				} else if ($sch_column['TYPE'] === 'int'){
					$value = unpack('l', fread($h, 4));
					$value = $value[1];
				} else if ($sch_column['TYPE'] === 'bool'){
					$value = unpack('C', fread($h, 1));
					$value = ($value[1] != 0);
				} else if ($sch_column['TYPE'] === 'float'){
					$value = unpack('f', fread($h, 4));
					$value = $value[1];
				} else if ($sch_column['TYPE'] === 'optstring'){
					$value = unpack('C', fread($h, 1));
					if ($value[1] == 0){
						$value = null;
					}
					else{
						$length = unpack('v', fread($h, 2));
						$value = ($length[1] === 0 ? '' : fread($h, $length[1]));
					}
				} else if ($sch_column['TYPE'] === 'byte'){
					$value = unpack('C', fread($h, 1));
					$value = $value[1];
				}
				if (isset($sch_column['EXCLUDE']) && $sch_column['EXCLUDE']){ continue; }
				
				// $entry[ $sch_column['NAME'] ] = $value;
				$entry[ $sch_idx ] = $value;
			}
			$_data[] = $entry;
		}
		
		fclose($h);
		$_tbl_data[ $file ] = $_data;
	}
	$tables[ $tbl_name ] = $_tbl_data;
}

if (!isset($tables['_political_parties_lords_defeated'])){
	$tables['_political_parties_lords_defeated'] = array();
}
// start_pos data
if (!isset($tables['_political_parties_lords_defeated']['data__'])){
$tables['_political_parties_lords_defeated']['data__'] = array(
#region _tmb_
	array(
		'wh2_dlc09_political_party_tmb_settra',
		array(
			'wh2_dlc09_political_party_tmb_settra' => 'wh2_dlc09_trait_defeated_settra'
		)
	),
	array(
		'wh2_dlc09_political_party_vor_tmb_settra',
		array(
			'wh2_dlc09_political_party_vor_tmb_settra' => 'wh2_dlc09_trait_defeated_settra'
		)
	),
	array(
		'wh2_dlc09_political_party_tmb_arkhan',
		array(
			'wh2_dlc09_political_party_tmb_arkhan' => 'wh2_dlc09_trait_defeated_arkhan'
		)
	),
	array(
		'wh2_dlc09_political_party_vor_tmb_arkhan',
		array(
			'wh2_dlc09_political_party_vor_tmb_arkhan' => 'wh2_dlc09_trait_defeated_arkhan'
		)
	),
	array(
		'wh2_dlc09_political_party_tmb_khalida',
		array(
			'wh2_dlc09_political_party_tmb_khalida' => 'wh2_dlc09_trait_defeated_khalida'
		)
	),
	array(
		'wh2_dlc09_political_party_vor_tmb_khalida',
		array(
			'wh2_dlc09_political_party_vor_tmb_khalida' => 'wh2_dlc09_trait_defeated_khalida'
		)
	),
	array(
		'wh2_dlc09_political_party_tmb_khatep',
		array(
			'wh2_dlc09_political_party_tmb_khatep' => 'wh2_dlc09_trait_defeated_khatep'
		)
	),
	array(
		'wh2_dlc09_political_party_vor_tmb_khatep',
		array(
			'wh2_dlc09_political_party_vor_tmb_khatep' => 'wh2_dlc09_trait_defeated_khatep'
		)
	),
#endregion
#region _def_
	array(
		'wh2_main_political_party_def_malekith',
		array(
			'wh2_main_political_party_def_malekith' => 'wh2_main_trait_defeated_malekith'
		)
	),
	array(
		'wh2_main_political_party_vor_def_malekith',
		array(
			'wh2_main_political_party_vor_def_malekith' => 'wh2_main_trait_defeated_malekith'
		)
	),
	array(
		'wh2_main_political_party_def_morathi',
		array(
			'wh2_main_political_party_def_morathi' => 'wh2_main_trait_defeated_morathi'
		)
	),
	array(
		'wh2_main_political_party_vor_def_morathi',
		array(
			'wh2_main_political_party_vor_def_morathi' => 'wh2_main_trait_defeated_morathi'
		)
	),
	array(
		'wh2_dlc10_political_party_def_hellebron',
		array(
			'wh2_dlc10_political_party_def_hellebron' => 'wh2_dlc10_trait_defeated_hellebron'
		)
	),
	array(
		'wh2_dlc10_political_party_vor_def_hellebron',
		array(
			'wh2_dlc10_political_party_vor_def_hellebron' => 'wh2_dlc10_trait_defeated_hellebron'
		)
	),
	array(
		'wh2_dlc11_political_party_def_lokhir_fellheart_ruler',
		array(
			'wh2_dlc11_political_party_def_lokhir_fellheart_ruler' => 'wh2_dlc11_trait_defeated_lokhir_fellheart'
		)
	),
	array(
		'wh2_dlc11_political_party_vor_def_lokhir_fellheart_ruler',
		array(
			'wh2_dlc11_political_party_vor_def_lokhir_fellheart_ruler' => 'wh2_dlc11_trait_defeated_lokhir_fellheart'
		)
	),
#endregion
#region _hef_
	array(
		'wh2_main_political_party_hef_tyrion',
		array(
			'wh2_main_political_party_hef_tyrion' => 'wh2_main_trait_defeated_tyrion'
		)
	),
	array(
		'wh2_main_political_party_vor_hef_tyrion',
		array(
			'wh2_main_political_party_vor_hef_tyrion' => 'wh2_main_trait_defeated_tyrion'
		)
	),
	array(
		'wh2_main_political_party_hef_teclis',
		array(
			'wh2_main_political_party_hef_teclis' => 'wh2_main_trait_defeated_teclis'
		)
	),
	array(
		'wh2_main_political_party_vor_hef_teclis',
		array(
			'wh2_main_political_party_vor_hef_teclis' => 'wh2_main_trait_defeated_teclis'
		)
	),
	array(
		'wh2_dlc10_political_party_hef_alarielle',
		array(
			'wh2_dlc10_political_party_hef_alarielle' => 'wh2_dlc10_trait_defeated_alarielle'
		)
	),
	array(
		'wh2_dlc10_political_party_vor_hef_alarielle',
		array(
			'wh2_dlc10_political_party_vor_hef_alarielle' => 'wh2_dlc10_trait_defeated_alarielle'
		)
	),
	array(
		'wh2_dlc10_political_party_hef_alith_anar',
		array(
			'wh2_dlc10_political_party_hef_alith_anar' => 'wh2_dlc10_trait_defeated_alith_anar'
		)
	),
	array(
		'wh2_dlc10_political_party_vor_hef_alith_anar',
		array(
			'wh2_dlc10_political_party_vor_hef_alith_anar' => 'wh2_dlc10_trait_defeated_alith_anar'
		)
	),
#endregion
#region _cst_
	array(
		'wh2_dlc11_political_party_vampire_coast_ruler',
		array(
			'wh2_dlc11_political_party_vampire_coast_ruler' => 'wh2_dlc11_trait_defeated_luthor_harkon'
		)
	),
	array(
		'wh2_dlc11_political_party_vor_vampire_coast_ruler',
		array(
			'wh2_dlc11_political_party_vor_vampire_coast_ruler' => 'wh2_dlc11_trait_defeated_luthor_harkon'
		)
	),
	array(
		'wh2_dlc11_political_party_cst_noctilus_ruler',
		array(
			'wh2_dlc11_political_party_cst_noctilus_ruler' => 'wh2_dlc11_trait_defeated_count_noctilus'
		)
	),
	array(
		'wh2_dlc11_political_party_vor_cst_noctilus_ruler',
		array(
			'wh2_dlc11_political_party_vor_cst_noctilus_ruler' => 'wh2_dlc11_trait_defeated_count_noctilus'
		)
	),
	array(
		'wh2_dlc11_political_party_cst_sartosa_ruler',
		array(
			'wh2_dlc11_political_party_cst_sartosa_ruler' => 'wh2_dlc11_trait_defeated_aranessa_saltspite'
		)
	),
	array(
		'wh2_dlc11_political_party_vor_cst_sartosa_ruler',
		array(
			'wh2_dlc11_political_party_vor_cst_sartosa_ruler' => 'wh2_dlc11_trait_defeated_aranessa_saltspite'
		)
	),
	array(
		'wh2_dlc11_political_party_cst_the_drowned_ruler',
		array(
			'wh2_dlc11_political_party_cst_the_drowned_ruler' => 'wh2_dlc11_trait_defeated_cylostra_direfin'
		)
	),
	array(
		'wh2_dlc11_political_party_vor_cst_the_drowned_ruler',
		array(
			'wh2_dlc11_political_party_vor_cst_the_drowned_ruler' => 'wh2_dlc11_trait_defeated_cylostra_direfin'
		)
	),
#endregion
#region _lzd_
	array(
		'wh2_main_political_party_lzd_lord_mazdamundi',
		array(
			'wh2_main_political_party_lzd_lord_mazdamundi' => 'wh2_main_trait_defeated_lord_mazdamundi'
		)
	),
	array(
		'wh2_main_political_party_vor_lzd_lord_mazdamundi',
		array(
			'wh2_main_political_party_vor_lzd_lord_mazdamundi' => 'wh2_main_trait_defeated_lord_mazdamundi'
		)
	),
	array(
		'wh2_main_political_party_lzd_kroq_gar',
		array(
			'wh2_main_political_party_lzd_kroq_gar' => 'wh2_main_trait_defeated_kroq_gar'
		)
	),
	array(
		'wh2_main_political_party_vor_lzd_kroq_gar',
		array(
			'wh2_main_political_party_vor_lzd_kroq_gar' => 'wh2_main_trait_defeated_kroq_gar'
		)
	),
	array(
		'wh2_dlc12_political_party_lzd_tehenhauin',
		array(
			'wh2_dlc12_political_party_lzd_tehenhauin' => 'wh2_dlc12_trait_defeated_tehenhauin'
		)
	),
	array(
		'wh2_dlc12_political_party_vor_lzd_tehenhauin',
		array(
			'wh2_dlc12_political_party_vor_lzd_tehenhauin' => 'wh2_dlc12_trait_defeated_tehenhauin'
		)
	),
	array(
		'wh2_dlc12_political_party_lzd_tiktaqto',
		array(
			'wh2_dlc12_political_party_lzd_tiktaqto' => 'wh2_dlc12_trait_defeated_tiktaqto'
		)
	),
	array(
		'wh2_dlc12_political_party_vor_lzd_tiktaqto',
		array(
			'wh2_dlc12_political_party_vor_lzd_tiktaqto' => 'wh2_dlc12_trait_defeated_tiktaqto'
		)
	),
#endregion
#region _skv_
	array(
		'wh2_main_political_party_skv_queek_headtaker',
		array(
			'wh2_main_political_party_skv_queek_headtaker' => 'wh2_main_trait_defeated_queen_headtaker'
		)
	),
	array(
		'wh2_main_political_party_vor_skv_queek_headtaker',
		array(
			'wh2_main_political_party_vor_skv_queek_headtaker' => 'wh2_main_trait_defeated_queen_headtaker'
		)
	),
	array(
		'wh2_main_political_party_skv_lord_skrolk',
		array(
			'wh2_main_political_party_skv_lord_skrolk' => 'wh2_main_trait_defeated_lord_strolk'
		)
	),
	array(
		'wh2_main_political_party_vor_skv_lord_skrolk',
		array(
			'wh2_main_political_party_vor_skv_lord_skrolk' => 'wh2_main_trait_defeated_lord_strolk'
		)
	),
	array(
		'wh2_dlc09_political_party_skv_tretch_craventail',
		array(
			'wh2_dlc09_political_party_skv_tretch_craventail' => 'wh2_dlc09_trait_defeated_tretch'
		)
	),
	array(
		'wh2_dlc09_political_party_vor_skv_tretch_craventail',
		array(
			'wh2_dlc09_political_party_vor_skv_tretch_craventail' => 'wh2_dlc09_trait_defeated_tretch'
		)
	),
	array(
		'wh2_dlc12_political_party_skv_ikit_claw',
		array(
			'wh2_dlc12_political_party_skv_ikit_claw' => 'wh2_dlc12_trait_defeated_ikit_claw'
		)
	),
	array(
		'wh2_dlc12_political_party_vor_skv_ikit_claw',
		array(
			'wh2_dlc12_political_party_vor_skv_ikit_claw' => 'wh2_dlc12_trait_defeated_ikit_claw'
		)
	),
#endregion
#region _beastmen_
	array(
		'wh_dlc03_political_party_beastmen_ruler',
		array(
			'wh_dlc03_political_party_beastmen_ruler' => 'wh2_main_trait_defeated_khazrak_one_eye',
			'wh_dlc03_political_party_beastmen_malagor' => 'wh2_main_trait_defeated_malagor_the_dark_omen',
			'wh_dlc03_political_party_beastmen_morghur' => 'wh2_main_trait_defeated_morghur_the_shadowgave'
		)
	),
#endregion
#region _wood_elves_
	array(
		'wh_dlc05_political_party_wood_elves_ruler',
		array(
			'wh_dlc05_political_party_wood_elves_ruler' => 'wh2_main_trait_defeated_orion'
		)
	),
	array(
		'wh_dlc05_political_party_wood_elves_durthu',
		array(
			'wh_dlc05_political_party_wood_elves_durthu' => 'wh2_main_trait_defeated_durthu'
		)
	),
#endregion
#region _bretonnia_
	array(
		'wh_dlc07_political_party_bretonnia_ruler',
		array(
			'wh_dlc07_political_party_bretonnia_ruler' => 'wh2_main_trait_defeated_louen_leoncouer'
		)
	),
	array(
		'wh_dlc07_political_party_bretonnia_fay',
		array(
			'wh_dlc07_political_party_bretonnia_fay' => 'wh2_main_trait_defeated_fay_enchantress'
		)
	),
	array(
		'wh_dlc07_political_party_bretonnia_alberic',
		array(
			'wh_dlc07_political_party_bretonnia_alberic' => 'wh2_main_trait_defeated_alberic_de_bordeleaux'
		)
	),
#endregion
#region _norsca_
	array(
		'wh_dlc08_political_party_norsca_ruler',
		array(
			'wh_dlc08_political_party_norsca_ruler' => 'wh_dlc08_trait_defeated_wulfrik'
		)
	),
	array(
		'wh_dlc08_political_party_norsca_throgg',
		array(
			'wh_dlc08_political_party_norsca_throgg' => 'wh_dlc08_trait_defeated_throgg'
		)
	),
#endregion
#region _chaos_
	array(
		'wh_main_political_party_chaos_ruler',
		array(
			'wh_main_political_party_chaos_ruler' => 'wh2_main_trait_defeated_archaon_the_everchosen',
			'wh_main_political_party_chaos_kholek' => 'wh2_main_trait_defeated_kholek_suneater',
			'wh_main_political_party_chaos_sigvald' => 'wh2_main_trait_defeated_prince_sigvald'
		)
	),
#endregion
#region _dwarf_
	array(
		'wh_main_political_party_dwarf_ruler',
		array(
			'wh_main_political_party_dwarf_ruler' => 'wh2_main_trait_defeated_thorgrim_grudgebearer',
			'wh_main_political_party_dwarf_grombrindal' => 'wh2_main_trait_defeated_grombrindal'
		)
	),
	array(
		'wh_main_political_party_dwarf_ungrim',
		array(
			'wh_main_political_party_dwarf_ungrim' => 'wh2_main_trait_defeated_ungrim_ironfist',
		)
	),
	array(
		'wh_dlc06_political_party_belegar_ruler',
		array(
			'wh_dlc06_political_party_belegar_ruler' => 'wh2_main_trait_defeated_belegar_ironhammer'
		)
	),
#endregion
#region _empire_
	array(
		'wh_main_political_party_empire_ruler',
		array(
			'wh_main_political_party_empire_ruler' => 'wh2_main_trait_defeated_karl_franz',
			'wh_main_political_party_empire_balthasar' => 'wh2_main_trait_defeated_balthasar_gelt',
			'wh_dlc04_political_party_empire_volkmar' => 'wh2_main_trait_defeated_volkmar_the_grim'
		)
	),
#endregion
#region _greenskins_
	array(
		'wh_main_political_party_greenskins_ruler',
		array(
			'wh_main_political_party_greenskins_ruler' => 'wh2_main_trait_defeated_grimgor_ironhide',
			'wh_main_political_party_greenskins_azhag' => 'wh2_main_trait_defeated_azhag_the_slaughterer'
		)
	),
	array(
		'wh_dlc06_political_party_wurrzag_ruler',
		array(
			'wh_dlc06_political_party_wurrzag_ruler' => 'wh2_main_trait_defeated_wurzzag'
		)
	),
	array(
		'wh_dlc06_political_party_skarsnik_ruler',
		array(
			'wh_dlc06_political_party_skarsnik_ruler' => 'wh2_main_trait_defeated_skarsnik'
		)
	),
#endregion
#region _vampire_
	array(
		'wh_main_political_party_vampire_ruler',
		array(
			'wh_main_political_party_vampire_ruler' => 'wh2_main_trait_defeated_mannfred_von_carstein',
			'wh_dlc04_political_party_vampire_helman' => 'wh2_main_trait_defeated_helmen_ghorst'
		)
	),
	array(
		'wh_pro02_political_party_vlad_ruler',
		array(
			'wh_pro02_political_party_vlad_ruler' => 'wh2_main_trait_defeated_vlad_von_carstein',
			'wh_pro02_political_party_vampire_isabella' => 'wh2_main_trait_defeated_isabella_von_carstein'
		)
	),
	array(
		'wh_main_political_party_vampire_heinrich',
		array(
			'wh_main_political_party_vampire_heinrich' => 'wh2_main_trait_defeated_heinrich_kemmler'
		)
	)
#endregion
);
}

foreach ($tables['_political_parties_lords_defeated'] as $file => &$file_table){
	$new_table = array();
	foreach ($file_table as $entry){
		foreach ($entry[1] as $lord => $trait){
			$new_table[] = array($entry[0], $lord, $trait);
		}
	}
	$file_table = $new_table;
	unset($file_table);
}



function transform($tables){
	global $tables_info;
	$keyed = array();
	$used = array();
	
	foreach ($tables as $tbl => $data){
		$ka = $tables_info[ $tbl ]['KEY'];
		$ka_keys = array_keys($ka);
		$len = sizeof($ka);
		
		$tbl_keyed = array();
		$tbl_used = array();
		
		foreach ($data as $file => $file_table){
			$a = array();
			// $aa = array();
			
			foreach ($file_table as $v){
				
				unset($b, $bb);
				// unset($b);
				$b = &$a;
				$bb = &$tbl_used;
				for ($i = 0; $i < $len - 1; ++$i){
					$k = $ka_keys[ $i ];
					$type = $ka[ $k ];
					$key = $v[ $k ];
					if (!isset($b[ $key ])){
						$b[ $key ] = array();
						$bb[ $key ] = array();
					}
					$b = &$b[ $key ];
					$bb = &$bb[ $key ];
				}
				$k = $ka_keys[ $len - 1 ];
				$type = $ka[ $k ];
				$key = $v[ $k ];
				if ($type === 'unique'){
					$b[ $key ] = $v;
					$bb[ $key ] = false;
				}
				else{
					$b[ $key ][] = $v;
					$bb[ $key ][] = false;
				}
			}
			
			$tbl_keyed[ $file ] = $a;
			// $tbl_used[ $file ] = $aa;
		}
		
		$keyed[ $tbl ] = $tbl_keyed;
		$used[ $tbl ] = $tbl_used;
		// $used[ $tbl ] = array();
	}
	return array($keyed, $used);
	// return array($keyed);
}

class Ref {
	public function __construct($a, $b = null){
		if ($b !== null){
			$a = $a .'['. $b .']';
		}
		$this->a = $a;
	}
	public function __toString(){ return $this->a; }
}

// StringRef
if (1){
	class StringRef {
		public $name;
		public $arr = array();
		public function __construct($name){
			$this->name = $name;
		}
	}
	class StringRefHolder {
		public static $names0 = array();
		public static $names = array();
		private $name = array();
		public $ref = array();
		private $cur = null;
		public $all = array();
		
		public function Add($string){
			if ($string === null){ return new Ref('N'); }
			$ref = self::Find($string);
			if ($ref !== null){ return $ref; }
			
			// a whole reason for this is to have as less bytes as possible.
			// you can determine this value by experiment.
			// for different datasets you will need different value
			if ($this->cur === null || sizeof($this->cur->arr) === 34){
				do {
					// i wanted to do this whole thing in math, but very lazy to waste time thinking.
					if (empty($this->name)){
						$this->name[] = self::$names0[0];
					}
					else{
						$idx = $len = sizeof($this->name) - 1;
						while (true){
							if ($idx === 0){
								$pos = array_search($this->name[ 0 ], self::$names0);
								if ($pos < sizeof(self::$names0) - 1){
									$this->name[ 0 ] = self::$names0[ $pos + 1 ];
								} else{
									$this->name[ 0 ] = self::$names0[0];
									$this->name[] = self::$names[0];
								}
								break;
							}
							else{
								$pos = array_search($this->name[ $idx ], self::$names);
								if ($pos < sizeof(self::$names) - 1){
									$this->name[ $idx ] = self::$names[ $pos + 1 ];
									break;
								}
								--$idx;
							}
						}
						for (++$idx; $idx <= $len; ++$idx){
							$this->name[ $idx ] = self::$names[0];
						}
					}
				} while (in_array(implode('', $this->name), array('T', 'F', 'N')));
				
				$this->ref[] = $this->cur = new StringRef(implode('', $this->name));
			}
			$this->cur->arr[] = $string;
			$this->all[ $string ] = $this->cur;
			return new Ref($this->cur->name, sizeof($this->cur->arr));
		}
		public function Find($string){
			if (!isset($this->all[ $string ])){ return null; }
			$cur = $this->all[ $string ];
			return new Ref($cur->name, array_search($string, $cur->arr) + 1);
		}
	}

	StringRefHolder::$names0[] = '_';
	// lowercase
	for ($i = 97; $i <= 122; ++$i){
		StringRefHolder::$names0[] = chr($i);
	}
	// uppercase
	for ($i = 65; $i <= 90; ++$i){
		StringRefHolder::$names0[] = chr($i);
	}
	StringRefHolder::$names = StringRefHolder::$names0;
	// numbers
	for ($i = 48; $i <= 57; ++$i){
		StringRefHolder::$names[] = chr($i);
	}
	$StringHolder = new StringRefHolder();
}

// $keyed = array();
// $used = array();
list($keyed, $used) = transform($tables);

// упорядочивание таблиц
if (1){
// tables are sorted, by how much of the same values are duplicated and the size of dataset in DESC
// (in consideration to space saving)
$tables = array(
	// effect, icon, icon_negative, category
	'effects' => $tables['effects'],
	// trait_level, trait
	'character_trait_levels' => $tables['character_trait_levels'],
	// trait_level, effect, effect_scope
	'trait_level_effects' => $tables['trait_level_effects'],
	// trait_category, icon_path
	'trait_categories' => $tables['trait_categories'],
	// trait
	'character_traits' => $tables['character_traits'],
	// trait, antitrait
	'trait_to_antitraits' => $tables['trait_to_antitraits'],
	// party, faction
	'faction_political_parties_junctions' => $tables['faction_political_parties_junctions'],
	// leader
	'frontend_faction_leaders' => $tables['frontend_faction_leaders'],
	// faction
	'frontend_factions' => $tables['frontend_factions'],
	// party, leader, trait
	'_political_parties_lords_defeated' => $tables['_political_parties_lords_defeated'],
	// party, campaign
	'political_parties' => $tables['political_parties'],
	// subtype_key
	'agent_subtypes' => $tables['agent_subtypes']
);
}


foreach ($tables['character_trait_levels'] as $file_1 => $file_table_1){
	foreach ($file_table_1 as $trait){
		// (trait) character_trait_levels.trait
		$used['character_traits'][ $trait[2] ] = true;
		
		// (trait_level) character_trait_levels.key
		$key = $trait[0];
		if (!isset($used['trait_level_effects'][ $key ])){
			continue;
		}
		
		// $used['trait_level_effects'][ $key ] = true;
		// if (!in_array($key, $used['trait_level_effects'])){
			// $used['trait_level_effects'][] = $key;
		// }
		
		foreach ($keyed['trait_level_effects'] as $file_2 => $data_2){
			if (!isset($data_2[ $key ])){ continue; }
			foreach ($data_2[ $key ] as $eff){
				// (effect) trait_level_effects.effect
				$used['effects'][ $eff[1] ] = true;
				$used['trait_level_effects'][ $key ][ $eff[1] ] = true;
			}
		}
	}
}

// excluding data
foreach ($tables as $key => &$data){
	if (isset($exclude[ $key ])){
		foreach ($exclude[ $key ] as $file){
			unset($data[ $file ]);
		}
	}
}


// Подготовка данных для вывода
if (1){
foreach ($tables['agent_subtypes'] as $file => &$file_table){
	foreach ($file_table as &$entry){
		// key
		$entry[0] = $StringHolder->Add($entry[0]);
		$entry = array_values($entry);
	}
	unset($entry, $file_table);
}

foreach ($tables['character_trait_levels'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as &$entry){
		// key
		$entry[0] = $StringHolder->Add($entry[0]);
		// trait
		$entry[2] = $StringHolder->Add($entry[2]);
		// $table[] = $entry;
	}
	// $keyed['character_trait_levels'][ $file ] = $table;
	unset($entry, $file_table);
}

foreach ($tables['character_traits'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as $i => &$entry){
		if (!$used['character_traits'][ $entry[0] ]){
			unset($file_table[ $i ]);
			continue;
		}
		// key
		$entry[0] = $StringHolder->Add($entry[0]);
		// hidden
		$entry[2] = new Ref($entry[2] ? 'T' : 'F');
		// icon
		$entry[4] = $StringHolder->Add($entry[4]);
		// $table[] = $entry;
	}
	$file_table = array_values($file_table);
	// $keyed['character_traits'][ $file ] = $table;
	unset($entry, $file_table);
}

if ($do_experimental){
	foreach ($tables['effect_bonus_value_unit_ability_junctions'] as $file => &$file_table){
		foreach ($file_table as $i => &$entry){
			if (!$used['effects'][ $entry[0] ]){
				unset($file_table[ $i ]);
				continue;
			}
			// effect
			$entry[0] = $StringHolder->Add($entry[0]);
			// unit_ability
			$entry[1] = $StringHolder->Add($entry[1]);
		}
		$file_table = array_values($file_table);
		unset($entry, $file_table);
	}
}

foreach ($tables['effects'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as $i => &$entry){
		if (!$used['effects'][ $entry[0] ]){
			unset($file_table[ $i ]);
			continue;
		}
		// effect
		$entry[0] = $StringHolder->Add($entry[0]);
		// icon
		$entry[1] = $StringHolder->Add($entry[1]);
		// icon_negative
		$entry[3] = $StringHolder->Add($entry[3]);
		// category
		$entry[4] = $StringHolder->Add($entry[4]);
		// is_positive_value_good
		$entry[5] = new Ref($entry[5] ? 'T' : 'F');
		// $table[] = $entry;
	}
	$file_table = array_values($file_table);
	// $keyed['effects'][ $file ] = $table;
	unset($entry, $file_table);
}

foreach ($tables['faction_political_parties_junctions'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as &$entry){
		// faction_key
		$entry[0] = $StringHolder->Add($entry[0]);
		// political_party_key
		$entry[1] = $StringHolder->Add($entry[1]);
		// $table[] = $entry;
	}
	// $keyed['faction_political_parties_junctions'][ $file ] = $table;
	unset($entry, $file_table);
}

foreach ($tables['frontend_faction_leaders'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as &$entry){
		// key
		$entry[3] = $StringHolder->Add($entry[3]);
		// character_image
		$entry[4] = $StringHolder->Add($entry[4]);
		// $table[] = array_values($entry);
		$entry = array_values($entry);
	}
	// $keyed['frontend_faction_leaders'][ $file ] = $table;
	unset($entry, $file_table);
}

foreach ($tables['frontend_factions'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as &$entry){
		// faction
		$entry[0] = $StringHolder->Add($entry[0]);
		// $table[] = array_values($entry);
		$entry = array_values($entry);
	}
	// $keyed['frontend_factions'][ $file ] = $table;
	unset($entry, $file_table);
}

foreach ($tables['_political_parties_lords_defeated'] as $file => &$file_table){
	foreach ($file_table as &$entry){
		// political_party
		$entry[0] = $StringHolder->Add($entry[0]);
		// lord
		$entry[1] = $StringHolder->Add($entry[1]);
		// trait
		$entry[2] = $StringHolder->Add($entry[2]);
	}
	unset($entry, $file_table);
}

foreach ($tables['political_parties'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as &$entry){
		// key (political_party)
		$entry[0] = $StringHolder->Add($entry[0]);
		// campaign_key
		$entry[4] = $StringHolder->Add($entry[4]);
		// $table[] = array_values($entry);
		$entry = array_values($entry);
	}
	// $keyed['political_parties'][ $file ] = $table;
	unset($entry, $file_table);
}

foreach ($tables['trait_categories'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as &$entry){
		// category
		$entry[0] = $StringHolder->Add($entry[0]);
		// icon_path
		$entry[1] = $StringHolder->Add($entry[1]);
		// $table[] = $entry;
	}
	// $keyed['trait_categories'][ $file ] = $table;
	unset($entry, $file_table);
}

foreach ($tables['trait_level_effects'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as $i => &$entry){
		if (!$used['trait_level_effects'][ $entry[0] ][ $entry[1] ]){
			unset($file_table[ $i ]);
			continue;
		}
		// foreach ($effects as $entry){
			// trait_level
			$entry[0] = $StringHolder->Add($entry[0]);
			// effect
			$entry[1] = $StringHolder->Add($entry[1]);
			// effect_scope
			$entry[2] = $StringHolder->Add($entry[2]);
			// $table[] = $entry;
		// }
	}
	$file_table = array_values($file_table);
	// $keyed['trait_level_effects'][ $file ] = $table;
	unset($entry, $file_table);
}

foreach ($tables['trait_to_antitraits'] as $file => &$file_table){
	// $table = array();
	foreach ($file_table as &$entry){
		// trait
		$entry[0] = $StringHolder->Add($entry[0]);
		// antitrait
		$entry[1] = $StringHolder->Add($entry[1]);
		// $table[] = $entry;
	}
	// $keyed['trait_to_antitraits'][ $file ] = $table;
	unset($entry, $file_table);
}

if ($do_experimental){
	foreach ($tables['unit_abilities'] as $file => &$file_table){
		foreach ($file_table as $i => &$entry){
			// effect
			$entry[0] = $StringHolder->Add($entry[0]);
			// unit_ability
			$entry[1] = $StringHolder->Add($entry[1]);
		}
		$file_table = array_values($file_table);
		unset($entry, $file_table);
	}
}
}









$left = array('T', 'F', 'N');
$right = array('true', 'false', 'nil');
foreach ($StringHolder->ref as $cur){
	$left[] = $cur->name;
	$right[] = my_print($cur->arr, "\t", $mini);
}
echo '
local ', implode(',', $left), ' = ', implode(',', $right);
foreach ($tables as $key => $data){
	if (empty($data)){
		unset($tables[ $key ]);
		continue;
	}
}
echo '
return ', my_print($tables, "\t", $mini);











