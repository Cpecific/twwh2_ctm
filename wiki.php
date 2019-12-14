
<html>
<head>
	<link rel="stylesheet" href="style/common.css" />
	<link rel="stylesheet" href="style/tooltip.css" />
	<link rel="stylesheet" href="style/wiki.css" />
	
	<script src="js/jquery-3.1.1.min.js"></script>
	<script src="js/jquery-pub-sub.js"></script>
	<script src="js/SuperScript.js"></script>
	<script src="js/common.js"></script>
	<script src="js/popup.js"></script>
	<script src="js/__tables.js"></script>
	<script src="js/__isenhart.js"></script>
	<script src="js/wiki.js"></script>
</head>
<body>
<script>$.publish('bodyShow')</script>
<script>
(function(){

var wiki = new WIKI({
	el: document.body
})

function Check(t_data, at_data, array){
	for (var i in t_data[5]){
		var level = t_data[5][i]
		for (var j in level[3]){
			var effect = level[3][j]
			var key = tbl_effects[ effect[0] ][0]
			if (typeof array[ key ] !== 'undefined'){ return true }
		}
	}
	if (at_data){
		for (var i in at_data[5]){
			var level = at_data[5][i]
			for (var j in level[3]){
				var effect = level[3][j]
				var key = tbl_effects[ effect[0] ][0]
				if (typeof array[ key ] !== 'undefined'){ return true }
			}
		}
	}
	return false
}
function Merge(a){
	var arr = {}
	a.each(function(b){
		var tbl = wiki.keyed[ b[0] ]
		if (!tbl){
			// console.error('NO TABLE', b[0])
			return
		}
		b[1].each(function(bv){
			if (typeof tbl[ bv ] === 'undefined'){
				// console.error('NOT FOUND', b[0], bv)
				return
			}
			tbl[ bv ].each(function(_, effect){
				arr[ effect ] = true
			})
		})
	})
	return arr
}

var filters = []
// Battle
/*;(function(){
	var tbl = Merge([
		['effect_bonus_value_basic_junction', [
			'can_fight_night_battles',
			'can_fight_night_battles_naval',
			'character_situation_land_ambush',
			'character_situation_land_amphibious_attack',
			'character_situation_land_attack',
			'character_situation_land_defence',
			'character_situation_land_siege_attack',
			'character_situation_sea_attack',
			'character_situation_sea_defence',
			'garrison_walls_15m',
			'garrison_walls_8m',
			'invincible_cavalry_during_withdrawl',
			'military_force_enable_assault_without_equipment',
			'siege_equipment_hit_points_mod',
			'tower_damage_mod',
			'tower_reload_mod',
			'unit_starting_fatigue_state'
		]],
		['effect_bonus_value_battle_context_army_special_ability_junctions', [
			'enable'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'unit_starting_fatigue_state',
			'xp_gain_rate_mod'
		]],
		['effect_bonus_value_military_force_ability_junctions', [
			'disable',
			'enable',
			'uses_mod'
		]],
		['effect_bonus_value_missile_weapon_junctions', [
			'enable'
		]],
		['effect_bonus_value_siege_item_junctions', [
			'siege_item_allowed',
			'siege_item_disabled',
			'siege_item_enabled',
			'siege_item_number_of_vehicles_per_construction_item'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'minimum_xp_gain_from_eligible_battle',
			'unit_starting_fatigue_state'
		]]
	])
	filters.push([
		'Battle',
		'melee.png',
		tbl
	])
})()*/
// Armour
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'armour_mod',
			'unit_stat_bonus_armour'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'armour_mod'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'armour_mod'
		]]
	]
	filters.push([
		'Armour',
		'armour',
		tbl,
		'unit_stat_localisations_onscreen_name_stat_armour'
	])
})()
// Leadership
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'military_force_morale_foriegn_soil_mod',
			'military_force_morale_idle_mod',
			'military_force_morale_modifier_animosity',
			'military_force_morale_modifier_buildings',
			'military_force_morale_modifier_buildings_ai_mod',
			'military_force_morale_modifier_casualties',
			'military_force_morale_modifier_characters',
			'military_force_morale_modifier_civil_war',
			'military_force_morale_modifier_faction',
			'military_force_morale_modifier_food',
			'military_force_morale_modifier_forced_march_stance',
			'military_force_morale_modifier_foreign_territory',
			'military_force_morale_modifier_foreign_waters',
			'military_force_morale_modifier_fortify_stance',
			'military_force_morale_modifier_garrison_stance',
			'military_force_morale_modifier_guarded_navy',
			'military_force_morale_modifier_horde_in_fighting',
			'military_force_morale_modifier_military_force',
			'military_force_morale_modifier_offices',
			'military_force_morale_modifier_own_territory',
			'military_force_morale_modifier_own_waters',
			'military_force_morale_modifier_patrol_stance',
			'military_force_morale_modifier_plague',
			'military_force_morale_modifier_politics',
			'military_force_morale_modifier_raid_stance',
			'military_force_morale_modifier_raiding',
			'military_force_morale_modifier_settle_stance',
			'military_force_morale_modifier_tax',
			'military_force_morale_modifier_technologies',
			'morale',
			'morale_bonus_for_concurrent_wars',
			'morale_land',
			'morale_naval',
			'unit_stat_bonus_morale_mod',
			'unit_winter_morale_bonus'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'general_aoe_mod',
			'general_aoe_morale_effect_mod',
			'morale'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'general_aoe_mod',
			'general_aoe_morale_effect_mod',
			'morale'
		]]
	]
	filters.push([
		'Leadership',
		'morale_character',
		tbl,
		'unit_stat_localisations_onscreen_name_stat_morale'
	])
})()
// Speed
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'mod_ship_movement_battle',
			'mod_ship_movement_battle_top_gallants'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'mod_land_movement_battle',
			'movement_mod'
		]]
	]
	filters.push([
		'Speed',
		'battle_movement',
		tbl,
		'unit_stat_localisations_onscreen_name_scalar_speed'
	])
})()
// Melee attack
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'melee_attack_mod',
			'melee_damage_ap_mod_add',
			'melee_damage_ap_mod_mult',
			'melee_damage_mod_add',
			'melee_damage_mod_mult',
			'splash_attack_power_mod_add',
			'unit_stat_bonus_attack'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'melee_attack_mod'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'melee_attack_mod'
		]]
	]
	filters.push([
		'Melee attack',
		'melee',
		tbl,
		'unit_stat_localisations_onscreen_name_stat_melee_attack'
	])
})()
// Melee defence
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'melee_defence_mod',
			'unit_stat_bonus_defence',
			'unit_stat_bonus_hull_armour'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'melee_defence_mod'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'melee_defence_mod'
		]]
	]
	filters.push([
		'Melee defence',
		'defence',
		tbl,
		'unit_stat_localisations_onscreen_name_stat_melee_defence'
	])
})()
// Weapon strength
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'melee_damage_ap_mod_add',
			'melee_damage_ap_mod_mult',
			'melee_damage_mod_add',
			'melee_damage_mod_mult',
			'splash_attack_power_mod_add',
			'unit_flaming_attack_mod',
			'unit_is_magical',
			'unit_stat_bonus_weapon'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'melee_damage_ap_mod_add',
			'melee_damage_ap_mod_mult',
			'melee_damage_mod_add',
			'melee_damage_mod_mult',
			'splash_attack_power_mod_add',
			'unit_flaming_attack_mod'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'melee_damage_ap_mod_add',
			'melee_damage_ap_mod_mult',
			'melee_damage_mod_add',
			'melee_damage_mod_mult',
			'splash_attack_power_mod_add',
			'unit_flaming_attack_mod'
		]]
	]
	filters.push([
		'Weapon strength',
		'weapon_damage',
		tbl,
		'unit_stat_localisations_onscreen_name_stat_weapon_damage'
	])
})()
// Charge bonus
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'charge_bonus_for_land_units',
			'unit_stat_bonus_charge_bonus_mod'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'charge_bonus',
			'ship_ram_damage_mod'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'charge_add',
			'charge_bonus',
			'ship_ram_damage_mod'
		]]
	]
	filters.push([
		'Charge bonus',
		'charge_character',
		tbl,
		'unit_stat_localisations_onscreen_name_stat_charge_bonus'
	])
})()
// Range
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'ammo_mod',
			'missile_damage_ap_mod_mult',
			'missile_damage_mod_mult',
			'mod_misfire_land',
			'mod_misfire_naval',
			'mod_reload_land',
			'mod_reload_naval',
			'unit_stat_bonus_accuracy_mod',
			'unit_stat_bonus_ammunition'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'ammo_mod',
			'missile_damage_ap_mod_mult',
			'missile_damage_mod_mult',
			'range_mod',
			'reload_mod'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'ammo_mod',
			'missile_damage_ap_mod_mult',
			'missile_damage_mod_mult',
			'range_mod',
			'reload'
		]]
	]
	filters.push([
		'Range',
		'accuracy',
		tbl,
		'uied_component_texts_localised_string_button_missile_attack_Tooltip_280064'
		// 'unit_stat_localisations_onscreen_name_stat_missile_damage_base'
	])
})()
// Resistance and health
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'damage_resistance_all_mod',
			'damage_resistance_flame_mod',
			'damage_resistance_magic_mod',
			'damage_resistance_missile_mod',
			'damage_resistance_physical_mod',
			'general_bodyguard_size_mod', // in wh hit points is equivalent
			'unit_fatigue_resistance_mod',
			'unit_stat_bonus_hull_health',
			'unit_stat_bonus_shield'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'ship_health_mod',
			'unit_damage_resistance_all_mod',
			'unit_damage_resistance_flame_mod',
			'unit_damage_resistance_magic_mod',
			'unit_damage_resistance_missile_mod',
			'unit_damage_resistance_physical_mod',
			'unit_fatigue_resistance_mod'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'ship_health_mod',
			'unit_damage_resistance_all_mod',
			'unit_damage_resistance_flame_mod',
			'unit_damage_resistance_magic_mod',
			'unit_damage_resistance_missile_mod',
			'unit_damage_resistance_physical_mod',
			'unit_fatigue_resistance_mod'
		]]
	]
	filters.push([
		'Resistance and health',
		'resistance_ward_save',
		tbl,
		'unit_stat_localisations_onscreen_name_stat_resistance_all'
	])
})()
// Bonus vs
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'damage_vs_cavalry',
			'damage_vs_infantry',
			'damage_vs_large_entities'
		]],
		['effect_bonus_value_basic_junction', [
			'damage_vs_cavalry',
			'damage_vs_infantry',
			'damage_vs_large_entities',
		]],
		['effect_bonus_value_battle_context_junctions', [
			'damage_vs_cavalry',
			'damage_vs_infantry',
			'damage_vs_large_entities'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'damage_vs_cavalry',
			'damage_vs_infantry',
			'damage_vs_large_entities'
		]]
	]
	filters.push([
		'Bonus vs',
		'bonus_vs_large',
		tbl,
		'uied_component_texts_localised_string_tx_bonuses_NewState_Text_7f002c'
		// 'unit_stat_localisations_onscreen_name_stat_bonus_vs_large'
	])
})()
// Magic and abilities
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'autoresolver_spell_modifier',
			'max_winds_of_magic_depletion_mod',
			'max_winds_of_magic_depletion_mod_agent_actions',
			'max_winds_of_magic_depletion_mod_character',
			'max_winds_of_magic_depletion_mod_events',
			'max_winds_of_magic_depletion_mod_items',
			'max_winds_of_magic_depletion_mod_region',
			'max_winds_of_magic_depletion_mod_stances',
			'miscast_chance_mod',
			'starting_winds_of_magic_mod'
		]],
		['effect_bonus_value_battle_context_junctions', [
			'miscast_chance_mod'
		]],
		['effect_bonus_value_battle_context_unit_ability_junctions', [
			'enable'
		]],
		['effect_bonus_value_unit_ability_junctions', [
			'cost_mod',
			'cost_percentage_mod',
			'disable',
			'disable_overchage',
			'enable',
			'enable_overchage',
			'miscast_percentage_mod',
			'recharge_mod',
			'uses_mod'
		]],
		['effect_bonus_value_unit_set_unit_ability_junctions', [
			'cost_mod',
			'cost_percentage_mod',
			'disable',
			'disable_overchage',
			'enable',
			'enable_overchage',
			'miscast_percentage_mod',
			'recharge_mod',
			'uses_mod'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'miscast_chance_mod'
		]]
	]
	filters.push([
		'Magic and abilities',
		'magic',
		tbl,
		'uied_component_texts_localised_string_tab_title_active_Text_1f005b'
	])
})()
// Attributes
;(function(){
	tbl = [
		// no entries
		['effect_bonus_value_basic_junction', [
			'military_force_ability_strength'
		]],
		['effect_bonus_value_special_ability_phase_record_junctions', [
			'active'
		]],
		['effect_bonus_value_battle_context_unit_attribute_junctions', [
			'enable'
		]],
		['effect_bonus_value_unit_attribute_junctions', [
			'enable'
		]],
		['effect_bonus_value_unit_set_unit_attribute_junctions', [
			'enable'
		]],
	]
	filters.push([
		'Attributes',
		'attribute_fatigue_immune',
		tbl,
		['encyclopedia_template_strings_text_attributes', // was working at some point in vortex
		// 'encyclopedia_pages_title_6004c_tw_game_guide_wh_battle_gameplay_attributes',
		'uied_component_texts_localised_string_tx_attributes_NewState_Text_480072']
	])
})()
// Post battle
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'captives_taken_mod',
			'income_per_10_percent_settlement_destruction',
			'income_per_man_killed_in_battle',
			'looting_increase',
			'looting_mod',
			'occupation_decision_colonise_cost_mod',
			'post_battle_ancillary_drop_chance_mod',
			'post_battle_ancillary_steal_chance_mod',
			'post_battle_loot_mod',
			'post_battle_unit_saving_chance_mod',
			'post_battle_unit_saving_replenishment_mod',
			'provided_battle_loot_mod',
			'provided_captives_taken_mod',
			'provided_sacking_loot_mod',
			'provided_settlement_loot_mod',
			'razing_mod',
			'sacking_mod'
		]],
		['effect_bonus_value_subculture_junctions', [
			'sacking_mod'
		]]
	]
	filters.push([
		'Post battle',
		'item_ability',
		tbl,
		'campaign_localised_strings_string_ritual_currency_factor_BATTLE'
	])
})()
// Income
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'extra_income_from_vassals',
			'financial_gain_from_sujugation',
			'gdp_animal_husbandry',
			'gdp_entertainment',
			'gdp_farming',
			'gdp_fertility',
			'gdp_fertility_agriculture',
			'gdp_fertility_livestock',
			'gdp_land_trade',
			'gdp_learning',
			'gdp_local_trade',
			'gdp_manufacture',
			'gdp_mining',
			'gdp_mod_all',
			'gdp_mod_all_horde_infighting_per_army_value',
			'gdp_mod_animal_husbandry',
			'gdp_mod_entertainment',
			'gdp_mod_farming',
			'gdp_mod_fertility',
			'gdp_mod_fertility_agriculture',
			'gdp_mod_fertility_livestock',
			'gdp_mod_land_trade',
			'gdp_mod_learning',
			'gdp_mod_local_trade',
			'gdp_mod_manufacture',
			'gdp_mod_mining',
			'gdp_mod_other',
			'gdp_mod_poor_fertility_bonus',
			'gdp_mod_sea_trade',
			'gdp_mod_slaves',
			'gdp_mod_subsistence',
			'gdp_other',
			'gdp_sea_trade',
			'gdp_slaves',
			'gdp_subsistence',
			'percentage_of_region_gdp_leeching',
			'percentage_of_region_gdp_mod_leeching',
			'province_slaves_reduction_per_turn_modifier',
			'province_slaves_slave_gap_mod_all_income_bonus_modifier',
			'raid_income_mod',
			'raid_radius_mod',
			'tax_bonus_building',
			'tax_bonus_events',
			'tax_bonus_minister',
			'tax_bonus_technology',
			'upkeep_cost_mod_land_all',
			'upkeep_cost_mod_naval_all'
		]],
		['effect_bonus_value_building_set_junctions', [
			'mod_gdp'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'no_upkeep',
			'upkeep_mod'
		]],
		['effect_bonus_value_subculture_junctions', [
			'raid_income_mod'
		]]
	]
	filters.push([
		'Income',
		'income', // treasury
		tbl,
		'random_localisation_strings_string_finance_header_income'
	])
})()
// Recruitment
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'ercenary_province_pool_cap_mod',
			'faction_mercenary_pool_cost_mod',
			'faction_mercenary_pool_replenishment_mod',
			'global_recruit_time_mod_land',
			'global_recruit_time_mod_naval',
			'mercenary_cost_mod',
			'mercenary_faction_pool_cap_mod',
			'mercenary_faction_unit_exp_bonus',
			'mercenary_province_pool_replenishment_mod',
			'mercenary_province_unit_exp_bonus',
			'naval_recruitment_points',
			'recruit_time_mod_land',
			'recruit_time_mod_naval',
			'recruitment_mod_cost_land_all',
			'recruitment_mod_cost_naval_all',
			'recruitment_points',
			'recruitment_points_home_region'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'cost_mod',
			'food_cost_mod',
			'global_recruit_time_mod',
			'recruit_time_mod',
			'unit_xp_mod',
			'vampiric_mercenary_recruitment_capacity',
			'vampiric_mercenary_recruitment_replenishment_rate'
		]]
	]
	filters.push([
		'Recruitment',
		'experience',
		tbl,
		'event_feed_subcategories_subcategory_title_wh_event_subcategory_military_unit_recruited'
	])
})()
// Replenishment
;(function(){
	var tbl = [
		['effect_bonus_value_attrition_record_junctions', [
			'active',
			'damage_mod',
			'immunity'
		]],
		['effect_bonus_value_basic_junction', [
			'attrition_difficulty_addition',
			'cancel_all_replenishment_in_region',
			'enable_desertion_attrition_for_own_forces_in_region',
			'max_replenishment_pct_override',
			'plague_breakout_mod',
			'plague_infectivity_mod',
			'plague_lifetime_mod',
			'provided_replenishment_percentage_bonus',
			'replenishment_partial_bankruptcy_mod',
			'replenishment_percentage_bonus',
			'unrestricted_replenishment_bonus'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'replenishment_percentage_bonus'
		]]
	]
	filters.push([
		'Replenishment',
		'disaster',
		tbl,
		'uied_component_texts_localised_string_dy_replenish_Tooltip_6d000b'
	])
})()
// Public order
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'agent_create_disaffection_in_region',
			'agent_create_disorder_in_region',
			'ai_region_resistance_modifier',
			'attribute_authority_mod',
			'attribute_subterfuge_mod',
			'attribute_zeal_mod',
			'character_public_order_decrease_mod',
			'character_public_order_increase_mod',
			'education_happy_mod',
			'gentleman_happiness_bonus',
			'happiness_character_trait_or_ancillary',
			'happiness_events_factional',
			'happiness_events_regional',
			'happiness_ministerial_position',
			'happiness_mod_religious_unrest',
			'happiness_religious_unrest_added',
			'province_public_order_attitude_factor',
			'province_public_order_happiness_baseline_factor',
			'province_public_order_happiness_building_negative_factor',
			'province_public_order_happiness_building_positive_factor',
			'province_public_order_happiness_character_factor',
			'province_public_order_happiness_events_factor',
			'province_public_order_happiness_faction_factor',
			'province_public_order_happiness_factor',
			'province_public_order_happiness_family_power',
			'province_public_order_happiness_food_factor',
			'province_public_order_happiness_military_activity_factor',
			'province_public_order_happiness_per_war_factor',
			'province_public_order_happiness_plague',
			'province_public_order_happiness_tax_factor',
			'province_public_order_happiness_technology_factor',
			'province_public_order_repression_threshold_mod',
			'province_public_order_satisfaction_threshold_mod',
			'province_slaves_slave_public_order_modifier'
		]],
		['effect_bonus_value_religion_junction', [
			'faction_public_order',
			'public_order'
		]]
	]
	filters.push([
		'Public order',
		'public_order',
		tbl,
		'uied_component_texts_localised_string_region_happiness_Tooltip_3006c'
	])
})()
// Growth
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'additional_growth_for_migration',
			'additional_growth_for_raze',
			'additional_growth_for_resettle',
			'cancel_all_growth_in_region',
			'military_force_development_growth_characters',
			'military_force_development_growth_core',
			'military_force_development_growth_events',
			'military_force_development_growth_stance',
			'military_force_development_growth_stance_settled',
			'military_force_development_growth_tax',
			'military_force_development_growth_tech',
			'province_cancel_all_growth_in_province',
			'province_development_growth_added_armies',
			'province_development_growth_added_building',
			'province_development_growth_added_character',
			'province_development_growth_added_faction',
			'province_development_growth_added_food',
			'province_development_growth_added_other',
			'province_development_growth_added_province',
			'province_development_growth_added_taxes',
			'province_development_growth_added_tech_per_region_in_province',
			'province_development_growth_added_technology'
		]]
	]
	filters.push([
		'Growth',
		'growth',
		tbl,
		'uied_component_texts_localised_string_region_growth_Tooltip_6d005c'
	])
})()
// Campaign
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'character_gravitas_per_turn',
			'faction_level_points',
			'foreign_slot_building_denies_line_of_sight',
			'general_admiral_action_point_bonus',
			'general_start_of_turn_action_point_penalty',
			'immortal',
			'line_of_sight_extension',
			'mod_land_movement_campaign',
			'mod_ship_movement_campaign',
			'region_line_of_sight_bonus',
			'reinforcement_radius_bonus',
			'research_cost_mod',
			'research_points',
			'research_rate_mod',
			'roads',
			'siege_time_mod',
			'siege_time_reduction_for_attacker',
			'stance_ap_cost_refundable_on_off',
			'stance_ap_percentage_cost',
			'stealth_mod',
			'treasure_hunt_success_chance_percentage_mod',
			'tunnel_interception_chance',
			'tunnel_interception_evasion_chance',
			'unit_xp_mod_land',
			'unit_xp_mod_naval',
			'unit_xp_mod_training',
			'unit_xp_mod_training_garrison'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'training_mod',
			'unit_xp_mod_training'
		]]
	]
	filters.push([
		'Campaign',
		'campaign_movement',
		tbl,
		'uied_component_texts_localised_string_tab_title_NewState_Text_160028'
	])
})()
// Characters
;(function(){
	var tbl = [
		['effect_bonus_value_id_action_results_additional_outcomes_junctions', [
			'action_results_additional_outcome_record_value_addition',
			'action_results_additional_outcome_record_value_pct_mod'
		]],
		['effect_bonus_value_agent_junction', [
			'action_cost_mod',
			'availability',
			'cap',
			'critical_failure_mod',
			'critical_failure_mod_opponent',
			'critical_success_mod',
			'critical_success_mod_opponent',
			'opportune_failure_mod',
			'opportune_failure_mod_opponent',
			'recruit_cost_mod',
			'recruitment_level',
			'success_mod',
			'success_mod_opponent'
		]],
		['effect_bonus_value_agent_action_record_junctions', [
			'active',
			'cost_mod',
			'critical_failure_mod',
			'critical_failure_mod_opponent',
			'critical_success_mod',
			'critical_success_mod_opponent',
			'disabled',
			'opportune_failure_mod',
			'opportune_failure_mod_opponent',
			'success_mod',
			'success_mod_opponent'
		]],
		['effect_bonus_value_agent_subtype_junctions', [
			'availability',
			'cap',
			'loyalty_initial_mod' // is this feasible?
		]],
		['effect_bonus_value_basic_junction', [
			'agent_action_failure_cost_mod',
			'agent_create_disaffection_in_region',
			'agent_create_disorder_in_region',
			'agent_religion_conversion',
			'agent_success_chance_mod',
			'loyalty',
			'loyalty_initial_mod',
			'loyalty_mod_character',
			'loyalty_mod_faction',
			'loyalty_mod_fame',
			'loyalty_recruitment_accuracy_mod',
			'percentage_of_xp_to_give_other_characters_of_same_type',
			'percentage_of_xp_to_steal_from_characters_of_same_type',
			'recuperation_time_mod'
		]],
		['effect_bonus_value_faction_junctions', [
			'loyalty_initial_mod'
		]],
		['effect_bonus_value_loyalty_event_junctions', [
			'amount_mod',
			'chance_mod',
			'threshold_mod'
		]]
	]
	filters.push([
		'Characters',
		'agent',
		tbl,
		'uied_component_texts_localised_string_header_characters_normal_Text_6f0045'
	])
})()
// Construction
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'admin_cost_mod',
			'building_conversion_cost_mod',
			'building_conversion_time_mod',
			'building_cost_mod',
			'building_slum_creation_time_mod',
			'building_upkeep_mod',
			'military_force_building_build_time_mod',
			'military_force_building_conversion_cost_mod',
			'military_force_building_conversion_dev_point_cost_mod',
			'military_force_building_conversion_time_mod',
			'military_force_building_cost_mod',
			'military_force_building_dev_point_cost_mod',
			'military_force_building_upkeep_mod',
			'mod_build_time'
		]],
		['effect_bonus_value_building_set_junctions', [
			'add_conversion_time',
			'mod_build_time',
			'mod_conversion_cost',
			'mod_conversion_time',
			'mod_cost',
			'mod_food_cost',
			'num_buildings_to_spawn_for_new_horde_army'
		]]
	]
	filters.push([
		'Construction',
		'construction',
		tbl,
		'uied_component_texts_localised_string_construction_button_Tooltip_a0037'
	])
})()
// Trade
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'region_gdp_trade_piracy_effect_mod',
			'resource_export_enable',
			'resource_production_mod',
			'trade_income_mod',
			'trade_node_commodity_mod',
			'trade_route_all_mod_growth_rate',
			'trade_route_cap_sea'
		]],
		['effect_bonus_value_building_set_junctions', [
			'mod_resource_production'
		]],
		['effect_bonus_value_resource_junction', [
			'production'
		]]
	]
	filters.push([
		'Trade',
		'trade_agreement',
		tbl,
		'uied_component_texts_localised_string_tx_trade_NewState_Text_160050'
	])
})()
// Religion
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'agent_religion_conversion',
			'state_conversion_from_technology',
			'state_religion_conversion_bonus',
			'state_religion_conversion_bonus_minister'
		]],
		['effect_bonus_value_religion_junction', [
			'conversion',
			'conversion_events',
			'conversion_when_state',
			'osmotic_conversion',
			'osmotic_conversion_when_state'
		]]
	]
	filters.push([
		'Religion',
		'religion',
		tbl,
		'uied_component_texts_localised_string_region_religion_Tooltip_b0036'
	])
})()
// Diplomacy
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'dignitary_dimplomatic_gift_cost_reduction',
			'diplomacy_bonus_enlightenment',
			'diplomacy_bonus_faction_leader',
			'diplomatic_relations_influence_cost_mod',
			'political_action_money_cost_mod',
			'political_action_power_cost_mod',
			'political_event_chance_mod_overall',
			'political_event_chance_mod_per_turn',
			'political_gravitas_mod',
			'political_gravitas_per_situation_mod',
			'political_support_per_turn'
		]],
		['effect_bonus_value_faction_junctions', [
			'diplomatic_mod',
			'diplomatic_mod_imperium',
			'diplomatic_mod_other',
			'diplomatic_mod_ritual',
			'diplomatic_mod_tech'
		]],
		['effect_bonus_value_religion_junction', [
			'diplomatic_mod',
			'diplomatic_mod_imperium',
			'diplomatic_mod_other',
			'diplomatic_mod_ritual',
			'diplomatic_mod_tech'
		]],
		['effect_bonus_value_subculture_junctions', [
			'diplomatic_mod',
			'diplomatic_mod_imperium',
			'diplomatic_mod_other',
			'diplomatic_mod_ritual',
			'diplomatic_mod_tech'
		]]
	]
	filters.push([
		'Diplomacy',
		'diplomacy',
		tbl,
		'uied_component_texts_localised_string_tx_diplomacy_NewState_Text_b0043'
	])
})()
// Ambush
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'ambush_chance_of_success_attack_bonus',
			'ambush_chance_of_success_defence_bonus'
		]]
	]
	filters.push([
		'Ambush',
		'icon_effects_ambush',
		tbl,
		'uied_component_texts_localised_string_button_ambush_Tooltip_700050'
	])
})()
// Capacity
;(function(){
	var tbl = [
		['effect_bonus_value_agent_junction', [
			'cap'
		]],
		['effect_bonus_value_basic_junction', [
			'army_cap_bonus',
			'navy_cap_bonus'
		]],
		['effect_bonus_value_ids_unit_sets', [
			'unit_cap'
		]]
	]
	filters.push([
		'Capacity',
		'unit_capacity',
		tbl,
		'uied_component_texts_localised_string_tx_label_NewState_Text_100024'
		// 'uied_component_texts_localised_string_unit_cap_Tooltip_200035'
	])
})()
// Pooled resources
;(function(){
	var tbl = [
		['effect_bonus_value_basic_junction', [
			'food_comsumption_horde_infighting',
			'food_comsumption_horde_infighting_per_army_value',
			'food_consumption_armies',
			'food_consumption_base_food',
			'food_consumption_buildings',
			'food_consumption_characters',
			'food_consumption_edicts',
			'food_consumption_events',
			'food_consumption_settle_stance_enemy_territory',
			'food_consumption_settle_stance_major_treaty_territory',
			'food_consumption_settle_stance_minor_treaty_territory',
			'food_consumption_settle_stance_neutral_territory',
			'food_consumption_settle_stance_vassal_territory',
			'food_consumption_technologies',
			'food_production_armies',
			'food_production_base_food',
			'food_production_buildings',
			'food_production_characters',
			'food_production_edicts',
			'food_production_events',
			'food_production_fertility',
			'food_production_settle_stance_enemy_territory',
			'food_production_settle_stance_major_treaty_territory',
			'food_production_settle_stance_minor_treaty_territory',
			'food_production_settle_stance_neutral_territory',
			'food_production_settle_stance_vassal_territory',
			'food_production_technologies',
			'influence',
			'region_horde_food_consumption'
		]],
		['effect_bonus_value_pooled_resource_factor_junctions', [
			'base_amount',
			'percentage_multiplier_mod'
		]],
		['effect_bonus_value_pooled_resource_junctions', [
			'maximum_mod',
			'minimum_mod',
			'percentage_multiplier_mod'
		]]
	]
	filters.push([
		'Pooled resources',
		'skaven_food',
		tbl,
		'ui_text_replacements_localised_text_hp_campaign_title_resources'
	])
})()
// Rituals
;(function(){
	var tbl = [
		['effect_bonus_value_ritual_junctions', [
			'disable',
			'enable',
			'percentage_cost_mod',
			'slave_cost_mod',
			'slave_percentage_cost_mod'
		]]
	]
	filters.push([
		'Rituals',
		'icon_ritual_currency_hef_bundle',
		tbl,
		'uied_component_texts_localised_string_button_rituals_Tooltip_7d0027'
	])
})()


// lua export
if (0){
var res = ''
filters.each(function(filter){
	res += '-- '+ filter[0] +'\n\
do\n\
	local tbl = Merge({\n\
	'
	filter[2].each(function(a, idx){
		res +=	'	'+ a[0] +' = {'
		if (a[1].length){
			res += '\n\
			\''+ a[1].join('\',\n\
			\'') +'\'\n\
		'
		}
		res += '}'
		if (idx + 1 < filter[2].length){ res += ',' }
		res += '\n\
	'
	})
	res += '})\n\
	table.insert(filters, {\n\
		'+ (typeof filter[3] === 'string' ? '{\''+ filter[3] +'\'}' : '{\''+ filter[3].join('\',\n\
		\'') +'\'}') +',\n\
		\''+ filter[1] +'\',\n\
		function(t_key, t_data, at_key, at_data)\n\
			return Check(t_data, at_data, tbl)\n\
		end\n\
	})\n\
end\n'
})
console.log(res)
}


filters.each(function(filter, i){
	var tbl = filter[2]
	if (typeof tbl === 'function'){ return }
	tbl = Merge(tbl)
	filter[2] = function(t_key, t_data, at_key, at_data){
		return Check(
			t_data,
			at_data,
			tbl)
	}
	filters[ i ] = filter
})

// Excluded
;(function(){
	filters.push([
		'Excluded',
		'snipe',
		function(t_key, t_data, at_key, at_data){
			for (var i = filters.length - 2; i >= 0; --i){
				if (filters[ i ][2](t_key, t_data, at_key, at_data)){
					return false
				}
			}
			return true
		}
	])
})()

wiki.SetFilters(filters)

})()
</script>
</body>
</html>