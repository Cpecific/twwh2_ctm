(function(){

var tbl_trait_keys = []
var TRAITS_DATA = {}

var TraitContainer = function(q){
	var trait = this.trait = new Trait({
		data: TRAITS_DATA[ q.trait_key_idx ],
		trait_key_idx: q.trait_key_idx
	}, this)
	trait.Build()
	this.el = trait.el
	var data = trait.data
	if (data.anti_trait){
		var atrait = this.atrait = new Trait({
			data: TRAITS_DATA[ data.anti_trait ],
			trait_key_idx: data.anti_trait
		}, this)
		atrait.BuildLevels(trait)
		trait.anti_trait = atrait
	}
	trait.BuildLevels(trait)
	trait.BuildFinal()
	trait.SetCur(0)
}
extProt(
	TraitContainer.prototype
)
var Trait = function(q, container){
	this.container = container
	var data = q.data.slice().toKeys(
		'no_going_back_level', 'hidden',
		'precedence', 'icon', 'ui', 'levels',
		'anti_trait', 'trait_key_idx'
	)
	// data.trait_key = tbl_trait_keys[ q.trait_key_idx ]
	data.trait_key = tbl_trait_keys[ data.trait_key_idx ]
	data.levels = data.levels.slice()
	// console.log(data, q.data)
	data.levels.each(function(level, idx){
		level = level.toKeys(
			'level', 'level_key', 'threshold_points',
			'effects'
		)
		level.effects = level.effects.slice()
		level.effects.each(function(effect, jdx){
			level.effects[ jdx ] = effect.toKeys('effect', 'scope', 'value')
		})
		data.levels[ idx ] = level
	})
	this.data = data
	this.levels = []
}
function ParseText(text){
	text = text.replace(/\[\[img\:([^\]]*)\]\][^\[]*\[\[\/img\]\]/, function(){
		var icon = arguments[1]
		// var src = icon
		// if (src.substr(0, 5) === 'icon_'){
			// src = src.substr(5)
		// }
		return '<img src="game/ui/skins/default/'+ icon +'.png" icon="'+ icon +'" />'
	})
	text = text.replace(/\[\[col\:([^\]]*)\]\]([^\[]*)\[\[\/col\]\]/, '<span style="color: $1;">$2</span>')
	return text
}
function GetLevelTooltipContent(level){
	var level_key = level.level_key //[1]
	var onscreen_name = DB_TEXT['character_trait_levels_onscreen_name_'+ level_key]
	if (typeof onscreen_name === 'undefined'){ onscreen_name = '<span style="color: red;">onscreen_name</span>' }
	onscreen_name += ' ('+ level.threshold_points +')'
	
	var colour_text = DB_TEXT['character_trait_levels_colour_text_'+ level_key]
	if (typeof colour_text === 'undefined'){ colour_text = '<span style="color: red;">colour_text</span>' }
	
	var explanation_text = DB_TEXT['character_trait_levels_explanation_text_'+ level_key]
	if (typeof explanation_text === 'undefined'){ explanation_text = '<span style="color: red;">explanation_text</span>' }
	else{ explanation_text = ParseText(explanation_text) }
	
	var cont = []
	// dy_title
	cont.push(E('div', null).h(onscreen_name))
	// description_window
	cont.push(E('div', { class: 'effects-tooltip-colour-text' }).h(colour_text))
	// dy_explanation
	cont.push(E('div', { class: 'effects-tooltip-explanation-text' }).h(explanation_text))
	// effects_list
	var list
	cont.push(list = E('div', { class: 'brw' }))
	
	// индикатор сортировки
	if (!level._sorted){
		level._sorted = true
		level.effects.sort(function(a, b){
			return tbl_effects[ a.effect ][2] - tbl_effects[ b.effect ][2]
		})
	}
	
	level.effects.each(function(e_property, idx){
		var e_data = tbl_effects[ e_property.effect ]
		// console.log(e_data)
		
		// if priority = 0, then it's hidden (or if images are nil)
		if (e_data[2] == 0){
			// return
		}
		var scope = tbl_scopes[ e_property.scope ],
			value = e_property.value
		// out('effect = '.. my_print(e_data))
		
		var icon, icon_negative
		var ability = tbl_ebv_unit_abilities[ e_property.effect ]
		if (typeof ability === 'undefined'){
			icon = tbl_strings[ e_data[1] ]
			icon = (icon ? 'game/ui/campaign ui/effect_bundles/'+ icon : '')
			icon_negative = tbl_strings[ e_data[3] ]
			icon_negative = (icon_negative ? 'game/ui/campaign ui/effect_bundles/'+ icon_negative : '')
		}
		else{
			icon = icon_negative = 'game/ui/battle ui/ability_icons/'+ tbl_unit_abilities[ ability ] +'.png'
		}
		
		var my_icon = icon,
			colour = 'lime'
		// positive
		if (e_data[5] != (value >= 0)){
			my_icon = icon_negative
			colour = 'red'
		}
		
		// key
		var text = DB_TEXT['effects_description_'+ e_data[0].toString()]
		
		if (value > 0){ text = text.replace('%+n', '+'+ value.toString()) }
		else{ text = text.replace('%+n', value.toString()) }
		text = text.replace('%n', value.toString())
		
		text += DB_TEXT['campaign_effect_scopes_localised_text_'+ scope]
		text += ' ('+ e_data[2] +')'
		
		var el = E('div', { class: 'effect-cont' })
		if (my_icon){
			icon = my_icon.split('/')
			icon = icon[ icon.length - 1 ]
			icon = icon.substr(0, icon.length - 4)
			el.A(E('img', { 
				class: 'fl',
				src: my_icon,
				icon: icon
			}))
		}
		el
		.A(E('div', { style: 'color: '+ colour +';' }).h(ParseText(text)))
		.A(E('div', { class: 'cl' }))
		
		list.A(el)
	})
	return cont
}
extProt(
	Trait.prototype,
	'Build', function(){
		var self = this
		var data = this.data
		var img
		this.el = E('div', { class: 'fl bra trait' })
		.A(
			E('div', { class: 'trait-dy' })
			.A(
				E('img', { class: 'fl', src: 'game/'+ tbl_icons[ data.icon ][1] })
				.on('error', function(){
					var src = this.ga('src')
					if (src.indexOf('/ui/skins/default/') === -1){
						src = src.split('/')
						this.sa('src', 'game/ui/skins/default/'+ src[ src.length - 1 ])
					}
				})
			)
			.A(this.dy = E('div'))
			.A(E('div', { class: 'cl' }))
		)
		.A(this.bar = E('div', { class: 'fl trait-level-bar' }))
		.A(E('div', { class: 'cl' }))
		.on('click', function(){
			console.log(data)
			if (self.anti_trait){
				console.log(self.anti_trait.data)
			}
		})
	},
	'BuildLevels', function(obj){
		// console.log('BuildLevels', this === obj, obj)
		var self = this
		var data = this.data
		data.levels.each(function(level, idx){
			var cl = (data.ui === 0 ? '0' : (data.ui < 0 ? 'm' : 'p') + Math.min(3, level.level))
			var el = E('div', { class: 'fl trait-level trait-level-'+ cl })
			.on('click', function(){
				self.SetCur(idx)
			})
			.on('mouseenter', function(){
				var content = GetLevelTooltipContent(level)
				var el = Tooltip(this, content).popup.el.children[0]
				el.style['z-index'] = 100
				el.style['top'] = '20px'
				el.CR('bra').CA('effects-tooltip')
				
				F('img', el).each(function(img){
					img.on('error', function(){
						var src = this.ga('src')
						if (src === null){ return }
						if (!this.triedSD){
							this.triedSD = true
							this.sa('src', 'game/ui/skins/default/'+ this.ga('icon') +'.png')
						}
						else if (!this.triedEB){
							this.triedEB = true
							var src = this.ga('icon')
							if (src.substr(0, 5) === 'icon_'){ src = src.substr(5) }
							this.sa('src', 'game/ui/campaign ui/effect_bundles/'+ src +'.png')
						}
						else if (!this.triedUCI){
							this.triedUCI = true
							this.sa('src', 'game/ui/common ui/unit_category_icons/'+ this.ga('icon') +'.png')
						}
						else if (!this.triedUCI_short){
							this.triedUCI_short = true
							var src = this.ga('icon')
							if (src.substr(0, 5) === 'icon_'){
								src = src.substr(5)
								this.sa('src', 'game/ui/common ui/unit_category_icons/'+ src +'.png')
							}
						}
					})
					var src = img.ga('src')
					if (src.indexOf('/default/') !== -1){
						img.triedSD = true
					}
					else if (src.indexOf('/effect_bundles/') !== -1){
						img.triedEB = true
					}
					else if (src.indexOf('/unit_category_icons/') !== -1){
						img.triedUCI = true
					}
					// if (img.ha('src2')){
						// img.sa('src', img.ga('src2'))
						// img.ra('src2')
					// }
				})
			})
			.on('mouseleave', function(){
				if (!this.__tooltip){ return }
				this.__tooltip.popup.Remove()
			})
			self.levels.push(el)
			obj.bar.A(el)
		})
		if (obj !== this){
			obj.bar.A(E('div', { class: 'fl', style: 'width: 8px; height: 20px;'}))
		}
	},
	'BuildFinal', function(){
		this.bar.A(E('div', { class: 'cl' }))
	},
	'SetCur', function(idx){
		var obj = this.container.trait
		;[].slice.call(obj.bar.children).CR('trait-level-cur')
		this.levels[ idx ].CA('trait-level-cur')
		var level_key = this.data.levels[ idx ].level_key //[1]
		var onscreen_name = DB_TEXT['character_trait_levels_onscreen_name_'+ level_key]
		var name = (obj.data.hidden ? '(hidden) ' : '')
		name += (typeof onscreen_name === 'undefined' ? '<span style="color: red;">'+ level_key +'</span>' : onscreen_name)
		obj.dy.h(name)
	}
)

if (1){
table = {
	insert: function(a, b){
		a.push(b)
	}
}
function in_array(a, b){ return b.has(a) }
function array_search(a, b){ return b.index(a) }
var array_keys = function(a){ return [null].concat(a.keys()) }
var ksort = function(tbl, in_order){
	var index = []
	tbl.each(function(_, key){
		index.push(key)
    })
	index.sort()
	if (in_order === -1){
		index = index.reverse()
	}
	var new_tbl = []
	index.each(function(key){
		new_tbl.push(tbl[ key ])
	})
    return new_tbl
}

var tbl_forbidden_subtypes = {}
var tbl_scopes = []
var tbl_trait_keys = []
var tbl_icons = []
var tbl_icons_idx = {}
var tbl_strings = []
tbl_effects = []
tbl_effects_idx = {}
var tbl_ebv_unit_abilities = {}
var tbl_unit_abilities = {}
var frontend_factions
var TRAITS_DATA = []
var lords_arr = {},
	def_lords_idx = {},
	def_traits_idx = {}
var TRAITS_IT = [] // iteratable
var DISABLED_TRAITS = {}

var tables_info = {
	agent_subtypes: { '0': 'unique' },
	character_trait_levels: { '0': 'unique' },
	character_traits: { '0': 'unique' },
	
	effect_bonus_value_id_action_results_additional_outcomes_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_agent_junction: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_agent_action_record_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_agent_subtype_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_attrition_record_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_basic_junction: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_battle_context_army_special_ability_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_battle_context_unit_ability_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_battle_context_unit_attribute_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_battle_context_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_building_set_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_faction_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_loyalty_event_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_military_force_ability_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_missile_weapon_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_pooled_resource_factor_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_pooled_resource_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_religion_junction: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_resource_junction: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_ritual_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_siege_item_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_special_ability_phase_record_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_subculture_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_unit_ability_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_unit_attribute_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_unit_set_unit_ability_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_unit_set_unit_attribute_junctions: { '0': 'unique', '1': 'unique' },
	effect_bonus_value_ids_unit_sets: { '0': 'unique', '1': 'unique' },
	effects: { '0': 'unique' },
	
	faction_political_parties_junctions: { '1': 'unique' },
	frontend_faction_leaders: { '0': 'unique' },
	frontend_factions: { '0': 'unique' },
	// political_parties_frontend_leaders_junction: { '1': 'unique' },
	_political_parties_lords_defeated: { '0': 'unique', '1': 'unique' },
	political_parties: { '0': 'unique' },
	trait_categories: { '0': 'unique' },
	trait_level_effects: { '0': 'unique', '1': 'unique' },
	trait_to_antitraits: { '0': 'unique' },
	unit_abilities: { '0': 'unique' }
}
var transform = function(tables){
	var keyed = {}
	var used = {}
	
	tables.each(function(data, tbl){
		var ka = tables_info[ tbl ]
		var ka_keys = ka.keys()
		var len = ka_keys.length
		
		var tbl_keyed = {}
		var tbl_used = {}
		
		data.each(function(file_table, file){
			// var a = tbl_keyed
			// $aa = array();
			
			file_table.each(function(entry){
				// unset($b, $bb);
				var b = tbl_keyed
				var bb = tbl_used
				for (var i = 0; i < len - 1; ++i){
					var k = parseInt(ka_keys[ i ])
					var type = ka[ k ]
					var key = entry[ k ]
					if (typeof b[ key ] === 'undefined'){
						b[ key ] = {}
						bb[ key ] = {}
					}
					b = b[ key ]
					bb = bb[ key ]
				}
				var k = parseInt(ka_keys[ len - 1 ])
				var type = ka[ k ]
				var key = entry[ k ]
				
				b[ key ] = entry
				bb[ key ] = false
			})
			
			// tbl_keyed[ file ] = a
			// tbl_used[ file ] = aa
		})
		
		keyed[ tbl ] = tbl_keyed
		used[ tbl ] = tbl_used
	})
	return [keyed, used]
if (0){
	var keyed = {},
		used = {},
		_type, key
	
	// for tbl, data in pairs(tables) do
	tables.each(function(data, tbl){
		var ka = tables_info[ tbl ]
		var ka_keys = array_keys(ka)
		var len = ka_keys.length
		
		var tbl_keyed = {},
			tbl_used = {}
		
		// for file, file_table in pairs(data) do
		data.each(function(file_table, file){
			var a = {}
			
			// for _,entry in ipairs(file_table) do
			file_table.each(function(entry){
				var b = tbl_keyed,
					bb = tbl_used
				
				// for i = 1, len - 1 do
				for (var i = 0; i < len - 1; ++i){
					var k = ka_keys[ i ]
					_type = ka[ k ]
					key = entry[ k ]
					// if not b[ key ] then
					if (!b[ key ]){
						b[ key ] = {}
						bb[ key ] = {}
					}
					b = b[ key ]
					bb = bb[ key ]
				}
				k = ka_keys[ len - 1 ]
				_type = ka[ k ]
				key = entry[ k ]
				
				b[ key ] = entry
				bb[ key ] = false
			})
		})
		
		keyed[ tbl ] = tbl_keyed
		used[ tbl ] = tbl_used
	})
	tables_info = null
	transform = null
	return [keyed, used]
}
}

function GetTraitDataIdx(t_idx){
	var t_data = TRAITS_DATA[ t_idx ]
	if (t_data[6]){
		var at_data = TRAITS_DATA[ t_data[6] ]
		return [tbl_trait_keys[ t_data[7] ], t_data, tbl_trait_keys[ at_data[7] ], at_data]
	}
	return [tbl_trait_keys[ t_data[7] ], t_data]
}
}

WIKI = function(q){
	var self = this
	var left, right
	var el = E('table', { id: 'wiki_content' }).A(E('tbody').A(E('tr')
		.A(left = this.left = E('td', { id: 'wiki_left' }))
		.A(right = this.right = E('td', { id: 'wiki_right', class: 'noselect' }))
	))
	q.el.A(el)
	
	this.ProcessDb()
	
	TRAITS_IT.each(function(idx, i){
		var obj = new TraitContainer({
			trait_key_idx: idx
		})
		left.A(obj.el)
	})
}
extProt(
	WIKI.prototype,
	'ProcessDb', function(db){
		// console.log(JSON.stringify(DB_DATA, null, 2))
		
		DB_DATA.each(function(tbl_data, tbl_name){
			DB_DATA[ tbl_name ] = ksort(tbl_data, -1)
		})
		ksort = null
		
		var a = transform(DB_DATA)
		var keyed = this.keyed = a[0]
		var used = a[1]
		
		// var res = 'DB_DATA["effects"] ='+ JSON.stringify(DB_DATA['effects'], null, 2) +'\n\n\n\n\n\n\n\n\n\n'
		// res += 'keyed["effects"] ='+ JSON.stringify(keyed['effects'], null, 2) +'\n\n\n\n\n\n\n\n\n\n'
		
// нам не нужны терпилдеры, которые не могут качаца
keyed['agent_subtypes'].each(function(v, key){
	if (!v[1]){
		tbl_forbidden_subtypes[ key ] = true
	}
})

keyed['trait_level_effects'].each(function(effects){
	effects.each(function(eff){
		// (trait_scope) trait_level_effects.scope
		if (!in_array(eff[2], tbl_scopes)){
			table.insert(tbl_scopes, eff[2])
		}
	})
})

var character_trait_levels = {}
keyed['character_trait_levels'].each(function(trait, i){
	// (trait) character_trait_levels.trait
	if (!in_array(trait[2], tbl_trait_keys)){
		table.insert(tbl_trait_keys, trait[2])
		used['character_traits'][ trait[2] ] = true
		character_trait_levels[ trait[2] ] = []
	}
	character_trait_levels[ trait[2] ].push(i)
	
	// (trait_level) character_trait_levels.key
	var key = trait[0]
	if (keyed['trait_level_effects'][ key ]){
		keyed['trait_level_effects'][ key ].each(function(_, effect){
			// (effect) trait_level_effects.effect
			used['effects'][ effect ] = true
			used['trait_level_effects'][ key ][ effect ] = true
		})
	}
})

keyed['character_traits'].each(function(entry){
	// character_traits.icon
	var icon = entry[4]
	if (typeof tbl_icons_idx[ icon ] === 'undefined'){
		table.insert(tbl_icons, [
			icon,
			// trait_categories.icon_path
			keyed['trait_categories'][ icon ][1]
		])
		tbl_icons_idx[ icon ] = tbl_icons.length - 1
	}
})

// if compact_string then
	keyed['effects'].each(function(entry){
		// effects.effect
		if (used['effects'][ entry[0] ]){
			// effects.icon
			if (!in_array(entry[1], tbl_strings)){ table.insert(tbl_strings, entry[1]) }
			// effects.icon_negative
			if (!in_array(entry[3], tbl_strings)){ table.insert(tbl_strings, entry[3]) }
			// effects.category
			if (!in_array(entry[4], tbl_strings)){ table.insert(tbl_strings, entry[4]) }
		}
	})
// end

keyed['effects'].each(function(a){
	// effects.effect
	var effect = a[0]
	if (used['effects'][ effect ]){
		// if compact_string then
			// effects.icon
			a[1] = array_search(a[1], tbl_strings)
			// effects.icon_negative
			a[3] = array_search(a[3], tbl_strings)
			// effects.category
			a[4] = array_search(a[4], tbl_strings)
		// end
		table.insert(tbl_effects, a)
		tbl_effects_idx[ effect ] = tbl_effects.length - 1
	}
})

function BuildTrait(levels_arr, t_key){
	// char_trait: { key, no_going_back_level, hidden, precedence, icon, ui }
	var char_trait = keyed['character_traits'][ t_key ]
	var trait_key_idx = array_search(t_key, tbl_trait_keys)
	var T_DATA = [
		char_trait[1], // no_going_back_level
		char_trait[2], // hidden
		char_trait[3], // precedence
		tbl_icons_idx[ char_trait[4] ], // icon: char_trait[3],
		char_trait[5], // ui
		[], // levels
		null,
		trait_key_idx
	]
	
	// trait: { key, level, trait, threshold_points }
	levels_arr.each(function(level_i){
		var trait = keyed['character_trait_levels'][ level_i ]
		var effects = []
		// no effects for dummies
		if (keyed['trait_level_effects'][ trait[0] ]){
			// eff: { trait_level, effect, effect_scope, value }
			keyed['trait_level_effects'][ trait[0] ].each(function(eff){
				table.insert(effects, [
					tbl_effects_idx[ eff[1] ], // effect
					array_search(eff[2], tbl_scopes), // scope
					eff[3] // value
				])
			})
		}
		table.insert(T_DATA[5], [
			trait[1], // level
			trait[0], // level_key (not idx)
			trait[3], // threshold_points
			effects
		])
	})
	if (T_DATA[4] < 0){
		T_DATA[5].sort(function(a, b){
			return b[0] - a[0]
		})
	} else{
		T_DATA[5].sort(function(a, b){
			return a[0] - b[0]
		})
	}
	return T_DATA
}

var tta = keyed['trait_to_antitraits']
var tta_used = {}
character_trait_levels.each(function(levels_arr, t_key){
	for (var k in tta){
		var entry = tta[ k ]
		if (entry[0] === t_key || entry[1] === t_key){
			var at_key = entry[ entry[0] === t_key ? 1 : 0 ]
			if (tta_used[ at_key ]){ return }
			
			tta_used[ t_key ] = true
			var t_data = BuildTrait(levels_arr, t_key)
			var at_data = BuildTrait(character_trait_levels[ at_key ], at_key)
			t_data[6] = TRAITS_DATA.length + 1
			at_data[6] = TRAITS_DATA.length
			table.insert(TRAITS_DATA, t_data)
			table.insert(TRAITS_DATA, at_data)
			return
		}
	}
	var t_data = BuildTrait(levels_arr, t_key)
	table.insert(TRAITS_DATA, t_data)
})

TRAITS_DATA.each(function(_, idx){
	var a = TRAITS_DATA[ idx ]
	// Сортируем по уровням
	// character_traits.ui
	if (a[4] >= 0){
		// var hasEffects = false
		// for (var i = 0; i < a[5].length; ++i){
			// if (a[5][ i ][3].length){
				// hasEffects = true
				// break
			// }
		// }
		// if (!hasEffects && a[6]){
			// var b = TRAITS_DATA[ a[6] ]
			// for (var i = 0; i < b[5].length; ++i){
				// if (b[5][ i ][3].length){
					// hasEffects = true
					// break
				// }
			// }
		// }
		var level_key = a[5][ 0 ][1]
		var onscreen_name = DB_TEXT['character_trait_levels_onscreen_name_'+ level_key]
		onscreen_name = onscreen_name.replace(/[.,\\\|\/#!$%\^&\*;:{}=\-_`~()\s]/, '')
		var add = (onscreen_name.length > 0)
		if (add){
			if (a[4] > 0 || !a[6] || TRAITS_DATA[ a[6] ][4] < 0){
				table.insert(TRAITS_IT, idx)
			}
		}
		console.log(level_key)
	}
})

TRAITS_IT.sort(function(a, b){
	return TRAITS_DATA[ a ][3] - TRAITS_DATA[ b ][3]
})
		
		keyed['effect_bonus_value_unit_ability_junctions'].each(function(bvi){
			bvi.each(function(entry){
				if (typeof tbl_effects_idx[ entry[1] ] !== 'undefined'){
					tbl_ebv_unit_abilities[ tbl_effects_idx[ entry[1] ] ] = entry[2]
				}
			})
		})
		
		keyed['unit_abilities'].each(function(entry){
			tbl_unit_abilities[ entry[0] ] = entry[1]
		})
	
	},
	'SetFilters', function(filters){
		var self = this
		filters.each(function(filter){
			filter = filter.toKeys('name', 'img', 'func')
			self.right.A(
				E('div', { class: 'filter' }, filter.name)
				.P(E('img', { src: 'game/ui/campaign ui/effect_bundles/'+ filter.img +'.png' }))
				.on('click', function(){
					if (this.CC('active')){ this.CR('active') }
					else{
						;[].slice.call(self.right.children).CR('active')
						this.CA('active')
					}
					self.Filter(this.CC('active') ? filter : null)
				})
			)
		})
	},
	'Filter', function(filter){
		var ff = (filter ? filter.func : null)
		var left = this.left
		left.h('')
		
		TRAITS_IT.each(function(t_idx){
			var a = GetTraitDataIdx(t_idx)
			var t_key = a[0],
				t_data = a[1],
				at_key = a[2],
				at_data = a[3]
			
			if (ff && !ff(t_key, t_data, at_key, at_data)){ 
				return
			}
			
			var obj = new TraitContainer({
				trait_key_idx: t_idx
			})
			left.A(obj.el)
		})
	}
)



})()