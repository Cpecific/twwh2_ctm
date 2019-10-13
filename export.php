<?php

include 'get_dir_data.php';
include 'class.php';

function change_uid($uic){
	$uic->uid = mb_substr($uic->uid, 0, 1) . '1' . mb_substr($uic->uid, 2);
	foreach ($uic->child as $child){
		change_uid($child);
	}
}
function for_all($uic, $func){
	$func($uic);
	foreach ($uic->child as $child){
		for_all($child, $func);
	}
}

// CTM_trait_template
// CTM_trait_dy_trait
// CTM_trait_bar_holder
// CTM_horizontal_bar
// CTM_trait_level_template
if (0){
	$h = fopen($DIR_DATA['campaign']['DIR'] . 'character_details_panel', 'r');
	if (!$h){ throw new Exception('FILE'); }

	$uic = new UIC();
	$uic->read($h);
	fclose($h);
	for_all($uic, function($ch){
		$ch->events = array();
	});
	
	// traits_list
	$ch = $uic->child[0]->child[9]->child[1]->child[0]->child[1]->child[1]->child[0]->child[0]->child[1];
	// template_entry
	$ch_tpl = $ch->child[0];
	
// CTM_trait_template
	$ch = $ch_tpl;
	$uic->child = array($ch);
	$ch->parent = $uic;
	$ch->b_01 = '00 01 00 00 00 00 01 00 00 00 00 01';
	
	// dy_trait
	$ch_dy = $ch->child[0];
	// bar_holder
	$ch_holder = $ch->child[2];
	// trait_bar
	$ch_bar = $ch_holder->child[0];
	// template_entry
	$ch_level = $ch_bar->child[0];
	$ch_bar->child = array(); // очищаем trait_bar
	
	$uic->child = array($ch);
	$ch->parent = $uic;
	$ch->child = array();
	
	if (true){
		$ch->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
		$ch->images[0]->path = 'ui\\skins\\default\\CTM_parchment_button_square_hover.png';
		
		$margin = array('top' => 3 + 4, 'left' => 10);
		$img_size = 24;
		
		// active
		$state = $ch->states[0];
		$state->name = 'active';
		$state->textalign['vertical'] = 2;
		$state->textbounds['height'] = 4;
		$state->textoffset = array(
			$margin['left'] + $img_size + 4,
			2, // right
			$margin['top'],
			0
		);
		$state->b7 = '01 00 00 00';
		
		// hover
		$ch->states[] = $state = new UIC__State($state);
		$state->uid = '40 E0 D8 54';
		$state->name = 'hover';
		$state->bgs[0]->colour = 'FF FF FF 96';
		
		// down
		$ch->states[] = $state = new UIC__State($state);
		$state->uid = 'A0 8B 53 0E';
		$state->name = 'down';
		$state->shader_name = 'brighten_t0';
		$state->shadervars = array(-0.5, 0, 0, 0);
		// $state->bgs[] = $bg = new UIC__State_Backgrounds($state->bgs[0]);
		// $bg->colour = 'FF FF FF 96';
		
		// down_off
		$ch->states[] = $state = new UIC__State($state);
		$state->uid = 'C0 3E 32 7F';
		$state->name = 'down_off';
		$state->shadervars = array(-0.25, 0, 0, 0);
		
		// active
		$ch->states[0]->b_mouse = '14 00 00 00 18 00 00 00';
		$ch->states[0]->mouse[] = new UIC__State_Mouse(array(
			'mouse_state' => 0,
			'state_uid' => $ch->states[1]->uid, // hover
			'b0' => 'C8 00 00 00 40 00 00 00'
		));
		
		// hover
		$ch->states[1]->b_mouse = '89 00 00 00 7E 00 00 00';
		$ch->states[1]->mouse[] = new UIC__State_Mouse(array(
			'mouse_state' => 1,
			'state_uid' => $ch->states[0]->uid, // active
			'b0' => 'C8 00 00 00 40 00 00 00'
		));
		$ch->states[1]->mouse[] = new UIC__State_Mouse(array(
			'mouse_state' => 2,
			'state_uid' => $ch->states[2]->uid, // down
			'b0' => '00 00 00 00 00 00 00 00'
		));
		
		// down
		$ch->states[2]->b_mouse = '91 00 00 00 DB 00 00 00';
		$ch->states[2]->mouse[] = new UIC__State_Mouse(array(
			'mouse_state' => 3,
			'state_uid' => $ch->states[1]->uid, // hover
			'b0' => '00 00 00 00 00 00 00 00'
		));
		$ch->states[2]->mouse[] = new UIC__State_Mouse(array(
			'mouse_state' => 1,
			'state_uid' => $ch->states[3]->uid, // down_off
			'b0' => '00 00 00 00 00 00 00 00'
		));
		
		// down_off
		$ch->states[3]->b_mouse = '1D 00 00 00 3F 01 00 00';
		$ch->states[3]->mouse[] = new UIC__State_Mouse(array(
			'mouse_state' => 0,
			'state_uid' => $ch->states[2]->uid, // down
			'b0' => '00 00 00 00 00 00 00 00'
		));
		$ch->states[3]->mouse[] = new UIC__State_Mouse(array(
			'mouse_state' => 8,
			'state_uid' => $ch->states[0]->uid, // active
			'b0' => '00 00 00 00 00 00 00 00'
		));
		
		// inactive
		$ch->states[] = $state = new UIC__State($ch->states[0]);
		$state->uid = 'D0 01 23 0A';
		$state->name = 'inactive';
		$state->colour = 'FF FF FF 32';
		// $state->b7 = '00 00 00 00';
		$state->b_mouse = '00 00 00 00 00 00 00 00';
		$state->mouse = array();
		
		$ch->default_state = $ch->states[0]->uid;
	}
	
	// conflict update
	if (true){
		$ch->images[] = $image = new UIC__Image(array(
			'uid' => '40 13 AA 0F',
			'path' => 'ui\\skins\\default\\CTM_alert_dot.png',
			'width' => 18,
			'height' => 18,
			'extra' => '00'
		), $ch);
		
		// active_conflict
		$ch->states[] = $state = new UIC__State($ch->states[0]);
		$state->uid = 'D1 8B FE 0E';
		$state->name = 'active_conflict';
		// hover_conflict
		$ch->states[] = $state = new UIC__State($ch->states[1]);
		$state->uid = '41 E0 D8 54';
		$state->name = 'hover_conflict';
		// down_conflict
		$ch->states[] = $state = new UIC__State($ch->states[2]);
		$state->uid = 'A1 8B 53 0E';
		$state->name = 'down_conflict';
		// down_off_conflict
		$ch->states[] = $state = new UIC__State($ch->states[3]);
		$state->uid = 'C1 3E 32 7F';
		$state->name = 'down_off_conflict';
		// inactive_conflict
		$ch->states[] = $state = new UIC__State($ch->states[4]);
		$state->uid = 'D1 01 23 0A';
		$state->name = 'inactive_conflict';
		
		// active_conflict
		$state = $ch->states[5];
		$state->mouse[0]->state_uid = $ch->states[6]->uid; // hover_conflict
		// hover_conflict
		$state = $ch->states[6];
		$state->mouse[0]->state_uid = $ch->states[5]->uid; // active_conflict
		$state->mouse[1]->state_uid = $ch->states[7]->uid; // down_conflict
		// down_conflict
		$state = $ch->states[7];
		$state->mouse[0]->state_uid = $ch->states[6]->uid; // hover_conflict
		$state->mouse[1]->state_uid = $ch->states[8]->uid; // down_off_conflict
		// down_conflict
		$state = $ch->states[8];
		$state->mouse[0]->state_uid = $ch->states[7]->uid; // down_conflict
		$state->mouse[1]->state_uid = $ch->states[5]->uid; // active_conflict
		
		for ($i = 5; $i <= 9; ++$i){
			$state = $ch->states[ $i ];
			$state->bgs[] = $bg = new UIC__State_Background($state->bgs[0]);
			$bg->uid = $image->uid;
			$bg->offset = array('left' => 4, 'top' => 4);
			$bg->bounds[0] -= 8; $bg->bounds[1] -= 8;
			$bg->colour = '00 00 FF FF';
			$bg->margin = array(6, 6, 6, 6);
		}
	}
	
	$ch->after = array('00', '', '', '00', '00', '00 00 00', '00 00 80 3F', '00 00 00 00', '00 00 00 00');
	// var_dump($uic->debug());
	
	file_put_contents('export/CTM_trait_template', $uic->dumpFile());
	
	// wh2
	$ch->images[0]->path = 'ui/skins/default/CTM_parchment_button_square_hover_wh2.png';
	file_put_contents('export/CTM_trait_template_wh2', $uic->dumpFile());
	
// CTM_trait_dy_trait
	$ch = $ch_dy;
	$uic->child = array($ch);
	$ch->parent = $uic;
	$ch->name = 'CTM_dy_trait';
	// $ch->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
	$ch->tooltip_text = '';
	$ch->tooltip_id = '';
	$ch->images[0]->path = '';
	
	$state = $ch->states[0];
	$state->name = 'active';
	$state->text = '';
	$state->textlabel = '';
	// $state->textoffset = array(28, 2, 4, 0 + 4);
	$state->b7 = '00 00 00 00';
	
	$ch->states[] = $state = new UIC__State($ch->states[0]);
	$state->uid = '00 1E 5B D2';
	$state->name = 'inactive';
	// $state->font_m_colour = '00 00 00 64';
	// $state->shader_name = 'set_greyscale_t0';
	// $state->shadervars = array(1, 0.6, 0, 0);
	// $state->text_shader_name = 'set_greyscale_t0';
	// $state->textshadervars = array(0, 0.6, 0, 0);
	$bg = $state->bgs[0];
	$bg->shader_name = 'set_greyscale_t0';
	$bg->shadertechnique_vars = array(1, 0.6, 0, 0);
	
	file_put_contents('export/CTM_trait_dy_trait', $uic->dumpFile());
	
// CTM_trait_bar_holder
	$ch = new UIC($ch_bar, $uic);
	$uic->child = array($ch);
	
	$ch->offset = array('top' => 32 + 4, 'left' => 10);
	$ch->docking = 0;
	$ch->images[0]->path = 'ui/skins/default/CTM_trait_frame.png';
	$state = $ch->states[0];
	$state->name = 'active';
	$state->bounds = array(67, 16 + 4);
	$state->b7 = '00 00 00 00';
	$state->bgs[0]->dock = array('right' => 1, 'bottom' => 0);
	
	$ch->states[] = $state = new UIC__State($state);
	$state->uid = 'A0 CC FE 0F';
	$state->name = 'inactive';
	// $state->shader_name = 'set_greyscale_t0';
	// $state->shadervars = array(1, 0.6, 0, 0);
	$bg = $state->bgs[0];
	$bg->shader_name = 'set_greyscale_t0';
	$bg->shadertechnique_vars = array(1, 0.6, 0, 0);
	
	file_put_contents('export/CTM_trait_bar_holder', $uic->dumpFile());
	
	// wh2
	$ch->images[0]->path = 'ui/skins/default/CTM_trait_frame_wh2.png';
	file_put_contents('export/CTM_trait_bar_holder_wh2', $uic->dumpFile());
	
// CTM_horizontal_bar
	$ch = new UIC($ch_bar, $uic);
	$uic->child = array($ch);
	
	$ch->name = 'horizontal_bar';
	$ch->offset = array('top' => 0, 'left' => 0);
	$ch->b7 = '00 00 00 00';
	$ch->b_01 = '01 01 00 00 00 00 01 00 00 00 00 01';
	$ch->images = array();
	$state = $ch->states[0];
	$state->textalign['vertical'] = 1;
	$state->bgs = array();
	// padding left and right
	$ch->after[2][6] = 0;
	$ch->after[2][7] = 0;
	
	file_put_contents('export/CTM_horizontal_bar', $uic->dumpFile());
	
// CTM_trait_level_template
	$ch = $ch_level;
	$uic->child = array($ch);
	$ch->parent = $uic;
	$ch->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
	$ch->tooltip_text = '';
	$ch->tooltip_id = '';
	// frame_current
	$ch->child[1]->b_01 = '00 00 00 00 00 00 00 00 00 00 00 00';
	
	$ch = $ch->child[0];
	foreach ($ch->images as $im){
		$im->path = str_replace('\\default\\', '\\default\\CTM_', $im->path);
	}
	for ($i = 0, $to = sizeof($ch->states); $i < $to; ++$i){
		$ch->states[] = $state = new UIC__State($ch->states[ $i ]);
		$uid = -array_shift(unpack('L1', fromhex($state->uid)));
		$state->uid = tohex(pack('L1', $uid));
		$state->name = 'i'. $state->name;
		$state->shader_name = 'set_greyscale_t0';
		$state->shadervars = array(1, 0.6, 0, 0);
	}
	
	$ch = $ch->parent->child[1];
	$ch->images[0]->path = str_replace('\\default\\', '\\default\\CTM_', $ch->images[0]->path);
	$ch->states[0]->name = 'active';
	$ch->states[] = $state = new UIC__State($ch->states[0]);
	$uid = -array_shift(unpack('L1', fromhex($state->uid)));
	$state->uid = tohex(pack('L1', $uid));
	$state->name = 'inactive';
	$state->shader_name = 'set_greyscale_t0';
	$state->shadervars = array(1, 0.6, 0, 0);
	
	file_put_contents('export/CTM_trait_level_template', $uic->dumpFile());
	
	// warhammer2
	$ch->images[0]->path = str_replace('.png', '_wh2.png', $ch->images[0]->path);
	$ch = $ch->parent->child[0];
	foreach ($ch->images as $im){
		$im->path = str_replace('.png', '_wh2.png', $im->path);
	}
	
	file_put_contents('export/CTM_trait_level_template_wh2', $uic->dumpFile());
}

// CTM_character_trait_tooltip
// CTM_character_tooltip_effect_template
if (0){
	$h = fopen($DIR_DATA['campaign']['DIR'] . 'character_trait_tooltip', 'r');
	if (!$h){ throw new Exception('FILE'); }

	$uic = new UIC();
	$uic->read($h);
	fclose($h);
	
	// root > character_trait_tooltip
	$ch = $uic->child[0];
	$ch->images[0]->path = 'ui/skins/default/CTM_tooltip_frame.png';
	// traits_list > template_entry
	$ch_template = $ch->child[3]->child[0];
	
	$ch->child[3]->child = array();
	$uic->events = array();
	foreach ($ch->child as $child){
		$child->events = array();
	}
	
	file_put_contents('export/CTM_character_trait_tooltip', $uic->dumpFile());
	
	// wh2
	$ch->images[0]->path = 'ui/skins/default/CTM_tooltip_frame_wh2.png';
	file_put_contents('export/CTM_character_trait_tooltip_wh2', $uic->dumpFile());
	
	$ch = $ch_template;
	$uic->child = array($ch);
	$ch->parent = $uic;
	$ch->child = array();
	for_all($uic, function($ch){
		$ch->events = array();
	});
	
	file_put_contents('export/CTM_character_tooltip_effect_template', $uic->dumpFile());
}

// deleted this chunk of code by mistake,
// and now cannot remember where got it from, so just let it be
// CTM_slider_list
/*if (0){
	$h = fopen($DIR_DATA['export']['DIR'] . 'CTM_panel', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic = new UIC();
	$uic->read($h);
	fclose($h);
	
	$ch = $uic->child[0]->child[1]->child[0];
	$uic->child = array($ch);
	
	file_put_contents('export/CTM_slider_list', $uic->dumpFile());
}*/

// CTM_panel
if (0){
	$h = fopen($DIR_DATA['campaign']['DIR'] . 'pre_battle_post_battle', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic = new UIC();
	$uic->read($h);
	fclose($h);
	
	// preview_map
	$ch_preview = $uic->child[0]->child[0]->child[0]->child[0]->child[0];
	
	// $uic->child = array($uic->child[0]);
	
	$ch = $ch_preview;
	$ch->parent = $uic;
	$uic->child = array($ch);
	
	$ch->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
	
	// переводим в 106 и создаём lroot, rroot
	if (true){
		$ch->child = array();
		$ch->events = '';
		$ch->offset = array('left' => 0, 'top' => 0);
		$ch->docking = 0;
		$ch->dock_offset = array('left' => 0, 'top' => 0);
		$ch->funcs = array();
		$ch->states[0]->b7 = '00 00 00 00';
		
		$ch->b5 = '00 00 00 00';
		$ch->states[0]->tooltip_id = '';
		$ch->states[0]->b5 = '00 00';
		$ch->states[0]->bgs[0]->margin = array(50, 50, 50, 50);
		$ch->after = array('00', '', '', '00', '00', '00 00 00');
		$uic->events = '';
		$uic->b5 = '00 00 00 00';
		$uic->states[0]->tooltip_id = '';
		$uic->states[0]->b5 = '00 00';
		$uic->after = array('00', '', '', '00', '00', '00 00 00');
		
		$ch->child = array(
			$ch_lroot = new UIC($ch, $ch),
			$ch_rroot = new UIC($ch, $ch)
		);
		$ch->docking = 7;
		// $ch->dock_offset = array('left' => 0, 'top' => -255 + 24 + 47 * 0);
		
		$ch_lroot->uid = 'C1 FA 79 39';
		$ch_lroot->name = 'left_holder';
		$ch_lroot->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
		
		$ch_rroot->uid = 'C2 FA 79 39';
		$ch_rroot->name = 'right_holder';
		$ch_rroot->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
	}
	
if (true){
	$h = fopen($DIR_DATA['export']['DIR'] . 'CTM_slider_list', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic_s = new UIC();
	$uic_s->read($h);
	fclose($h);
	
	$ch_r = $uic_s->child[0];
	
	// создаём связь lroot с ch_l
	if (true){
		$ch_r->parent = $ch_rroot;
		$ch_r->offset = array('left' => 0, 'top' => 0);
		
		$ch_lroot->child = array($ch_l = new UIC($ch_r, $ch_lroot));
		$ch_rroot->child = array($ch_r);
		
		$ch_l->uid = 'A1 CB 01 0B';
		$ch_l->default_state = '51 D7 E7 08';
		$ch_l->states[0]->uid = '51 D7 E7 08';
		
		$ch_l->child[0]->uid = 'D1 D9 77 09';
		$ch_l->child[0]->default_state = '11 32 15 0B';
		$ch_l->child[0]->states[0]->uid = '11 32 15 0B';
		
		$ch_l->child[0]->child[0]->uid = 'E1 CA 6A 03';
		$ch_l->child[0]->child[0]->default_state = '01 5B F6 0A';
		$ch_l->child[0]->child[0]->states[0]->uid = '01 5B F6 0A';
	}
	
	// $ch_l->child[1]->uid = '00 80 7A 10';
	// $ch_r->child[1]->uid = '0D 11 2F 90';
	
	$total_width = 0;
	
#region left
	$left_width = 51 * 8 + 20 * 2 + 27;
	$left_margin = 12;
	$intersection = 40;
	
	$padding = 20;
	$scroll_width = 20 + 7;
	$scroll_btns = 34;
	$width = $left_width - $padding * 2 - $scroll_width;
	$height = 510 - $left_margin * 2;
	$scroll_height = $height - $scroll_btns * 2;
	
	$total_width += $left_width - $intersection;
	
	$ch_lroot->images = array(
		$image = new UIC__Image(array(
			'uid' => 'C0 BC 57 36',
			'path' => 'ui/skins/default/CTM_leather.png',
			'width' => 212,
			'height' => 213,
			'extra' => '00'
		), $ch_lroot)
	);
	$ch_l->images = array();
	$ch_l->states[0]->bgs = array();
	
	$ch_lroot->offset = array('left' => 0, 'top' => $left_margin);
	$state = $ch_lroot->states[0];
	$state->bounds = array(
		$width + $padding * 2 + $scroll_width,
		$height + $padding * 2);
	$bg = $state->bgs[0];
	$bg->uid = $image->uid;
	$bg->bounds = array(
		$width + $padding * 2 + $scroll_width,
		$height + $padding * 2 + 245);
	$bg->y_flipped = 0;
	$bg->margin = array(32, 32, 267, 110);
	
	// listview
	$ch = $ch_l;
	$ch->states[0]->bounds = array(
		$width + $padding * 2 + $scroll_width,
		$height + $padding * 2);
	
	// list_clip
	$ch = $ch->child[0];
	
	$ch->offset = array('top' => $padding, 'left' => $padding);
	$ch->states[0]->bounds = array($width, $height);
	
	// list_box
	$ch = $ch->child[0];
	$ch->child = array();
	
	$ch->offset = array('top' => 0, 'left' => 0);
	$ch->states[0]->bounds = array($width, $height);
	
	// vslider
	$ch = $ch_l->child[1]->template[5];
	$ch->b_floats[3] = $scroll_height;
#endregion
	
#region right
	$width = 253 + 7 * 0; $height = 510;
	$scroll_height = $height - $scroll_btns * 2;
	
	$total_width += $width + $padding * 2 + $scroll_width;
	$total_height = $height + $padding * 2;
	
	$ch_rroot->images = array(
		$image = new UIC__Image(array(
			'uid' => '11 BA 52 02',
			'path' => 'ui/skins/default/CTM_parchment_texture.png',
			'width' => 256,
			'height' => 256,
			'extra' => '00'
		), $ch_rroot)
	);
	$ch_r->images = array();
	$ch_r->states[0]->bgs = array();
	$ch_rroot->offset = array('left' => $left_width - $intersection, 'top' => 0);
	$state = $ch_rroot->states[0];
	$state->bounds = array(
		$width + $padding * 2 + $scroll_width,
		$height + $padding * 2);
	$state->bgs[0]->uid = $image->uid;
	$state->bgs[0]->bounds = array(
		$width + $padding * 2 + $scroll_width,
		$height + $padding * 2);
	
	// listview
	$ch = $ch_r;
	$ch->states[0]->bounds = array(
		$width + $padding * 2 + $scroll_width,
		$height + $padding * 2);
	
	// list_clip
	$ch = $ch->child[0];
	
	$ch->offset = array('top' => $padding, 'left' => $padding);
	$ch->states[0]->bounds = array($width, $height);
	
	// list_box
	$ch = $ch->child[0];
	$ch->child = array();
	
	$ch->offset = array('top' => 0, 'left' => 0);
	$ch->states[0]->bounds = array($width, $height);
	
	// vslider
	$ch = $ch_r->child[1]->template[5];
	$ch->b_floats[3] = $scroll_height;
#endregion
	
	$ch = $ch_preview;
	$ch->images = array();
	$state = $ch->states[0];
	$state->bounds = array($total_width, $total_height);
	$state->bgs = array();
}
	
// добавляем анимации появления и избавления поднятия пропажи
if (true){
	$h = fopen($DIR_DATA['campaign']['DIR'] . 'layout', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic_a = new UIC();
	$uic_a->read($h);
	fclose($h);
	
	$ch_a = $uic_a->child[0]->child[5]->child[0];
	$ch = $uic->child[0];
	$ch->offset = array('left' => 0, 'top' => 0);
	$ch->dock_offset = $offset = array('left' => 0, 'top' => -231);
	// $offset_hide = array('left' => $offset['left'], 'top' => $offset['top'] + 258 * 2);
	$offset_hide = array('left' => $offset['left'], 'top' => $offset['top'] + 258);
	
	$ch->funcs = array(
		new UIC__Func($ch_a->funcs[0], $ch)
	);
	// перевод в 106
	if (true){
		$ch->funcs[0]->anim[0]->str_sth = '';
		$ch->funcs[0]->anim[0]->b3 = '00 00';
	}
	
	// show
	$func = $ch->funcs[0];
	$anim = $func->anim[0];
	$anim->offset = $offset_hide;
	$anim->bounds = array($total_width, $total_height);
	$anim->b2 = '01 00';
	$anim->interpolationtime = 0;
	
	$func->anim[] = $anim = new UIC__Func_Anim($anim);
	$anim->offset = $offset;
	$anim->interpolationtime = 500;
	
	// showlong
	$ch->funcs[] = $func = new UIC__Func($ch->funcs[0], $ch);
	$func->name = 'showlong';
	$anim = $func->anim[0];
	$anim->offset['top'] += 258;
	
	$anim = $func->anim[1];
	$anim->interpolationtime = 600;
	
	// showleft
	$ch->funcs[] = $func = new UIC__Func($ch->funcs[0], $ch);
	$func->name = 'showleft';
	$anim = $func->anim[0];
	$anim->offset['left'] = -$total_width + 1;
	
	array_splice($func->anim, 1, 0, array($anim = new UIC__Func_Anim($anim)));
	$anim->interpolationtime = round(700 * 20 / 32);
	
	$anim = $func->anim[2];
	$anim->interpolationtime = round(700 * 20 / 32);
	// 23 / 32 * 700
	
	if (0){
		$ch->funcs[] = $func = new UIC__Func($func);
		$func->name = 'immediate';
		array_splice($func->anim, 1);
		$anim = $func->anim[0];
		// $anim->colour = 'FF FF FF FF';
		$anim->offset = $offset;
	}
}
	
	$uic->setVersion(106);
	
	file_put_contents('export/CTM_panel', $uic->dumpFile());
	
	$ch_lroot->images[0]->path = 'ui/skins/default/CTM_leather_wh2.png';
	$ch_rroot->images[0]->path = 'ui/skins/default/CTM_parchment_texture_wh2.png';
	file_put_contents('export/CTM_panel_wh2', $uic->dumpFile());
}

// CTM_lord_btn
if (0){
	$h = fopen($DIR_DATA['frontend']['DIR'] . 'sp_grand_campaign', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic = new UIC();
	$uic->read($h);
	fclose($h);
	
	// button_faction_template
	$ch = $uic->child[0]->child[0]->child[1]->child[0]->child[1]->child[0]->child[0]->child[0];
	$ch->parent = $uic;
	$uic->child = array($ch);
	
	$ch->events = array();
	$ch->child = array();
	// $ch->b_01 = '01 01 00 00 00 00 01 01 01 00 00 01';
	$ch->b_01 = '01 01 00 00 00 00 01 00 00 00 00 01';
	$ch->offset = array('left' => 0, 'top' => 0);
	
	
	$ch->images[1]->path = 'ui\\skins\\default\\CTM_lord_portrait_frame.png';
	
	list($s_active,
		$s_down,
		$s_down_off,
		$s_hover,
		$s_selected) = $ch->states;
	
	$ch->states[] = $s_selected_hover = new UIC__State($s_selected);
	$ch->states[] = $s_selected_down = new UIC__State($s_down);
	$ch->states[] = $s_selected_down_off = new UIC__State($s_selected);
	$ch->states[] = $s_inactive = new UIC__State($s_active);
	$ch->states[] = $s_selected_inactive = new UIC__State($s_selected);
	
	$s_selected_hover->uid = '00 1F 40 21';
	$s_selected_down->uid = '40 21 53 21';
	$s_selected_down_off->uid = 'A0 FD B0 1F';
	$s_inactive->uid = 'D5 BB 9D 01';
	$s_selected_inactive->uid = 'FF AD 05 20';
	
	// active
	$a = $s_active;
	$a->mouse[] = new UIC__State_Mouse(array(
		'mouse_state' => 2,
		'state_uid' => $s_down->uid,
		'b0' => '00 00 00 00 00 00 00 00'
	), $a);
	
	// down
	$a = $s_down;
	array_splice($a->mouse, 0, 1);
	// $b = $a->mouse[0];
	// $b->state_uid = $s_selected_hover->uid;
	
	// selected
	$a = $s_selected;
	$a->b_mouse = '91 01 00 00 4D 01 00 00';
	$a->mouse = array(
		new UIC__State_Mouse(array(
			'mouse_state' => 0,
			'state_uid' => $s_selected_hover->uid,
			'b0' => 'C8 00 00 00 | 40 00 00 00'
		), $a),
		new UIC__State_Mouse(array(
			'mouse_state' => 2,
			'state_uid' => $s_selected_down->uid,
			'b0' => '00 00 00 00 00 00 00 00'
		), $a)
	);
	
	// selected_hover
	$a = $s_selected_hover;
	$a->name = 'selected_hover';
	$a->shadervars = array(1.0, 0, 0, 0);
	$a->b_mouse = '0D 01 00 00 D8 00 00 00';
	$a->mouse = array(
		new UIC__State_Mouse(array(
			'mouse_state' => 1,
			'state_uid' => $s_selected->uid,
			'b0' => 'C8 00 00 00 | 40 00 00 00'
		), $a),
		new UIC__State_Mouse(array(
			'mouse_state' => 2,
			'state_uid' => $s_selected_down->uid,
			'b0' => '00 00 00 00 | 00 00 00 00'
		), $a)
	);
	
	// selected_down
	$a = $s_selected_down;
	$a->name = 'selected_down';
	$a->shader_name = 'brighten_t0';
	$a->shadervars = array(0.5, 0, 0, 0);
	$a->b_mouse = '0C 01 00 00 78 00 00 00';
	$a->mouse = array(
		// #removed in agents update
		// new UIC__State_Mouse(array(
			// 'mouse_state' => 3,
			// 'state_uid' => $s_hover->uid,
			// 'b0' => '00 00 00 00 | 00 00 00 00'
		// ), $a),
		new UIC__State_Mouse(array(
			'mouse_state' => 1,
			'state_uid' => $s_selected_down_off->uid,
			'b0' => '00 00 00 00 | 00 00 00 00'
		), $a)
	);
	
	// selected_down_off
	$a = $s_selected_down_off;
	$a->name = 'selected_down_off';
	$a->shader_name = 'brighten_t0';
	$a->shadervars = $s_selected_hover->shadervars;
	$a->bgs[0]->colour = '7F 7F 7F FF';
	$a->b_mouse = '80 01 00 00 15 00 00 00';
	$a->mouse = array(
		new UIC__State_Mouse(array(
			'mouse_state' => 0,
			'state_uid' => $s_selected_down->uid,
			'b0' => '00 00 00 00 | 00 00 00 00'
		), $a),
		new UIC__State_Mouse(array(
			'mouse_state' => 8,
			'state_uid' => $s_selected->uid,
			'b0' => '00 00 00 00 | 00 00 00 00'
		), $a)
	);
	
	// inactive
	$a = $s_inactive;
	$a->name = 'inactive';
	$a->shader_name = 'set_greyscale_t0';
	$a->shadervars = array(1, 1, 0, 0);
	$a->b_mouse = '00 00 00 00 00 00 00 00';
	$a->mouse = array();
	
	// selected_inactive
	$a = $s_selected_inactive;
	$a->name = 'selected_inactive';
	$a->b_mouse = '00 00 00 00 00 00 00 00';
	$a->mouse = array();
	
	$ch->states = array(
		$s_active,
		$s_hover,
		$s_down,
		$s_down_off,
		$s_selected,
		$s_selected_hover,
		$s_selected_down,
		$s_selected_down_off,
		$s_inactive,
		$s_selected_inactive
	);
	
	foreach ($ch->states as $state){
		$state->bounds = array(51, 69);
		$state->bgs[0]->bounds = array(51, 69);
		$state->bgs[1]->bounds = array(51, 69);
	}
	
	$ch->dynamic = array();
	$ch->after[1] = '';
	
	// var_dump($uic->debug());exit;
	
	file_put_contents('export/CTM_lord_btn', $uic->dumpFile());
}

// CTM_traits_button
if (0){
	// $h = fopen($DIR_DATA['campaign']['DIR'] . 'character_details_panel', 'r');
	$h = fopen($DIR_DATA['campaign']['DIR'] . 'character_information', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic = new UIC();
	$uic->read($h);
	fclose($h);
	
	$uic->events = array();
	
	// SI_dial > button_parent
	// $ch = $uic->child[0]->child[5];
	$ch = $uic->child[0]->child[10];
	$ch->parent = $uic;
	$uic->child = array($ch);
	
	$ch->events = array();
	$ch->offset = array('left' => 0, 'top' => 0);
	$ch->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
	$ch->docking = 1;
	$ch->dock_offset = array('left' => 52, 'top' => 150);
	$ch->images[0]->path = 'ui\\skins\\default\\CTM_rank_dspl_back.png';
	$ch->dynamic = array();
	$ch->funcs = array();
	
	$ch->Resize(50, 50);
	
	$ch = $ch->child[0];
	$ch->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
	array_splice($ch->child, 5, 1);
	
	$ch->child[0]->events = array();
	$ch->child[0]->b_01 = '00 00 00 00 00 00 01 01 00 00 00 01';
	
	$ch->child[1]->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
	$ch->child[1]->images[0]->path = 'ui\\skins\\default\\CTM_rank_dspl_zero.png';
	
	$ch->child[2]->events = array();
	$ch->child[2]->b_01 = '00 00 00 00 00 00 01 01 00 00 00 01';
	$ch->child[2]->funcs = array();
	
	$ch->child[3]->events = array();
	$ch->child[3]->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
	$ch->child[3]->images[0]->path = 'ui/skins/default/CTM_rank_dspl_frame.png';
	array_splice($ch->child[3]->states, 4, 1); // inactive
	$ch->child[3]->dynamic = array();
	$ch->child[3]->after[1] = '';
	
if (false){
    // 'Norse-Bold',
    // 'la_gioconda_uppercase',
    // 'la_gioconda',
    // 'Norse',
    // 'georgia_italic',
    // 'Calligraph421'
	
	$cats = array(
		'Default Font Category',
		'fe_paragraph_heading',
		'fe_text',
		'fe_section_heading',
		'ingame_text',
		'fe_paragraph_heading_inactive',
		'ingame_text_small',
		'FE_header_small',
		'parchment_text',
		'ingame_tooltip',
		'fe_tooltip_text',
		'fe_page_heading',
		'fe_page_heading_red',
		'fe_page_heading_green',
		'fe_paragraph_heading_red',
		'fe_paragraph_heading_green',
		'parchment_header',
		'fe_text_small',
		'fe_text_inactive',
		'fe_default_text',
		'fe_italic',
		'fe_tooltip_red',
		'fe_tooltip_green',
		'grey_italic',
		'fe_text_bold',
		'ingame_panel_heading',
		'ingame_paragraph_heading_red',
		'parchment italic',
		'parchment_header_small',
		'parchment_text_inactive',
		'fe_text_red',
		'fe_paragraph_heading_blue',
		'chat',
		'ingame_paragraph_heading',
		'fe_text_blue',
		'ingame_text_red',
		'fe_text_gold',
		'Grudges_header',
		'Grudges text',
		'fe_text_green',
		'Scroll_text',
		'Scroll_section_header',
		'Grudges subheader',
		'Grudges_page_header',
		'fe_button_text',
		'fe_tooltip_small_red',
		'fe_tooltip_small_green',
		'fe_tooltip_green_light',
		'parchment_header_large',
		'fe_hyperlink',
		'parchment_header_small_grey',
		'ingame_section_heading',
		'ingame_text_small_grey',
		'fe_hyperlink_small',
		'ingame_text_green',
		'scripted_objective',
		'fe_text_yellow',
		'inagme_text_grey',
		'fe_text_small_inactive',
		'subtitle',
		'top_centre_subtitle',
		'ingame_tooltip_italic',
		'lab_battle_header',
		'text_orange',
		'credits_text',
		'credits_header',
		'credits_subheader',
		'fe_input_text'
	);
}
	
	$ch = $ch->child[4];
	$ch->b_01 = '00 00 00 00 00 00 01 00 00 00 00 01';
	$state = $ch->states[0];
	// $state->textbounds = array('width' => 2, 'height' => 4);
	$state->font_m_font_name = 'la_gioconda';
	$state->fontcat_name = 'fe_text_small';
	$state->font_m_size = 8;
	$state->textshadervars = array(0, 0, 0, 1);
	// $state->b7 = '00 00 01 01';
	$state->textlabel = '';
	
// добавляем анимацию
if (true){
	$h = fopen($DIR_DATA['campaign']['DIR'] . 'layout', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic_a = new UIC();
	$uic_a->read($h);
	fclose($h);
	
	$ch_a = $uic_a->child[0]->child[5]->child[0];
	$ch = $uic->child[0];
	$ch->docking = 7;
	$ch->dock_offset = $offset = array('left' => 52, 'top' => -62);
	// $offset_hide = array('left' => $offset['left'], 'top' => $offset['top'] + 258 * 2);
	$offset_hide = array('left' => $offset['left'], 'top' => $offset['top'] + 40);
	
	// $ch->states[0]->b7 = '01 00 00 00';
	$ch->funcs = array(
		new UIC__Func($ch_a->funcs[0], $ch)
	);
	// перевод в 106
	if (true){
		$uic->eachChild(function($ch){
			$ch->events = '';
			$ch->b5 = '00 00 00 00';
			foreach ($ch->states as $state){
				$state->tooltip_id = '';
				$state->b5 = '00 00';
			}
			$ch->after = array('00', '', '', '00', '00', '00 00 00');
		});
		
		$ch->funcs[0]->anim[0]->str_sth = '';
		$ch->funcs[0]->anim[0]->b3 = '00 00';
		
		$uic->setVersion(106);
	}
	
	// show
	$func = $ch->funcs[0];
	$anim = $func->anim[0];
	$anim->offset = $offset_hide;
	$anim->bounds = $ch->states[0]->bounds;
	$anim->b2 = '01 00';
	$anim->interpolationtime = 0;
	
	$func->anim[] = $anim = new UIC__Func_Anim($anim);
	$anim->offset = $offset;
	$anim->interpolationtime = 500;
	
	// showlong
	$ch->funcs[] = $func = new UIC__Func($ch->funcs[0], $ch);
	$func->name = 'showlong';
	$anim = $func->anim[0];
	$anim->offset['top'] += 20;
	
	$anim = $func->anim[1];
	$anim->interpolationtime = 600;
	
	// showleft
	$ch->funcs[] = $func = new UIC__Func($ch->funcs[0], $ch);
	$func->name = 'showleft';
	$anim = $func->anim[0];
	$anim->offset['left'] -= 20;
	
	// array_splice($func->anim, 1, 0, array($anim = new UIC__Func_Anim($anim)));
	// $anim->interpolationtime = round(700 * 20 / 32);
	
	$anim = $func->anim[1];
	$anim->interpolationtime = round(700 * 20 / 32);
	
	if (0){
		$ch->funcs[] = $func = new UIC__Func($func);
		$func->name = 'immediate';
		array_splice($func->anim, 1);
		$anim = $func->anim[0];
		// $anim->colour = 'FF FF FF FF';
		$anim->offset = $offset;
	}
}
	
	// var_dump($uic->debug());exit;
	
	file_put_contents('export/CTM_traits_button', $uic->dumpFile());
}

// CTM_mortuary_cult
if (0){
	$h = fopen($DIR_DATA['campaign']['DIR'] . 'mortuary_cult', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic = new UIC();
	$uic->read($h);
	fclose($h);
	
	// mortuary_cult
	$ch = $uic->child[0];
	
	if (1){
		$ov = new UIC($ch);
		$uic->child[0] = $ov;
		$ov->child = array($ch);
		$ch->my = $ov;
		
		$ov->name = 'overlay';
		$ov->events = array();
		$ov->offset = array('left' => 0, 'top' => 0);
		array_splice($ov->images, 1);
		$ov->images[0]->path = '';
		$ov->images[0]->width = 1;
		$ov->images[0]->height = 1;
		$ov->states[0]->bounds = array(1920 * 4, 1080 * 4); // 50% in 4K
		array_splice($ov->states[0]->bgs, 0, 1);
		array_splice($ov->states[0]->bgs, 1);
		$ov->states[0]->bgs[0]->bounds = $ov->states[0]->bounds;
		$ov->states[0]->bgs[0]->colour = 'FF FF FF 00';
		$ov->states[0]->bgs[0]->tile = 0;
		$ov->states[0]->bgs[0]->margin = array(0, 0, 0, 0);
	}
	
	// help pages button
	array_splice($ch->child, 1, 1);
	
	// panel_title
	$state = $ch->child[0]->child[0]->states[0];
	$state->text = '';//'{{tr:traits}}';
	$state->textlabel = 'tx_traits_NewState_Text_680041';
	
	// panel_heading_NewState_Text_350043
	// tx_subtitle_NewState_Text_680041
	// heading_traits_NewState_Text_680041
	// tx_header_NewState_Text_680041
	// tx_subtitle_NewState_Text_680041
	
	// listview
	$list = $ch->child[1];
	// list_clip -> list_box
	$box = $list->child[0]->child[0];
	$box->child = array();
	$box->after[2][1] = array();
	// headers
	array_splice($list->child, 2, 1);
	
	// resources_holder
	array_splice($ch->child, 3, 2);
	// header_list
	$head = $ch->child[3];
	// button_recycle_holder
	array_splice($head->child, 3, 1);
	
	// no_items_panel
	array_splice($ch->child, 4, 1);
	// header_list
	array_splice($ch->child, 3, 1);
	
	$ch->events = array();
	$list->dynamic = array();
	
	$list->child[1]->template[1]->dynamic = array(
		array('stepSize', '10')
	);
	$list->child[1]->template[3]->dynamic = array(
		array('min_size', '39')
	);
	$list->child[1]->template[4]->dynamic = array(
		array('stepSize', '10')
	);
	$list->child[1]->template[5]->b_floats[3] += 23;
	$list->child[1]->template[5]->dynamic = array(
		array('Value', '0'),
		array('minValue', '0'),
		array('maxValue', '100'),
		array('Notify', '')
	);
	array_splice($ch->child, 2, 0, array($list = new UIC($list)));
	
	$right_width = 253;
	$list->states[0]->bounds[0] = $right_width;
	$clip = $list->child[0];
	$clip->dock_offset = array('left' => 8, 'top' => 8);
	$clip->states[0]->bounds = array($right_width - 8 - 12, 640 - 8 * 2);
	$clip->child[0]->states[0]->bounds = $clip->states[0]->bounds;
	
	$list->uid = '80 67 EC 1B';
	$list->child[0]->uid = '80 BE F8 24';
	$list->child[0]->child[0]->uid = '00 9F C3 25';
	$list->docking = 3;
	$list->dock_offset['left'] = -16;
	$list->images[] = $im = new UIC__Image(array(
		'uid' => '11 BA 52 02',
		'path' => 'ui\\skins\\default\\parchment_texture.png',
		'width' => 256,
		'height' => 256,
		'extra' => '00'
	), $list);
	$state = $list->states[0];
	$state->bgs[] = $bg = new UIC__State_Background($uic->states[0]->bgs[0], $state);
	$bg->uid = $im->uid;
	$bg->bounds = $state->bounds;
	$bg->colour = 'FF FF FF FF';
	$bg->tile = 1;
	$bg->margin = array(50, 50, 50, 50);
	$list->child[1]->template[5]->b_floats[1] += 14;
	$list->child[1]->template[5]->b_floats[3] -= 28;
	
	$list = $ch->child[1];
	$list->docking = 1;
	$list->dock_offset['left'] = 16;
	for_all($list, function($uic){
		global $right_width;
		$uic->states[0]->bounds[0] -= $right_width + 8;
	});
	
	// CTM
	$uic->eachChild(function($uic){
		if ($uic instanceof UIC){
			foreach ($uic->images as $im){
				$pos = strpos($im->path, 'ui\\skins\\default\\');
				if ($pos !== false){
					$im->path = str_replace('ui\\skins\\default\\', 'ui\\skins\\default\\CTM_', $im->path);
				}
			}
		}
	});
	
	file_put_contents('export/CTM_mortuary_cult', $uic->dumpFile());
	
	// warhammer2
	$uic->eachChild(function($uic){
		if ($uic instanceof UIC){
			foreach ($uic->images as $im){
				$pos = strpos($im->path, 'ui\\skins\\default\\');
				if ($pos !== false){
					$im->path = mb_substr($im->path, 0, mb_strlen($im->path) - 4) .'_wh2.png';
				}
			}
		}
	});
	
	file_put_contents('export/CTM_mortuary_cult_wh2', $uic->dumpFile());
	
	
	
	
	$h = fopen($DIR_DATA['templates']['DIR'] . 'parchment_row', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic_g = $uic;
	
	$uic = new UIC();
	$uic->read($h);
	$ch = $uic->child[0];
	fclose($h);
	
	array_splice($ch->images, 0, 0, array($im = new UIC__Image(array(
		'uid' => '70 0B 01 0F',
		'path' => 'ui\\skins\\default\\construction_positive.png',
		'width' => 1,
		'height' => 0,
		'extra' => '00'
	), $ch)));
	
	$bounds = array(253 - 8 - 12, 32 + 3 * 2);
	function TF_SetBg($state){
		global $bounds, $im;
		$state->bounds = $bounds;
		$state->textbounds = array('width' => 2, 'height' => 4);
		$state->font_m_font_name = 'la_gioconda';
		$state->font_m_size = 12;
		$state->font_m_colour = '00 00 00 FF';
		$state->fontcat_name = 'ingame_text';
		$state->textoffset = array(38, 2, 7, 0);
		foreach ($state->bgs as $bg){
			// $bg->bounds = array($bounds[0], $bounds[1] + 3);
			// if ($bg->offset['top'] < 0){ $bg->offset['top'] = 0; }
			$bg->bounds = array($bounds[0], 38); // 31
			$bg->dockpoint = 5;
			// $bg->dockpoint = 8;
		}
		$state->bgs[] = $bg = new UIC__State_Background($state->bgs[0]);
		$bg->uid = $im->uid;
		$bg->bounds = array(24, 24);
		$bg->colour = 'FF FF FF FF';
		$bg->tile = 0;
		$bg->offset = array('left' => 10, 'top' => 3 + 4);
		$bg->dockpoint = 4;
		$bg->dock = array('right' => 0, 'bottom' => 0);
		$bg->margin = array(0, 0, 0, 0);
	}
	
	// invalid
	array_splice($ch->states, 6, 1);
	
	foreach ($ch->states as $state){
		TF_SetBg($state);
	}
	
	
	$ch->states[] = $state = new UIC__State($ch->states[5]); // down
	$state->uid = '40 21 53 21';
	$state->name = 'selected_down';
	
	$ch->states[] = $state = new UIC__State($ch->states[3]); // down_off
	$state->uid = 'A0 FD B0 1F';
	$state->name = 'selected_down_off';
	
	$states = array(
		'hover' => $ch->states[0],
		'selected_hover' => $ch->states[1],
		'unselected' => $ch->states[2],
		'down_off' => $ch->states[3],
		'selected' => $ch->states[4],
		'down' => $ch->states[5],
		'selected_down' => $ch->states[6],
		'selected_down_off' => $ch->states[7]
	);
	
	// hover
	// selected_hover
	$states['selected_hover']->mouse[] = new UIC__State_Mouse(array(
		'mouse_state' => 2,
		'state_uid' => $states['selected_down']->uid,
		'b0' => '00 00 00 00 00 00 00 00',
		'sth' => array()
	), $states['selected_hover']);
	// unselected
	// down_off
	// selected
	// down
	$m = $states['down']->mouse;
	$m[0]->state_uid = $states['selected_hover']->uid;
	// selected_down
	$m = $states['selected_down']->mouse;
	$m[0]->state_uid = $states['hover']->uid;
	$m[1]->state_uid = $states['selected_down_off']->uid;
	// selected_down_off
	$m = $states['selected_down_off']->mouse;
	$m[0]->state_uid = $states['selected_down']->uid;
	$m[1]->state_uid = $states['selected']->uid;
	
	foreach ($ch->images as $i => $im){
		if ($i > 0){
			$im->path = str_replace('\\default\\', '\\default\\CTM_', $im->path);
		}
	}
	
	file_put_contents('export/CTM_trait_filter', $uic->dumpFile());
	
	// warhammer2
	foreach ($ch->images as $i => $im){
		if ($i > 0){
			$im->path = str_replace('.png', '_wh2.png', $im->path);
		}
	}
	
	file_put_contents('export/CTM_trait_filter_wh2', $uic->dumpFile());
}

// CTM_add_trait_template
if (0){
	$h = fopen($DIR_DATA['export']['DIR'] . 'CTM_trait_template', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic = new UIC();
	$uic->read($h);
	fclose($h);
	
	$ch = $uic->child[0];
	
	array_splice($ch->states, 0, 5);
	array_splice($ch->states, 4, 1); // inactive
	$ch->default_state = $ch->states[0]->uid;
	foreach ($ch->states as $state){
		$state->name = mb_substr($state->name, 0, mb_strlen($state->name) - 9);
		$state->bgs[1]->colour = '5F 98 AF FF';
	}
	
	
	$h = fopen($DIR_DATA['export']['DIR'] . 'CTM_trait_dy_trait', 'r');
	if (!$h){ throw new Exception('FILE'); }
	
	$uic_dy = new UIC();
	$uic_dy->read($h);
	fclose($h);
	
	$ch->child[] = $uic_dy->child[0];
	$ch->child[0]->parent = $ch;
	$ch = $ch->child[0];
	$ch->images[0]->path = 'ui/skins/default/CTM_icon_plus_small.png';
	array_splice($ch->states, 1); // inactive
	foreach ($ch->states as $state){
		$state->bounds = array(253 - 10 - 2, 62 - 5);
		$state->bgs[0]->offset['top'] = 14;
	}
	
	file_put_contents('export/CTM_add_trait_template', $uic->dumpFile());
	
	// wh2
	$ch = $ch->parent;
	$ch->images[0]->path = 'ui\skins\default\CTM_parchment_button_square_hover_wh2.png';
	foreach ($ch->states as $state){
		$state->bgs[1]->colour = '91 99 9B FF';
	}
	$ch = $ch->child[0];
	$ch->images[0]->path = 'ui/skins/default/CTM_icon_plus_small_wh2.png';
	
	file_put_contents('export/CTM_add_trait_template_wh2', $uic->dumpFile());
}


