# About
Total War Warhammer 2 Mod "Cpecific's Traits Manager" (UI Layout Parser is included).

# How to use Traits Manager with your mod
Export all database tables listed below through Pack Manager in Binary format into /game folder (and you need data__ of main game too). To export in Binary in Pack File Manager right click on entry and select Extract > Extract Selected (Ctrl + X).
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

If your mod introduces new traits of type "Defeated some noname lord", then you will need to add your data (by hand) into `$tables['_political_parties_lords_defeated']` in `index.php` (line 1072).

If your mod changes data__ files (why did you do dat?) then you will need to fill `$modded_data__` in `index.php` (line 10).

For those who are not familiar with web servers, just install XAMPP, make subfolder in htdocs, and put files there. You can access them at `localhost/[yourfolder]/[file].php`

Run `/index.php` and copy result in new file (ex. `__tables.lua`). Then add code below. The safest time to call it is in `UICreated Event`. You won't be able to pass your data from the moment, when `FirstTickAfterWorldCreated Event` is fired. It is visible in global in `export_helper_[yourname]`. Considering is it visible from `campaign/~/mod/yourname.lua` I don't know.
```lua
if CpecificTraitsManager ~= nil then
	CpecificTraitsManager.SetData(require('script/~path/__tables'))
end
```

If you can't make this shit work, contact me vahonin.prog@gmail.com

# UI Layout Parser
So, i fucked up a lot with wasting time. It's another example in recent years that Google is shit and I can't find anything useful in it.
There is parser by __Alpaca__ and his inheritor __taw__, which I found, when already completed my parser (not like i wouldn't need to spend weeks of hard work on this project, but it could save me a couple of weeks).

Anyway, my parser "supports" version from 70 to 129; has a lot of deciphered fields; examples of use in `export.php`, outdated as a reference, but still can be used; easier (compared to xml) web representation in `ui.php`.

Supported games:
* Warhammer
* Warhammer II
* Thrones of Britannia
* Three Kingdoms

You need to export (preferably the whole) `/ui` folder with Pack Manager into `/game`.
For viewing files run `/ui.php`, for debugging run `/dump.php`.

But beware pioneers, CA's UI is pain in the ass.
