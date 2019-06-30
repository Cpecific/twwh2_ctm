<?php

header('');

$mini = 2;
define('JAVASCRIPT', false);
define('IS_MODDED', true);

// FILL THIS ARRAY IF YOU HAVE REPLACED data__ IN SOME TABLES
$modded_data__ = array(
	// Ex.:    'some_table' => true,
);

// DB_TEXT
if (JAVASCRIPT){
	$dir = __DIR__ .'/game/text/db/';
	$text_info = array(
		'effects' => array(
			'FILE' => $dir .'effects__.loc'
		),
		'campaign_effect_scopes' => array(
			'FILE' => $dir .'campaign_effect_scopes__.loc'
		),
		'character_trait_levels' => array(
			'FILE' => $dir .'character_trait_levels__.loc'
		),
		'Isenharttraits' => array(
			'FILE' => $dir .'Isenharttraits.loc'
		)
	);
	// all localisation files
	if (0){
		$text_info = array();
		foreach (scandir($dir, 1) as $file){
			if ($file === '.' || $file === '..' || is_dir($dir . $file)){ continue; }
			
			$path = realpath($dir . $file);
			
			$text_info[ rtrim(mb_substr($file, 0, mb_strlen($file) - 4), '__') ] = array(
				'FILE' => $dir . $file
			);
		}
	}
}

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
	if ($a instanceof Ref || $a instanceof StringCnt){
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
		$brackets = array('{', '}');
		foreach ($a as $k => $v){
			if (JAVASCRIPT){
				$k = '"'. addslashes($k) .'"';
			}
			else{
				if (!preg_match('#^[\w\d\_]+$#', $k)){
					$k = '["'. addslashes($k) .'"]';
				}
			}
			$d = $k . ($mini ? '' : ' ') . (JAVASCRIPT ? ':' : '=') . ($mini ? '' : ' ') . my_print($v, $new_level, $mini);
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
		$brackets = (JAVASCRIPT ? array('[', ']') : array('{', '}'));
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
		return $brackets[0]. mb_substr($str, 1) .$brackets[1];
	}
	else if ($mini === 1){
		if ($has_obj){ return $brackets[0] . mb_substr($str, 1) ."\r\n". $brackets[1]; }
		return $brackets[0] . mb_substr($str, 1) . $brackets[1];
	}
	if ($has_obj){ return $brackets[0] . mb_substr($str, 1) ."\r\n". mb_substr($level, 1) . $brackets[1]; }
	return $brackets[0] . mb_substr($str, 2) . $brackets[1];
}


echo '<pre style="word-break: break-all; white-space: pre-wrap;">';

// SCHEMA
$tables_info = array(
#region HIDE
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
			array( 'NAME' => 'key',								'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'level',							'TYPE' => 'int' ),
			array( 'NAME' => 'trait',							'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'threshold_points',				'TYPE' => 'int' )
		)
	),
	'character_traits' => array(
		'DIR' => 'character_traits_tables',
		'HEADER_SIZE' => 91,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'key',								'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'no_going_back_level',				'TYPE' => 'int' ),
			array( 'NAME' => 'hidden',							'TYPE' => 'bool' ),
			array( 'NAME' => 'precedence',						'TYPE' => 'int' ),
			array( 'NAME' => 'icon',							'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'ui',								'TYPE' => 'int' ),
			array( 'NAME' => 'pre_battle_speech_parameter',		'TYPE' => 'optstring',		'EXCLUDE' => true )
		)
	),
#endregion
#region effect_bonus_value
	'effect_bonus_value_id_action_results_additional_outcomes_junctions' => array(
		'DIR' => 'effect_bonus_value_id_action_results_additional_outcomes_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 2 => 'unique' ),
		'ENTRY' => array(1, 2),
		'SCHEMA' => array(
			// action_results_additional_outcome_record
			array( 'NAME' => 'araor',						'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' )
		)
	),
	'effect_bonus_value_agent_junction' => array(
		'DIR' => 'effect_bonus_value_agent_junction_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 0 => 'unique' ),
		'ENTRY' => array(1, 0),
		'SCHEMA' => array(
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'agent',						'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_agent_action_record_junctions' => array(
		'DIR' => 'effect_bonus_value_agent_action_record_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'agent_action_record',			'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_agent_subtype_junctions' => array(
		'DIR' => 'effect_bonus_value_agent_subtype_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'subtype',						'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_attrition_record_junctions' => array(
		'DIR' => 'effect_bonus_value_attrition_record_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 2 => 'unique' ),
		'ENTRY' => array(1, 2),
		'SCHEMA' => array(
			array( 'NAME' => 'attrition_record',			'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' )
		)
	),
	'effect_bonus_value_basic_junction' => array(
		'DIR' => 'effect_bonus_value_basic_junction_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 0 => 'unique' ),
		'ENTRY' => array(1, 0),
		'SCHEMA' => array(
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' )
		)
	),
	'effect_bonus_value_battle_context_army_special_ability_junctions' => array(
		'DIR' => 'effect_bonus_value_battle_context_army_special_ability_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 2 => 'unique' ),
		'ENTRY' => array(1, 2),
		'SCHEMA' => array(
			// battle_context_army_special_ability
			array( 'NAME' => 'bcasa',						'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' )
		)
	),
	'effect_bonus_value_battle_context_unit_ability_junctions' => array(
		'DIR' => 'effect_bonus_value_battle_context_unit_ability_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 2 => 'unique' ),
		'ENTRY' => array(1, 2),
		'SCHEMA' => array(
			// battle_context_unit_ability
			array( 'NAME' => 'bcua',						'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' )
		)
	),
	'effect_bonus_value_battle_context_unit_attribute_junctions' => array(
		'DIR' => 'effect_bonus_value_battle_context_unit_attribute_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 2 => 'unique' ),
		'ENTRY' => array(1, 2),
		'SCHEMA' => array(
			// battle_context_unit_atribute
			array( 'NAME' => 'bcua',						'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' )
		)
	),
	'effect_bonus_value_battle_context_junctions' => array(
		'DIR' => 'effect_bonus_value_battle_context_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 2 => 'unique' ),
		'ENTRY' => array(1, 2),
		'SCHEMA' => array(
			array( 'NAME' => 'battle_context_key',			'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' )
		)
	),
	'effect_bonus_value_building_set_junctions' => array(
		'DIR' => 'effect_bonus_value_building_set_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 2 => 'unique' ),
		'ENTRY' => array(0, 2),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'building_set',				'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' )
		)
	),
	'effect_bonus_value_faction_junctions' => array(
		'DIR' => 'effect_bonus_value_faction_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'faction',						'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_loyalty_event_junctions' => array(
		'DIR' => 'effect_bonus_value_loyalty_event_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'loyalty_event',				'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_military_force_ability_junctions' => array(
		'DIR' => 'effect_bonus_value_military_force_ability_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'force_ability',				'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_missile_weapon_junctions' => array(
		'DIR' => 'effect_bonus_value_missile_weapon_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			// missile_weapon_junction
			array( 'NAME' => 'mwj',							'TYPE' => 'int',			'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_pooled_resource_factor_junctions' => array(
		'DIR' => 'effect_bonus_value_pooled_resource_factor_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'resource_factor',				'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_pooled_resource_junctions' => array(
		'DIR' => 'effect_bonus_value_pooled_resource_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'pooled_resource',				'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_religion_junction' => array(
		'DIR' => 'effect_bonus_value_religion_junction_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 0 => 'unique' ),
		'ENTRY' => array(1, 0),
		'SCHEMA' => array(
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'religion',					'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_resource_junction' => array(
		'DIR' => 'effect_bonus_value_resource_junction_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 0 => 'unique' ),
		'ENTRY' => array(1, 0),
		'SCHEMA' => array(
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'resource',					'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_ritual_junctions' => array(
		'DIR' => 'effect_bonus_value_ritual_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'ritual',						'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_siege_item_junctions' => array(
		'DIR' => 'effect_bonus_value_siege_item_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'siege_item',					'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_special_ability_phase_record_junctions' => array(
		'DIR' => 'effect_bonus_value_special_ability_phase_record_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			// special_ability_phase
			array( 'NAME' => 'sap',							'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_subculture_junctions' => array(
		'DIR' => 'effect_bonus_value_subculture_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'subculture',					'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	// когда эффект ссылается на абилку, и мы ставим иконку картинки (unit_abilities)
	'effect_bonus_value_unit_ability_junctions' => array(
		'DIR' => 'effect_bonus_value_unit_ability_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique', 0 => 'unique' ),
		'ENTRY' => array(1, 0, 2),
		'SCHEMA' => array(
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'unit_ability',				'TYPE' => 'string_ascii' )
		)
	),
	'effect_bonus_value_unit_attribute_junctions' => array(
		'DIR' => 'effect_bonus_value_unit_attribute_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'unit_attribute',				'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_unit_set_unit_ability_junctions' => array(
		'DIR' => 'effect_bonus_value_unit_set_unit_ability_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'unit_set_ability',			'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_unit_set_unit_attribute_junctions' => array(
		'DIR' => 'effect_bonus_value_unit_set_unit_attribute_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'unit_set_attribute',			'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effect_bonus_value_ids_unit_sets' => array(
		'DIR' => 'effect_bonus_value_ids_unit_sets_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique', 1 => 'unique' ),
		'ENTRY' => array(0, 1),
		'SCHEMA' => array(
			array( 'NAME' => 'bonus_value_id',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			// к каким юнитам/типам юнитов применяется
			array( 'NAME' => 'unit_set',					'TYPE' => 'string_ascii',	'EXCLUDE' => true )
		)
	),
	'effects' => array(
		'DIR' => 'effects_tables',
		'HEADER_SIZE' => 91,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'effect',						'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'icon',						'TYPE' => 'optstring' ),
			array( 'NAME' => 'priority',					'TYPE' => 'int' ),
			array( 'NAME' => 'icon_negative',				'TYPE' => 'optstring' ),
			array( 'NAME' => 'category',					'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'is_positive_value_good',		'TYPE' => 'bool' )
		)
	),
#endregion
#region HIDE
	// тут мы получим faction для political_party
	'faction_political_parties_junctions' => array(
		'DIR' => 'faction_political_parties_junctions_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 1 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'faction_key',					'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'political_party_key',			'TYPE' => 'string_ascii' ),
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
			array( 'NAME' => 'faction',			'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'video',			'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'sort_order',		'TYPE' => 'int' ),
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
			array( 'NAME' => 'key',				'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'playable',		'TYPE' => 'bool',			'EXCLUDE' => true ),
			array( 'NAME' => 'effect_bundle',	'TYPE' => 'string_ascii',	'EXCLUDE' => true ),
			array( 'NAME' => 'initial_power',	'TYPE' => 'int',			'EXCLUDE' => true ),
			array( 'NAME' => 'campaign_key',	'TYPE' => 'string_ascii' )
		)
	),
	'trait_categories' => array(
		'DIR' => 'trait_categories_tables',
		'HEADER_SIZE' => 83,
		'KEY' => array( 0 => 'unique' ),
		'SCHEMA' => array(
			array( 'NAME' => 'category',		'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'icon_path',		'TYPE' => 'optstring' )
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
			array( 'NAME' => 'trait',			'TYPE' => 'string_ascii' ),
			array( 'NAME' => 'antitrait',		'TYPE' => 'string_ascii' )
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
#endregion
);

if (!isset($tables)){
	$tables = array();
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
// YOU CAN SET YOUR LEGENDARY LORDS RIGHT ABOUT HERE
// $tables['_political_parties_lords_defeated'][ YOURMOD ] = ...


foreach ($tables_info as $tbl_name => $tbl_info){
	$tbl_key = $tbl_info['KEY'];
	if (!isset($tables[ $tbl_name ])){ $tables[ $tbl_name ] = array(); }
	$_tbl_data = $tables[ $tbl_name ];
	
	if (!isset($tbl_info['DIR'])){ continue; }
	
	$dir = __DIR__ .'/game/db/'. $tbl_info['DIR'] .'/';
	if (!is_dir($dir)){ continue; }
	
	if (isset($tbl_info['ENTRY'])){
		$key = array();
		foreach ($tbl_info['KEY'] as $idx => $_type){
			$i = array_search($idx, $tbl_info['ENTRY']);
			$key[ $i ] = $_type;
		}
		// var_dump($tbl_name, $tbl_info['KEY'], $key);exit;
		$tables_info[ $tbl_name ]['KEY'] = $key;
	}
	
	foreach (scandir($dir, 1) as $file){
		if ($file === '.' || $file === '..' || is_dir($dir . $file)){ continue; }
		
		$_data = array();
		$path = realpath($dir . $file);
		// var_dump($tbl_name .' = '. $dir, $file);
		$h = fopen($dir . $file, 'r');
		if (!$h){ continue; }
		fread($h, $tbl_info['HEADER_SIZE'] - 4);
		$entries = unpack('V', fread($h, 4));
		$entries = $entries[1];
		
		for ($i = 0; $i < $entries; ++$i){
			$entry = array();
			
			foreach ($tbl_info['SCHEMA'] as $sch_idx => $sch_column){
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
			if (isset($tbl_info['ENTRY'])){
				$tmp = array();
				foreach ($tbl_info['ENTRY'] as $idx){
					$tmp[] = $entry[ $idx ];
				}
				$_data[] = $tmp;
			}
			else{
				$_data[] = $entry;
			}
		}
		
		fclose($h);
		$_tbl_data[ $file ] = $_data;
	}
	$tables[ $tbl_name ] = $_tbl_data;
}

// FAST WAY TO REMOVE YOUR MOD (WITHOUT REMOVING FILES)
if (0){
	foreach (array(
		'trait_level_effects' => array('Isenhart_traits_data__', 'Isenhart_traitsBRT_data__'),
		'character_trait_levels' => array('Isenhart_traits_data__', 'Isenhart_traitsBRT_data__')
	) as $tbl_name => $files){
		foreach ($files as $file){
			unset($tables[ $tbl_name ][ $file ]);
		}
	}
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

#region Ref
class Ref {
	public $str;
	public function __construct($a, $b = null){
		if ($b !== null){
			$a = $a .'['. $b .']';
		}
		$this->str = $a;
	}
	public function __toString(){ return $this->str; }
}
class StringRef {
	public $name;
	public $arr = array();
	public function __construct($name){
		$this->name = $name;
	}
}
class StringCnt {
	public $string;
	public $cnt;
	public $ref;
	public function __construct($string){
		$this->string = $string;
		$this->cnt = 0;
	}
	public function __toString(){ return $this->ref->str; }
}
class StringRefHolder {
	public static $names0 = array();
	public static $names = array();
	private $name = array();
	public $ref = array();
	private $cur = null;
	public $pool = array();
	
	public function Add($string){
		if ($string === null){ return new Ref('N'); }
		if (!isset($this->pool[ $string ])){
			$this->pool[ $string ] = $ref = new StringCnt($string);
		}
		else{
			$ref = $this->pool[ $string ];
		}
		++$ref->cnt;
		return $ref;
	}
	public function AddOpt($string){
		if ($this->cur === null || sizeof($this->cur->arr) === 99){
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
		if (JAVASCRIPT){ return new Ref($this->cur->name, sizeof($this->cur->arr) - 1); }
		else{ return new Ref($this->cur->name, sizeof($this->cur->arr)); }
	}
	public function Optimize(){
		// reversed
		uasort($this->pool, function($a, $b){
			return $b->cnt - $a->cnt;
		});
		foreach ($this->pool as $sc){
			$sc->ref = $this->AddOpt($sc->string);
		}
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
#endregion

list($keyed, $used) = transform($tables);

// DB_TEXT loading
if (JAVASCRIPT){
	$text_data = array();
	foreach ($text_info as $tbl_name => $tbl_data){
		$data = array();
		
		$file = realpath($tbl_data['FILE']);
		
		$h = fopen($file, 'r');
		if (!$h){ continue; }
		fread($h, 14 - 4);
		$entries = unpack('V', fread($h, 4));
		$entries = $entries[1];
		//var_dump($entries);exit;
		
		for ($i = 0; $i < $entries; ++$i){
			//$entry = array();
			
			for ($j = 0; $j < 2; ++$j){
				$length = unpack('v', fread($h, 2));
				$value = '';
				for ($length = $length[1]; $length > 0;){
					$a = fread($h, 1); $b = fread($h, 1);
					if ($b == 0x0A){
						fread($h, 2);
					}
					else if ($a == 0x0A && $b == 0){
						continue;
					}
					$value .= $a;
					--$length;
				}
				if ($j === 0){ $tag = $value; }
				else{ $data[ $tag ] = $value; }
			}
			fread($h, 1);
		}
		
		$text_data[ $tbl_name ] = $data;
	}
	
	// global search in localisations
	if (0){
	foreach ($text_data as $tbl_name => $data){
		foreach ($data as $tag => $val){
			$has = false;
			$lower = strtolower($val);
			// $lower = $val;
			foreach (array(
				// 'armour',
				// 'melee attack'
				'traits'
			) as $find){
				if (
				strpos($lower, $find) !== false
				&& mb_strlen($val) < 100
				// && mb_strlen($val) < mb_strlen($find) + 5
				){
					$has = true;
					break;
				}
			}
			if ($has){
				var_dump('['. $tbl_name .'] '. $tag .': '. $val);
			}
		}
	}
	}
}

#region USED
if (IS_MODDED){
	foreach ($tables['character_trait_levels'] as $file_1 => $file_table_1){
		foreach ($file_table_1 as $trait){
			$used['character_trait_levels'][ $trait[0] ] = true;
			// (trait) character_trait_levels.trait
			$used['character_traits'][ $trait[2] ] = true;
			
			// (trait_level) character_trait_levels.key
			$key = $trait[0];
			
			foreach ($keyed['trait_level_effects'] as $file_2 => $file_keyed_2){
				if (!isset($file_keyed_2[ $key ])){ continue; }
				foreach ($file_keyed_2[ $key ] as $effect => $_){
					// (effect) trait_level_effects.effect
					$used['effects'][ $effect ] = true;
					$used['trait_level_effects'][ $key ][ $effect ] = true;
				}
			}
		}
	}
	foreach ($tables['_political_parties_lords_defeated'] as $file => $file_table){
		foreach ($file_table as $entry){
			$used['political_parties'][ $entry[0] ] = true;
			$used['frontend_faction_leaders'][ $entry[1] ] = true;
			$used['character_traits'][ $entry[2] ] = true;
		}
	}
	foreach ($tables['character_traits'] as $file => $file_table){
		foreach ($file_table as $entry){
			if (!$used['character_traits'][ $entry[0] ]){
				continue;
			}
			$used['trait_categories'][ $entry[4] ] = true;
		}
	}
	foreach ($tables['faction_political_parties_junctions'] as $file => $file_table){
		foreach ($file_table as $entry){
			if (!$used['political_parties'][ $entry[1] ]){
				continue;
			}
			$used['frontend_factions'][ $entry[0] ] = true;
		}
	}
	foreach ($tables['effect_bonus_value_unit_ability_junctions'] as $file => $file_table){
		foreach ($file_table as $entry){
			if (!$used['effects'][ $entry[1] ]){
				continue;
			}
			$used['unit_abilities'][ $entry[2] ] = true;
		}
	}
}
#endregion


#region Подготовка данных для вывода
foreach ($tables['agent_subtypes'] as $file => &$file_table){
	foreach ($file_table as &$entry){
		$entry = array_values($entry);
		// key
		$entry[0] = $StringHolder->Add($entry[0]);
		// can_gain_xp
		$entry[1] = new Ref($entry[1] ? 'T' : 'F');
	}
	unset($entry, $file_table);
}

foreach ($tables['character_trait_levels'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		!$used['character_trait_levels'][ $entry[0] ] &&
		!$used['character_traits'][ $entry[2] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// key
		$entry[0] = $StringHolder->Add($entry[0]);
		// trait
		$entry[2] = $StringHolder->Add($entry[2]);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['character_traits'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		!$used['character_traits'][ $entry[0] ] &&
		!$used['trait_categories'][ $entry[4] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// key
		$entry[0] = $StringHolder->Add($entry[0]);
		// hidden
		$entry[2] = new Ref($entry[2] ? 'T' : 'F');
		// icon
		$entry[4] = $StringHolder->Add($entry[4]);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['effect_bonus_value_unit_ability_junctions'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		!$used['effects'][ $entry[1] ] &&
		!$used['unit_abilities'][ $entry[2] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		$used['unit_abilities'][ $entry[2] ] = true;
		// unit_ability
		$entry[2] = $StringHolder->Add($entry[2]);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['effects'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		// да, icon, icon_negative и category здесь не нужно проверять
		if (
		IS_MODDED &&
		!$used['effects'][ $entry[0] ]
		){
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
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['faction_political_parties_junctions'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		(isset($used['frontend_factions'][ $entry[0] ]) && !$used['frontend_factions'][ $entry[0] ]) &&
		!$used['political_parties'][ $entry[1] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// faction_key
		$entry[0] = $StringHolder->Add($entry[0]);
		// political_party_key
		$entry[1] = $StringHolder->Add($entry[1]);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['frontend_faction_leaders'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		!$used['frontend_faction_leaders'][ $entry[3] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// key
		$entry[3] = $StringHolder->Add($entry[3]);
		// character_image
		$entry[4] = $StringHolder->Add($entry[4]);
		$entry = array_values($entry);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}
// нужна для сортировки лордов
foreach ($tables['frontend_factions'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		!$used['frontend_factions'][ $entry[0] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// faction
		$entry[0] = $StringHolder->Add($entry[0]);
		$entry = array_values($entry);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['_political_parties_lords_defeated'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		!$used['political_parties'][ $entry[0] ] &&
		!$used['frontend_faction_leaders'][ $entry[1] ] &&
		!$used['character_traits'][ $entry[2] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// political_party
		$entry[0] = $StringHolder->Add($entry[0]);
		// lord
		$entry[1] = $StringHolder->Add($entry[1]);
		// trait
		$entry[2] = $StringHolder->Add($entry[2]);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['political_parties'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		!$used['political_parties'][ $entry[0] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// key (political_party)
		$entry[0] = $StringHolder->Add($entry[0]);
		// campaign_key
		$entry[4] = $StringHolder->Add($entry[4]);
		$entry = array_values($entry); // 2 values
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['trait_categories'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		!$used['trait_categories'][ $entry[0] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// category
		$entry[0] = $StringHolder->Add($entry[0]);
		// icon_path
		$entry[1] = $StringHolder->Add($entry[1]);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['trait_level_effects'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		// !$used['trait_level_effects'][ $entry[0] ][ $entry[1] ]
		if (
		IS_MODDED &&
		!$used['character_trait_levels'][ $entry[0] ] &&
		!$used['effects'][ $entry[1] ]
		){
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
		// }
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['trait_to_antitraits'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		if (
		IS_MODDED &&
		!$used['character_traits'][ $entry[0] ] &&
		!$used['character_traits'][ $entry[1] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// trait
		$entry[0] = $StringHolder->Add($entry[0]);
		// antitrait
		$entry[1] = $StringHolder->Add($entry[1]);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach ($tables['unit_abilities'] as $file => &$file_table){
	foreach ($file_table as $i => &$entry){
		$entry = array_values($entry);
		if (
		IS_MODDED &&
		!$used['unit_abilities'][ $entry[0] ]
		){
			unset($file_table[ $i ]);
			continue;
		}
		// key
		$entry[0] = $StringHolder->Add($entry[0]);
		// icon_name
		$entry[1] = $StringHolder->Add($entry[1]);
	}
	$file_table = array_values($file_table);
	unset($entry, $file_table);
}

foreach (array(
	'effect_bonus_value_id_action_results_additional_outcomes_junctions',
	'effect_bonus_value_agent_junction',
	'effect_bonus_value_agent_action_record_junctions',
	'effect_bonus_value_agent_subtype_junctions',
	'effect_bonus_value_attrition_record_junctions',
	'effect_bonus_value_basic_junction',
	'effect_bonus_value_battle_context_army_special_ability_junctions',
	'effect_bonus_value_battle_context_unit_ability_junctions',
	'effect_bonus_value_battle_context_unit_attribute_junctions',
	'effect_bonus_value_battle_context_junctions',
	'effect_bonus_value_building_set_junctions',
	'effect_bonus_value_faction_junctions',
	'effect_bonus_value_loyalty_event_junctions',
	'effect_bonus_value_military_force_ability_junctions',
	'effect_bonus_value_missile_weapon_junctions',
	'effect_bonus_value_pooled_resource_factor_junctions',
	'effect_bonus_value_pooled_resource_junctions',
	'effect_bonus_value_religion_junction',
	'effect_bonus_value_resource_junction',
	'effect_bonus_value_ritual_junctions',
	'effect_bonus_value_siege_item_junctions',
	'effect_bonus_value_special_ability_phase_record_junctions',
	'effect_bonus_value_subculture_junctions',
	'effect_bonus_value_unit_ability_junctions',
	'effect_bonus_value_unit_attribute_junctions',
	'effect_bonus_value_unit_set_unit_ability_junctions',
	'effect_bonus_value_unit_set_unit_attribute_junctions',
	'effect_bonus_value_ids_unit_sets'
) as $tbl_name){
	foreach ($tables[ $tbl_name ] as $file => &$file_table){
		foreach ($file_table as $i => &$entry){
			if (
			IS_MODDED &&
			!$used['effects'][ $entry[1] ]
			){
				unset($file_table[ $i ]);
				continue;
			}
			// bonus_value_id
			$entry[0] = $StringHolder->Add($entry[0]);
			// effect
			$entry[1] = $StringHolder->Add($entry[1]);
		}
		$file_table = array_values($file_table);
		unset($entry, $file_table);
	}
}
#endregion



foreach ($tables as $tbl_name => $data){
	$is_modded = (isset($modded_data__[ $tbl_name ]) && $modded_data__[ $tbl_name ]);
	if ($is_modded){ continue; }
	
	if (IS_MODDED){
		unset($tables[ $tbl_name ]['data__']);
	}
	// echo '<b>Fatal Warning:</b>    Not';
	
	if (empty($tables[ $tbl_name ])){
		unset($tables[ $tbl_name ]);
	}
}

$StringHolder->Optimize();
$left = array('T', 'F', 'N');
$right = array('true', 'false', JAVASCRIPT ? 'null' : 'nil');
foreach ($StringHolder->ref as $cur){
	$left[] = $cur->name;
	$right[] = my_print($cur->arr, "\t", $mini);
}

// js
if (JAVASCRIPT){
	echo '(function(){
var ';
	foreach ($left as $i => $var){
		echo ($i > 0 ? ",\r\n    " : ''), $var,' = ', $right[ $i ];
	}
	// $tables = array( 'effects' => $tables['effects'] );
	echo '
DB_DATA = extend(typeof DB_DATA === "undefined" ? {} : DB_DATA, ', my_print($tables, "\t", $mini), ')';
	if (IS_MODDED){
		unset($text_data['effects']);
		unset($text_data['campaign_effect_scopes']);
		unset($text_data['character_trait_levels']);
	}
	if (!empty($text_data)){
		$tmp = array();
		foreach ($text_data as $ar){
			$tmp = array_replace($tmp, $ar);
		}
		$text_data = $tmp;
		echo '
DB_TEXT = $.extend(typeof DB_TEXT === "undefined" ? {} : DB_TEXT, ', json_encode($text_data), ')';
	}
	echo '
})()';
}
// lua
else{
	echo '
local ', implode(',', $left), ' = ', implode(',', $right),'
return ', my_print($tables, "\t", $mini);
}











