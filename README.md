# About
Total War Warhammer 2 Mod "Cpecific's Traits Manager" (UI Layout Parser is included).

# How to use Traits Manager with your mod
Export all database tables listed below through Pack Manager in Binary format into /game folder (and you need data__ of main game too). To export in Binary in (Rusted) Pack File Manager right click on entry and select Extract > Extract Selected (Ctrl + X or Ctrl + E).
Your folders tree will look like this:
- game
  - db
    - character_traits
	  - data__
	  - yourmod

If your mod doesn't introduce changes to some of those tables, it's okay, don't worry. But don't forget to export data__.
* agent_subtypes_tables
* character_trait_levels_tables
* character_traits_tables
* effect_bonus_value_id_action_results_additional_outcomes_junctions_tables
* effect_bonus_value_agent_junction_tables
* effect_bonus_value_agent_action_record_junctions_tables
* effect_bonus_value_agent_subtype_junctions_tables
* effect_bonus_value_attrition_record_junctions_tables
* effect_bonus_value_basic_junction_tables
* effect_bonus_value_battle_context_army_special_ability_junctions_tables
* effect_bonus_value_battle_context_unit_ability_junctions_tables
* effect_bonus_value_battle_context_unit_attribute_junctions_tables
* effect_bonus_value_battle_context_junctions_tables
* effect_bonus_value_building_set_junctions_tables
* effect_bonus_value_faction_junctions_tables
* effect_bonus_value_loyalty_event_junctions_tables
* effect_bonus_value_military_force_ability_junctions_tables
* effect_bonus_value_missile_weapon_junctions_tables
* effect_bonus_value_pooled_resource_factor_junctions_tables
* effect_bonus_value_pooled_resource_junctions_tables
* effect_bonus_value_religion_junction_tables
* effect_bonus_value_resource_junction_tables
* effect_bonus_value_ritual_junctions_tables
* effect_bonus_value_ritual_category_junctions
* effect_bonus_value_siege_item_junctions_tables
* effect_bonus_value_special_ability_phase_record_junctions_tables
* effect_bonus_value_subculture_junctions_tables
* effect_bonus_value_unit_ability_junctions_tables
* effect_bonus_value_unit_attribute_junctions_tables
* effect_bonus_value_unit_set_unit_ability_junctions_tables
* effect_bonus_value_unit_set_unit_attribute_junctions_tables
* effect_bonus_value_ids_unit_sets_tables
* effects
* faction_political_parties_junctions_tables
* frontend_faction_leaders_tables
* frontend_factions_tables
* political_parties_tables
* trait_categories_tables
* trait_level_effects_tables
* trait_to_antitraits_tables
* unit_abilities

(after looking at this long list: "I better just export whole `db/` folder").

If your mod introduces new traits of type "Defeated some noname lord", then you will need to add your data (by hand) into `$tables['_political_parties_lords_defeated']` in `index.php` (line 601).

If your mod changes data__ files (why did you do dat?) then you will need to fill `$modded_data__` in `index.php` (line 8).

For those who are not familiar with web servers, just install XAMPP, make subfolder in htdocs, and put repository files there. You can access them at `localhost/[yourfolder]/[file].php`

Run `/index.php` and copy result in new file (ex. `__tables.lua`). Then you need to tell my mod about your data. The safest time to tell is in `UICreated Event`. You won't be able to pass your data from the moment, when `FirstTickAfterWorldCreated Event` is fired. My mod is visible in global for `export_helper_[yourname]`. Whether it is visible from `campaign/~/mod/yourname.lua` I don't know.
```lua
if CpecificTraitsManager ~= nil then
	CpecificTraitsManager.SetData(require('script/~path/__tables'))
end
```

# UI Layout Parser
There is parser by __Alpaca__ and his inheritor __taw__ for older versions.

My parser "supports" version from 70 to 133; has a lot of deciphered fields; examples of use in `export.php` (outdated as a reference, but still can be used); easier (compared to xml) web representation in `ui.php`.

Supported games:
* Attila
* Rome 2
* Three Kingdoms
* Thrones of Britannia
* Troy
* Warhammer
* Warhammer II

You need to export (preferably the whole) `/ui` folder with Pack Manager into `htdocs/[yourfolder]/game`.
For viewing files run `/ui.php`, for debugging use `/dump.php`, for checking parser use `/check.php`.

But beware pioneers, CA's UI is pain in the ass.
