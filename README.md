# About
Total War Warhammer 2 Mod "Cpecific's Traits Manager" (UI Layout Parser is included).

# How to use Traits Manager with your mod
Export all database tables listed below through Pack Manager in Binary format into /game folder (and you need data__ of main game too).
You should expect tree like
- game
  - db
    - character_traits
	  - data__
	  - yourname

If your mod doesn't introduce changes to some of those tables, it's okay, don't worry. But don't forget to export data__.
* agent_subtypes
* character_trait_levels
* character_traits
* effects
* faction_political_parties_junctions
* frontend_faction_leaders
* frontend_factions
* political_parties
* trait_categories
* trait_level_effects
* trait_to_antitraits

If your mod introduces new traits of type "Defeated some noname lord", then you will need to add your data (by hand) into `$tables['_political_parties_lords_defeated']` in `index.php`.

For those who are not familiar with web, then just install Denwer or XAMPP.

Run `/index.php` and copy result in new file (ex. __tables.lua). Then add code below. The safest time to call it is in `UICreated Event`. You won't be able to pass your data from the moment, when `FirstTickAfterWorldCreated Event` is fired. It is visible in global in `export_helper_[yourname]`. Considering is it visible from `campaign/~/mod/yourname.lua` I don't know.
```lua
CpecificTraitsManager.SetData(require('script/~path/__tables'))
```

Beware, I didn't check if it works, so if you can't make this shit work, contact me vahonin.prog@gmail.com

# UI Layout Parser
So, i fucked up a lot with wasting time. It's another example in recent years that Google is shit.
There is parser by Alpaca, which I found, when already completed my parser (not like i wouldn't need to spend weeks of hard work on this project, but it could save me a couple of weeks).

Anyway, my parser "supports" version from 70 to 119 (mostly from 100 to 119), has a lot of deciphered fields and examples of use in `export.php`.
You need to export (preferably the whole) /ui folder with Pack Manager into /game folder
For viewing files run `/ui.php`, for debuggin run `/dump.php`.
But beware pioneers, CA's UI is pain in the ass.