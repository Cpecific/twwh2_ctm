<?php

error_reporting(E_ALL ^ E_NOTICE);

function my_assert($bool, $obj = null){
	if ($bool){ return; }
	global $h;
	$obj->pos = ftell($h);
	fseek($h, 0, SEEK_END);
	$obj->diff = ftell($h) - $obj->pos;
	$obj->error = true;
	// var_dump($obj);
	// var_dump($obj->debug());
	// var_dump($obj->root()->debug());
	// global $path;
	throw new Exception();
}
function my_unpack_one($obj, ...$args){
	$r = call_user_func_array('unpack', $args);
	if ($r === false){ my_assert(false, $obj); }
	return $r[1];
}
function my_unpack_array($obj, ...$args){
	$r = call_user_func_array('unpack', $args);
	if ($r === false){ my_assert(false, $obj); }
	return array_values($r);
}
function tohex($str){
	return strtoupper(implode(' ', str_split(bin2hex($str), 2)));
}
/*function hex2bin($hexstr){ 
	$n = strlen($hexstr);
	$sbin = '';
	for ($i = 0; $i < $n; $i += 2){
		$a = substr($hexstr, $i, 2);
		$c = pack('H*', $a);
		if ($i === 0){ $sbin = $c; }
		else{ $sbin .= $c; }
	}
	return $sbin;
}*/
function fromhex($str){
	if (is_array($str)){
		$res = '';
		foreach ($str as $a){
			$res .= fromhex($a);
		}
		return $res;
	}
	return hex2bin(str_replace(array(' | ', ' '), '', $str));
}
function tobits($hex){
	if (strpos($hex, ' ') !== false){
		$val = array();
		foreach (explode(' ', $hex) as $a){
			$val[] = tobits($a);
		}
		return implode(' ', $val);
	}
	$val = '';
	for ($i = 0; $i < 2; ++$i){
		$a = $hex[ $i ];
		if (!in_array($a, array('A', 'B', 'C', 'D', 'E', 'F'))){ $b = (int)$a; }
		else{ $b = 10 + ord($a) - ord('A'); }
		for ($j = 0; $j < 4; ++$j){
			$val .= (($b >> (3 - $j)) & 0x1);
		}
	}
	return $val;
}
function read_string($h, $num, $obj, $check_print = true){
	$arr = array();
	for (; $num > 0; --$num){
		$len = unpack('S1', fread($h, 2));
		if ($len === false){ my_assert(false, $obj); }
		// my_assert($len[1] < 1000, $obj);
		if ($len[1] > 0){
			$arr[] = $str = fread($h, $len[1]);
			// if ($check_print){ my_assert(ctype_print($str), $obj); }
		}
		else{ $arr[] = ''; }
	}
	if (sizeof($arr) === 1){ return $arr[0]; }
	return $arr;
}
function write_string($str){
	if (is_array($str)){
		$res = '';
		foreach ($str as $a){
			$res .= write_string($a);
		}
		return $res;
	}
	return pack('Sa'. strlen($str), strlen($str), $str);
}
function read_utf8_string($h, $num, $obj){
	$arr = array();
	for (; $num > 0; --$num){
		$len = unpack('S1', fread($h, 2));
		$len = $len[1];
		// my_assert($len < 1000, $obj);
		if ($len > 0){
			$arr[] = $str = mb_convert_encoding(fread($h, $len * 2), 'UTF-8', 'UTF-16LE');
			// my_assert(ctype_print($str), $obj);
		}
		else{ $arr[] = ''; }
	}
	if (sizeof($arr) === 1){ return $arr[0]; }
	return $arr;
}
function write_utf8_string($str){
	if (is_array($str)){
		$res = '';
		foreach ($str as $a){
			$res .= write_utf8_string($a);
		}
		return $res;
	}
	$len = strlen($str);
	return pack('Sa'. ($len * 2), $len, mb_convert_encoding($str, 'UTF-16LE', 'UTF-8'));
}
function file_strpos($h, $ar){
	$pos = 0;
	for (; !feof($h); ++$pos){
		$c = unpack('C1', fread($h, 1));
		if ($c[1] === $ar[0]){
			for ($i = 1; $i < sizeof($ar); ++$i){
				$c = unpack('C1', fread($h, 1));
				if ($c[1] !== $ar[ $i ]){
					fseek($h, -$i + 1, SEEK_CUR);
					continue 2;
				}
			}
			return $pos;
		}
	}
	return false;
}

function read_double_4($h, &$info, $num){
	for (; $num > 0; --$num){
		$info[] = tohex(fread($h, 4)) .' | '. tohex(fread($h, 4));
	}
}

class UIC {
	#region vars
	public $version;
	public $parent;
	
	public $uid = null;
	public $b_sth = null;
	public $name;
	public $b0;
	public $num_events = null;
	public $events = array();
	public $offset = array( 'left' => null, 'top' => null );
	public $b1;
	public $b_01;
	public $tooltip_text;
	public $tooltip_id;
	public $docking;
	public $dock_offset = array( 'left' => null, 'top' => null );
	public $b3;
	public $default_state;
	public $num_images;
	public $images = array();
	public $maskimage;
	public $b5;
	
	public $b_sth2 = null;
	public $num_states;
	public $states = array();
	
	public $b_sth3 = null;
	public $num_dynamic;
	public $dynamic = array();
	
	public $b6;
	public $num_funcs;
	public $funcs = array();
	
	public $child = array();
	public $num_child;
	
	public $after = array();
	public $child_idx;
	#endregion
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		foreach ($a as $k => $v){
			if (!property_exists('UIC', $k)){ continue; }
			if ($k === 'images'){
				foreach ($v as $image){
					$this->images[] = new UIC__Image($image, $this);
				}
			} else if ($k === 'states'){
				foreach ($v as $state){
					// var_dump($a->name .'.'. $state->name);
					$this->states[] = new UIC__State($state, $this);
				}
			} else if ($k === 'dynamic'){
				foreach ($v as $dynamic){
					$this->dynamic[] =  new UIC__Dynamic($dynamic, $this);
				}
			} else if ($k === 'funcs'){
				foreach ($v as $func){
					$this->funcs[] = new UIC__Func($func, $this);
				}
			} else if ($k === 'child'){
				foreach ($v as $child){
					if ($child instanceof UIC){
						$this->child[] = new UIC($child, $this);
					} else{
						$this->child[] = new UIC_Template($child, $this);
					}
				}
			} else{
				$this->$k = $v;
			}
		}
		if ($parent !== null){ $this->parent = $parent; }
	}
	public function read($h, $parent = null){
		$this->parent = $parent;
		if ($parent === null){
			$this->version = (int)substr(fread($h, 10), 7);
		}
		else{
			$this->version = $parent->version;
			$this->child_idx = sizeof($parent->child) - 1;
		}
		
		$v = $this->version;
		
		$this->uid = tohex(fread($h, 4));
		if ($v >= 126 && $v < 130){
			$this->b_sth = tohex(fread($h, 16));
		}
		$this->name = read_string($h, 1, $this);
		$this->b0 = read_string($h, 1, $this);
		
		// вообще может быть ExitFunc + у меня крашилось, когда я удалял
		// элемент, и в нём были events
		if ($v >= 100 && $v < 110){
			// название event? может быть core listener?
			// this bitch is definetly context object, but how to use it?
			// and of course it's not events key, but it's still too early to name it context data,
			// since we have no idea how to use it.
			$this->events = read_string($h, 1, $this); // CustomTooltip (tooltip_effect_bundle)
		}
		else if ($v >= 110 && $v < 130){
			if ($v === 113){
				$this->num_events = 1;
			} else{
				$this->num_events = my_unpack_one($this, 'l', fread($h, 4));
			}
			my_assert($this->num_events < 20, $this);
			for ($i = 0; $i < $this->num_events; ++$i){
				$a = read_string($h, 3, $this);
				if ($v >= 124 && $v < 130){
					$num_sth = my_unpack_one($this, 'l', fread($h, 4));
					$b = array();
					for ($j = 0; $j < $num_sth; ++$j){
						$b[] = read_string($h, 2, $this);
					}
					$a[] = $b;
				}
				$this->events[] = $a;
			}
		}
		
		// относительно parent (разумеется)
		list($this->offset['left'],
			$this->offset['top']) = my_unpack_array($this, 'l2', fread($h, 4 * 2));
		
		if ($v >= 70 && $v < 90){ $this->b1 = tohex(fread($h, 1)); }
		
		/*
		// (confirmed) allowhorizontalresize, allowverticalresize, moveable
		
		// (unconfirmed)
		// (suggest)
		// renderwhendragged
		// renderifroot
		// renderlastonfocused
		// useglobalclicks
		// updatewhennotvisible
		// isaspectratiolocked
		// isrelativeresize
		// ?, ?, ?
		// checked updatewhennotvisible for animations, none of the helped.
		// maybe updatewhennotvisible isn't here, ot it just doesn't influence animations.
		
		// (suppose confirmed) visible, clipchildren, clipimagestocomponent
		
		// (unconfirmed)
		// locked
		// fontscale
		// ?, ?, 01
		*/
		$this->b_01 = tohex(fread($h, 12)); // 01 01 ...
		
		$this->tooltip_text = read_utf8_string($h, 1, $this); // Sort by public order
		$this->tooltip_id = read_utf8_string($h, 1, $this); // sort_happines_Tooltip_5a0034
		list($this->docking,
			$this->dock_offset['left'],
			$this->dock_offset['top']) = my_unpack_array($this, 'l3', fread($h, 4 * 3));
			
		// component_anchor_point
		// priority
		$this->b3 = tohex(fread($h, 1));
		
		// sometimes it's zeros. but don't worry, it still selects a (first?) state.
		$this->default_state = tohex(fread($h, 4));
		
		$this->num_images = my_unpack_one($this, 'l', fread($h, 4));
		my_assert($this->num_images < 20, $this);
		for ($i = 0; $i < $this->num_images; ++$i){
			$this->images[] = $a = new UIC__Image();
			$a->read($h, $this);
		}
		
		$this->maskimage = tohex(fread($h, 4));
		
		if ($v >= 70 && $v < 110){
			$this->b5 = tohex(fread($h, 4)); // ?, ?
		}
		
		// tooltipslocalised
		// componentleveltooltip
		// tooltiplabel
		// soundcategory
		// layouttransition
		// locked
		// marked_for_deletion
		// comment
		if ($v >= 126 && $v < 130){
			$this->b_sth2 = tohex(fread($h, 16));
		}
		$this->num_states = my_unpack_one($this, 'l', fread($h, 4));
		// my_assert($this->num_states < 64, $this);
		for ($i = 0; $i < $this->num_states; ++$i){
			$this->states[] = $a = new UIC__State();
			$a->read($h, $this);
		}
		
		if ($v >= 126 && $v < 130){
			$this->b_sth3 = tohex(fread($h, 16));
		}
		$this->num_dynamic = my_unpack_one($this, 'l', fread($h, 4));
		my_assert($this->num_dynamic < 20, $this);
		for ($i = 0; $i < $this->num_dynamic; ++$i){
			$this->dynamic[] = $a = new UIC__Dynamic();
			$a->read($h, $this);
		}
		
		// (suppose confirmed) priority?
		$this->b6 = tohex($num_sth = fread($h, 4)); // unknown
		
		$this->num_funcs = my_unpack_one($this, 'l', fread($h, 4));
		my_assert($this->num_funcs < 20, $this);
		global $has;
		if ($this->num_funcs > 0){ $has['funcs'] = true; }
		for ($i = 0; $i < $this->num_funcs; ++$i){
			$this->funcs[] = $a = new UIC__Func();
			$a->read($h, $this);
		}
		
		$this->num_child = my_unpack_one($this, 'l', fread($h, 4));
		
		if ($v >= 70 && $v < 100){
			for ($i = 0; $i < $this->num_child; ++$i){
				$uic = new UIC();
				$this->child[] = $uic;
				$uic->read($h, $this);
			}
		}
		else if ($v >= 100 && $v < 130){
			for ($i = 0; $i < $this->num_child; ++$i){
				$bits = tohex(fread($h, 2));
				if ($bits === '00 00'){
					$uic = new UIC();
					$this->child[] = $uic;
					$uic->read($h, $this);
				}
				else{
					fseek($h, -2, SEEK_CUR);
					$uic = new UIC_Template();
					$this->child[] = $uic;
					$uic->read($h, $this);
				}
			}
		}
		
		$this->readAfter($h);
		
		if ($this->parent === null){
			$this->pos = ftell($h);
			fseek($h, 0, SEEK_END);
			$this->diff = ftell($h) - $this->pos;
			my_assert($this->diff === 0, $this);
		}
	}
	public function readAfter($h){
		$v = $this->version;
		
		$this->after[] = tohex(fread($h, 1));
		$this->after[] = ($type = read_string($h, 1, $this));
		
		if ($v >= 70 && $v < 80){
			if ($type === 'List'){
				$a = array();
				
				$a[] = 'num_sth = '. tohex($num_sth = fread($h, 4));
				$num_sth = my_unpack_one($this, 'l', $num_sth);
				my_assert($num_sth < 10, $this);
				$b = array();
				for ($i = 0; $i < $num_sth; ++$i){
					$b[] = tohex(fread($h, 4));
				}
				$a[] = $b;
				$a[] = tohex(fread($h, 21));
				
				$this->after[] = $a;
			}
			else{
				if ($v === 79){
					$this->after[] = tohex(fread($h, 2));
					if ($this->num_child !== 0){
						$this->after[] = tohex(fread($h, 4));
					}
				} else{
					$this->after[] = tohex(fread($h, 6));
				}
				if ($type){ $this->after[] = tohex(fread($h, 1)); }
			}
			return;
		}
		else if ($v >= 80 && $v < 90){
			if ($v >= 80 && $v < 85){
				$this->after[] = tohex(fread($h, 5));
			}
			else{
				$this->after[] = tohex(fread($h, 6));
			}
			return;
		}
		
		global $has; $has_type = true;
		if ($type === 'List'){
			$has['list'] = true;
			$a = array();
			
			$a[] = 'num_sth = '. tohex($num_sth = fread($h, 4));
			$num_sth = my_unpack_one($this, 'l', $num_sth);
			my_assert($num_sth < 10, $this);
			// array of floats - strange values: several hundreds
			// or maybe they are not floats?
			// kinda confirmed 2 first values, it is restrictions for children in width and height
			// it is kinda buggy, my CTM_trait_template retains it's bounds,
			// but it's children were confined to height, so they were inline
			$b = array();
			for ($i = 0; $i < $num_sth; ++$i){
				$b[] = my_unpack_one($this, 'f1', fread($h, 4));
			}
			$a[] = $b;
			
			// values: 0, -5
			$a[] = my_unpack_one($this, 'l', fread($h, 4));
			// values: 0, 1, 4, 5, 14
			$a[] = my_unpack_one($this, 'l', fread($h, 4));
			// bool
			$a[] = $bool = tohex(fread($h, 1));
			// values: 1, 3, 20
			$a[] = my_unpack_one($this, 'l', fread($h, 4));
			
			if ($v >= 91 && $v < 97){
				$a[] = $bit = tohex(fread($h, 1));
				if ($v === 96){
					$a[] = tohex(fread($h, 5));
				}
				$a[] = tohex(fread($h, 2));
			}
			else if ($v >= 97 && $v < 100){
				$a[] = tohex(fread($h, 6));
				$a[] = read_string($h, 1, $this);
				$a[] = tohex(fread($h, 5));
			}
			else{
				// {00 00 | 01 00}
				$a[] = tohex(fread($h, 2));
				// margins?
				$a[] = my_unpack_one($this, 'l', fread($h, 4));
				$a[] = my_unpack_one($this, 'l', fread($h, 4));
				$a[] = my_unpack_one($this, 'l', fread($h, 4));
				$a[] = my_unpack_one($this, 'l', fread($h, 4));
				
				$len = 19;
				if ($v >= 100 && $v < 110){
					if ($v >= 100 && $v <= 101){
						$len = 2;
					}
					else if ($v >= 102 && $v <= 104){
						// 3 bytes, short?, bool?
						$len = 6;
					}
					else if ($v === 105){
						$len = 11;
					}
					else if ($v === 106){
						$len = 10;
					}
				}
				else if ($v === 113){
					$len = 14;
				}
				else if ($v >= 110 && $v < 120){
					// int, bool, bool, int
				}
				else if ($v >= 120 && $v < 127){
					$len = 26;
				}
				else if ($v >= 127 && $v < 130){
					$len = 29;
				}
				$a[] = tohex(fread($h, $len));
				
				if ($v === 106 || $v >= 110 && $v < 130){
					$a[] = read_string($h, 1, $this);
				}
				
				if ($v === 106){
					// int?, short?, bool?
					$a[] = tohex(fread($h, 7));
				}
			}
			
			$this->after[] = $a;
		}
		else if ($type === 'HorizontalList'){
			$has['hlist'] = true;
			$a = array();
			
			$a[] = 'num_sth = '. ($num_sth = my_unpack_one($this, 'l', fread($h, 4)));
			$b = array();
			for ($i = 0; $i < $num_sth; ++$i){
				$b[] = my_unpack_one($this, 'f1', fread($h, 4));
			}
			$a[] = $b;
			
			$a[] = my_unpack_one($this, 'l', fread($h, 4));
			$a[] = tohex(fread($h, 5));
			$a[] = my_unpack_one($this, 'l', fread($h, 4));
			
			if ($v >= 91 && $v < 97){
				$a[] = $bit = tohex(fread($h, 1));
				if ($v === 96){
					$a[] = tohex(fread($h, 5));
				}
				$a[] = tohex(fread($h, 2));
			}
			else if ($v >= 97 && $v < 100){
				$a[] = tohex(fread($h, 6));
				$a[] = read_string($h, 1, $this);
				$a[] = tohex(fread($h, 5));
			}
			else{
				$a[] = my_unpack_one($this, 'S1', fread($h, 2));
				
				// padding = {left, right, top, bottom} (for actual content)
				$a[] = my_unpack_one($this, 'l', fread($h, 4));
				$a[] = my_unpack_one($this, 'l', fread($h, 4));
				$a[] = my_unpack_one($this, 'l', fread($h, 4));
				$a[] = my_unpack_one($this, 'l', fread($h, 4));
			
				$a[] = my_unpack_one($this, 'l', fread($h, 4));
				$a[] = my_unpack_one($this, 'S1', fread($h, 2));
				
				if ($v == 105){
					$a[] = my_unpack_one($this, 'l', fread($h, 4));
					$a[] = tohex(fread($h, 1));
				}
				else if ($v >= 106){
					$a[] = my_unpack_one($this, 'l', fread($h, 4));
					// 106:
					// last 3 bytes: short, byte
					$a[] = read_string($h, 1, $this);
					$a[] = tohex(fread($h, 7));
					if ($v >= 120 && $v < 130){
						$a[] = tohex(fread($h, 10));
					}
				}
				
				if ($v >= 110 && $v < 130){
					$a[] = read_string($h, 1, $this);
				}
				// var_dump($a);
			}
			
			$this->after[] = $a;
		}
		else if ($type === 'RadialList'){
			$has['rlist'] = true;
			// float (angle), float?, float (angle)
			// float (some value: 100), bool?, 2 bytes
			$a = array();
			$a[] = my_unpack_one($this, 'f', fread($h, 4));
			$a[] = my_unpack_one($this, 'f', fread($h, 4));
			$a[] = my_unpack_one($this, 'f', fread($h, 4));
			$a[] = my_unpack_one($this, 'f', fread($h, 4));
			$a[] = tohex(fread($h, 3));
			$this->after[] = $a;
		}
		else if ($type === 'Table'){
			// i've encounteres table somewhere in frontend ui
			$has['table'] = true;
			$a = array();
			// #attention need to be checked what comes first: rows or columns
			$a[] = 'num_rows = '. ($num_rows = my_unpack_one($this, 'l', fread($h, 4)));
			$b = array();
			for ($i = 0; $i < $num_rows; ++$i){
				$b_val = array();
				$b_val[] = 'num_columns = '. ($num_columns = my_unpack_one($this, 'l', fread($h, 4)));
				$c = array();
				for ($j = 0; $j < $num_rows; ++$j){
					// float, float
					$c_val = array();
					$c_val[] = my_unpack_one($this, 'f1', fread($h, 4));
					$c_val[] = my_unpack_one($this, 'f1', fread($h, 4));
					$c_val[] = tohex(fread($h, 12));
					$c[] = $c_val;
				}
				$b_val[] = $c;
				$b[] = $b_val;
			}
			$a[] = $b;
			$a[] = tohex(fread($h, 2));
			$this->after[] = $a;
		}
		else{
			$has_type = false;
		}
		
		if ($has_type && $v >= 100 && $v < 110){
			return;
		}
		
		$this->after[] = read_string($h, 1, $this);
		$this->after[] = $bit = tohex(fread($h, 1));
		if ($bit === '01'){
			$this->after[] = 'num_sth = '. ($num_sth = my_unpack_one($this, 'l', fread($h, 4)));
			$a = array();
			for ($i = 0; $i < $num_sth; ++$i){
				$a[] = tohex(fread($h, 4));
			}
			$this->after[] = $a;
		}
		
		if ($v === 97 && !$has_type){
			$this->after[] = $bit = tohex(fread($h, 1));
			if ($bit === '01'){
				$this->after[] = $a = my_unpack_array($this, 'l2', fread($h, 4 * 2));
				$num_sth = $a[1];
				$this->after[] = tohex(fread($h, $num_sth * 4));
			}
			$this->after[] = tohex(fread($h, 4));
		}
		$this->after[] = $bit = tohex(fread($h, 1));
		if ($bit === '01'){
			$this->after[] = $str = read_string($h, 1, $this);
			// yeah, i have no idea what this shit all about, but it seems to point to 3d models.
			// don't remember where i've seen it, probably in portholes
			$a = array();
			// 2 unknown, 4 unknown
			// float?
			// 8 unknown
			// float, float, float?
			// {FF FF FF | DB F2 FA}
			// float, float?, float?
			// byte (bool?)
			// float, float, float?, float, float (angle), float (angle), float
			$a[] = tohex(fread($h, 74));
			$a[] = 'num_models = '. ($num_models = my_unpack_one($this, 'l', fread($h, 4)));
			$models = array();
			for ($i = 0; $i < $num_models; ++$i){
				$b = array();
				$b[] = read_string($h, 1, $this);
				$b[] = read_string($h, 1, $this);
				$b[] = tohex(fread($h, 1));
				$b[] = 'num_anim = '. ($num_anim = my_unpack_one($this, 'l', fread($h, 4)));
				$anim = array();
				for ($j = 0; $j < $num_anim; ++$j){
					$c = array();
					$c[] = read_string($h, 1, $this);
					$c[] = read_string($h, 1, $this);
					$c[] = tohex(fread($h, 4));
					$anim[] = $c;
				}
				$b[] = $anim;
				$models[] = $b;
			}
			$a[] = $models;
			$this->after[] = $a;
			$this->after[] = tohex(fread($h, 3));
		}
		else if ($v >= 90 && $v < 95){
			$this->after[] = tohex(fread($h, 2));
		}
		else{
			// bool, ?, bool
			$this->after[] = tohex(fread($h, 3));
		}
		
		
		if ($v >= 110 && $v < 130){
			// float, float, float
			$this->after[] = tohex(fread($h, 4));
			$this->after[] = tohex(fread($h, 4));
			$this->after[] = tohex(fread($h, 4));
		}
	}
	public function debug(){
		$a = array();
		if ($this->parent === null){
			$a['version'] = $this->version;
		}
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'version', 'parent',
				// 'b0',
				// 'num_events', 'events', 'offset', 'b_01', 'tooltip_text', 'tooltip_id',
				// 'b2', 'b3', 'default_state', 'b5', 'b6',
				// 'num_images', 'images',
				// 'num_states', 'states',
				// 'num_dynamic', 'dynamic',
				// 'num_funcs', 'funcs',
				// 'after'
			))){ continue; }
			if (in_array($k, array('images')) && empty($v)){ continue; }
			if (in_array($k, array('images', 'states', 'dynamic', 'funcs', 'child'))){
				$b = array();
				foreach ($v as $child){
					$b[] = $child->debug();
				}
				$a[ $k ] = $b;
				continue;
			}
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function root(){
		$p = $this;
		while ($p->parent !== null){
			$p = $p->parent;
		}
		return $p;
	}
	public function find($name){
		foreach ($this->child as $ch){
			if ($ch->name === $name){ return $ch; }
			else if ($ch instanceof UIC){
				$res = $ch->find($name);
				if ($res){ return $res; }
			}
		}
		return null;
	}
	public function hasState($name){
		foreach ($this->states as $state){
			if ($state->name === $name){ return true; }
		}
		return false;
	}
	public function getPath(){
		$arr = array();
		$p = $this;
		while ($p){
			$arr[] = $p->name;
			$p = $p->parent;
		}
		return implode(' < ', $arr);
	}
	public function printTree($my = null, $t = ''){
		$res = $t . ($my === $this ? '*' : '') . $this->name . ' ('. sizeof($this->child) .'/'. $this->num_child .')';
		foreach ($this->child as $child){
			$res .= "\n". $child->printTree($my, $t . "\t");
		}
		return $res;
	}
	public function checkTemplateStatesEq($states){
		foreach ($states as $a){
			foreach ($this->states as $state){
				if ($state->name === $a){
					continue 2;
				}
			}
			return false;
		}
		return true;
	}
	public function dumpJS(){
		$a = array(
			$this->uid,
			$this->b_sth,
			$this->name,
			$this->b0,
			$this->events,
			$this->offset,
			$this->b1,
			$this->b_01,
			$this->tooltip_text,
			$this->tooltip_id,
			$this->docking,
			$this->dock_offset,
			$this->b3,
			$this->default_state,
		);
		
		$arr = array();
		foreach ($this->images as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$a[] = $this->maskimage;
		
		$a[] = $this->b5;
		$a[] = $this->b_sth2;
		
		$arr = array();
		foreach ($this->states as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$a[] = $this->b_sth3;
		
		$arr = array();
		foreach ($this->dynamic as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$a[] = $this->b6;
		
		$arr = array();
		foreach ($this->funcs as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$arr = array();
		foreach ($this->child as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$a[] = $this->after;
		return $a;
	}
	public function dumpFile(){
		$v = $this->version;
		
		$res = '';
		
		if ($this->parent === null){
			$res .= 'Version'. ($v < 10 ? '0' : '') . ($v < 100 ? '0' : ''). $v;
		}
		else if ($this->parent instanceof UIC){
			if ($v >= 100){
				$res .= pack('C2', 0x00, 0x00);
			}
		}
		
		$res .= fromhex($this->uid);
		if ($v >= 126 && $v < 130){
			$res .= fromhex($this->b_sth);
		}
		$res .= write_string($this->name);
		$res .= write_string($this->b0);
		
		if ($v >= 100 && $v < 110){
			$res .= write_string($this->events);
		}
		else if ($v >= 110 && $v < 130){
			if ($v === 113){
				
			} else{
				$res .= pack('l', sizeof($this->events));
			}
			foreach ($this->events as $a){
				$res .= write_string(array_slice($a, 0, 3));
				if ($v >= 124 && $v < 130){
					$res .= pack('l', sizeof($a[3]));
					foreach ($a[3] as $b){
						$res .= write_string(array_slice($b, 0, 2));
					}
				}
			}
		}
		
		$res .= pack('l2',	$this->offset['left'],
							$this->offset['top']);
		
		if ($v >= 70 && $v < 90){ $res .= fromhex($this->b1); }
		$res .= fromhex($this->b_01);
		
		// Text Description?
		$res .= write_utf8_string($this->tooltip_text);
		$res .= write_utf8_string($this->tooltip_id);
		
		$res .= pack('l3',	$this->docking,
							$this->dock_offset['left'],
							$this->dock_offset['top']);
		
		$res .= fromhex($this->b3);
		$res .= fromhex($this->default_state);
		
		$res .= pack('l', sizeof($this->images));
		foreach ($this->images as $image){
			$res .= $image->dumpFile();
		}
		
		$res .= fromhex($this->maskimage);
		if ($v >= 70 && $v < 110){
			$res .= fromhex($this->b5);
		}
		
		if ($v >= 126 && $v < 130){
			$res .= fromhex($this->b_sth2);
		}
		$res .= pack('l', sizeof($this->states));
		foreach ($this->states as $state){
			$res .= $state->dumpFile();
		}
		
		if ($v >= 126 && $v < 130){
			$res .= fromhex($this->b_sth3);
		}
		$res .= pack('l', sizeof($this->dynamic));
		foreach ($this->dynamic as $dynamic){
			$res .= $dynamic->dumpFile();
		}
		
		$res .= fromhex($this->b6);
		
		$res .= pack('l', sizeof($this->funcs));
		foreach ($this->funcs as $funcs){
			$res .= $funcs->dumpFile();
		}
		
		$res .= pack('l', sizeof($this->child));
		foreach ($this->child as $child){
			$res .= $child->dumpFile();
		}
		
		$res .= $this->dumpFileAfter();
		
		return $res;
	}
	public function dumpFileAfter(){
		$v = $this->version;
		
		$res = '';
		$i = 0;
		
		$res .= fromhex($this->after[ $i++ ]);
		$res .= write_string($type = $this->after[ $i++ ]);
		
		if ($v >= 70 && $v < 80){
			if ($type === 'List'){
				$a = $this->after[ $i++ ];
				
				$res .= pack('l', sizeof($a[1]));
				foreach ($a[1] as $b){
					$res .= fromhex($b);
				}
				
				$res .= fromhex($a[2]);
			} else{
				if ($v === 79){
					$res .= fromhex($this->after[ $i++ ]);
					if ($this->num_child !== 0){
						$res .= fromhex($this->after[ $i++ ]);
					}
				} else{
					$res .= fromhex($this->after[ $i++ ]);
				}
				if ($type){
					$res .= fromhex($this->after[ $i++ ]);
				}
			}
			return $res;
		}
		else if ($v >= 80 && $v < 90){
			$res .= fromhex($this->after[ $i++ ]);
			return $res;
		}
		
		$has_type = true;
		if ($type === 'List'){
			$a = $this->after[ $i++ ];
			
			$res .= pack('l', sizeof($a[1]));
			foreach ($a[1] as $b){
				$res .= pack('f', $b);
			}
			
			$j = 2;
			
			$res .= pack('l2',	$a[ $j++ ],
								$a[ $j++ ]);
			$res .= fromhex($a[ $j++ ]);
			$res .= pack('l',	$a[ $j++ ]);
			
			if ($v >= 91 && $v < 97){
				$res .= fromhex($a[ $j++ ]);
				if ($v === 96){
					$res .= fromhex($a[ $j++ ]);
				}
				$res .= fromhex($a[ $j++ ]);
			}
			else if ($v >= 97 && $v < 100){
				$res .= fromhex($a[ $j++ ]);
				$res .= write_string($a[ $j++ ]);
				$res .= fromhex($a[ $j++ ]);
			}
			else{
				$res .= fromhex($a[ $j++ ]);
				$res .= pack('l4',	$a[ $j++ ],
									$a[ $j++ ],
									$a[ $j++ ],
									$a[ $j++ ]);
				$res .= fromhex($a[ $j++ ]);
				
				if ($v === 106 || $v >= 110 && $v < 130){
					$res .= write_string($a[ $j++ ]);
				}
				if ($v === 106){
					$res .= fromhex($a[ $j++ ]);
				}
			}
		}
		else if ($type === 'HorizontalList'){
			$a = $this->after[ $i++ ];
			
			$res .= pack('l', sizeof($a[1]));
			foreach ($a[1] as $b){
				$res .= pack('f1', $b);
			}
			
			$j = 2;
			
			$res .= pack('l', $a[ $j++ ]);
			$res .= fromhex($a[ $j++ ]);
			$res .= pack('l', $a[ $j++ ]);
			
			if ($v >= 91 && $v < 97){
				$res .= fromhex($a[ $j++ ]);
				if ($v === 96){
					$res .= fromhex($a[ $j++ ]);
				}
				$res .= fromhex($a[ $j++ ]);
			}
			else if ($v >= 97 && $v < 100){
				$res .= fromhex($a[ $j++ ]);
				$res .= write_string($a[ $j++ ]);
				$res .= fromhex($a[ $j++ ]);
			}
			else{
				$res .= pack('S1', $a[ $j++ ]);
				$res .= pack('l', $a[ $j++ ]);
				$res .= pack('l', $a[ $j++ ]);
				$res .= pack('l', $a[ $j++ ]);
				$res .= pack('l', $a[ $j++ ]);
				
				$res .= pack('l', $a[ $j++ ]);
				$res .= pack('S1', $a[ $j++ ]);
				
				if ($v === 105){
					$res .= pack('l', $a[ $j++ ]);
					$res .= fromhex($a[ $j++ ]);
				}
				else if ($v >= 106){
					$res .= pack('l', $a[ $j++ ]);
					$res .= write_string($a[ $j++ ]);
					$res .= fromhex($a[ $j++ ]);
					if ($v >= 120 && $v < 130){
						$res .= fromhex($a[ $j++ ]);
					}
				}
				
				if ($v >= 110 && $v < 130){
					$res .= write_string($a[ $j++ ]);
				}
			}
		}
		else if ($type === 'RadialList'){
			$a = $this->after[ $i++ ];
			$j = 0;
			$res .= pack('f4',	$a[ $j++ ],
								$a[ $j++ ],
								$a[ $j++ ],
								$a[ $j++ ]);
			$res .= fromhex($a[ $j++ ]);
		}
		else if ($type === 'Table'){
			// #todo
			$a = $this->after[ $i++ ];
			$res .= pack('l', sizeof($a[1]));
			foreach ($a[1] as $b){
				$res .= pack('l', sizeof($b[1]));
				foreach ($b[1] as $c){
					$res .= pack('f2', $c[0], $c[1]);
					$res .= fromhex($c[2]);
				}
			}
			$res .= fromhex($a[2]);
		}
		else{
			$has_type = false;
		}
		
		if ($has_type && $v >= 100 && $v < 110){
			return $res;
		}
		
		$res .= write_string($this->after[ $i++ ]);
		$res .= fromhex($bit = $this->after[ $i++ ]);
		
		if ($bit === '01'){
			$i++; // num_sth
			$res .= pack('l', sizeof($this->after[ $i ]));
			foreach ($this->after[ $i ] as $a){
				$res .= fromhex($a);
			}
			$i++; // sth
		}
		
		if ($v === 97 && !$has_type){
			$res .= fromhex($bit = $this->after[ $i++ ]);
			if ($bit === '01'){
				$a = $this->after[ $i++ ];
				$res .= pack('l2', $a[0], $a[1]);
				$res .= fromhex($this->after[ $i++ ]);
			}
			$res .= fromhex($this->after[ $i++ ]);
		}
		$res .= fromhex($bit = $this->after[ $i++ ]);
		if ($bit === '01'){
			$res .= write_string($str = $this->after[ $i++ ]);
			
			$a = $this->after[ $i++ ];
			$res .= fromhex($a[0]);
			$res .= pack('l', sizeof($a[2]));
			// models
			foreach ($a[2] as $b){
				$res .= write_string($b[0]);
				$res .= write_string($b[1]);
				$res .= fromhex($b[2]);
				$res .= pack('l', sizeof($b[4]));
				// anim
				foreach ($b[4] as $c){
					$res .= write_string($c[0]);
					$res .= write_string($c[1]);
					$res .= fromhex($c[2]);
				}
			}
			
			$res .= fromhex($this->after[ $i++ ]);
		}
		else if ($v >= 90 && $v < 95){
			$res .= fromhex($this->after[ $i++ ]);
		}
		else{
			$res .= fromhex($this->after[ $i++ ]);
		}
		
		if ($v >= 110 && $v < 130){
			$res .= fromhex($this->after[ $i++ ]);
			$res .= fromhex($this->after[ $i++ ]);
			$res .= fromhex($this->after[ $i++ ]);
		}
		
		return $res;
	}
	public function setVersion($v){
		$this->version = $v;
		foreach ($this->child as $child){
			$child->setVersion($v);
		}
	}
	public function Resize($width, $height = null){
		// we assume that every state has the same bounds
		if ($height === null){
			$factor = $width;
			$this->offset['left'] = round($this->offset['left'] * $factor[0]);
			$this->offset['top'] = round($this->offset['top'] * $factor[1]);
			$this->dock_offset['left'] = round($this->dock_offset['left'] * $factor[0]);
			$this->dock_offset['top'] = round($this->dock_offset['top'] * $factor[1]);
		} else{
			$bounds = $this->states[0]->bounds;
			$factor = array(
				$width / $bounds[0],
				$height / $bounds[1],
				sqrt($width * $height) / sqrt($bounds[0] * $bounds[1])
			);
		}
		foreach ($this->states as $state){
			$state->bounds[0] = round($state->bounds[0] * $factor[0]);
			$state->bounds[1] = round($state->bounds[1] * $factor[1]);
			$state->font_m_size = round($state->font_m_size * $factor[2]);
			foreach ($state->bgs as $bg){
				$bg->offset['left'] = round($bg->offset['left'] * $factor[0]);
				$bg->offset['top'] = round($bg->offset['top'] * $factor[1]);
				$bg->bounds[0] = round($bg->bounds[0] * $factor[0]);
				$bg->bounds[1] = round($bg->bounds[1] * $factor[1]);
			}
		}
		foreach ($this->child as $ch){
			$ch->Resize($factor);
		}
	}
	public function eachChild($func){
		$func($this);
		foreach ($this->child as $child){
			$child->eachChild($func);
		}
	}
}

class UIC__Image {
	public $my;
	public $uid;
	public $b_sth = null;
	public $path;
	public $width;
	public $height;
	public $extra; // componentvisible, canuse1bitalpha
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		foreach ($a as $k => $v){
			if (!property_exists('UIC__Image', $k)){ continue; }
			$this->$k = $v;
		}
		if ($parent !== null){ $this->my = $parent; }
	}
	public function read($h, $my){
		$this->my = $my;
		$v = $my->version;
		
		$this->uid = tohex(fread($h, 4));
		if ($v >= 126 && $v < 130){
			$this->b_sth = tohex(fread($h, 16));
		}
		$this->path = read_string($h, 1, $this);
		$this->width = my_unpack_one($this, 'l', fread($h, 4));
		$this->height = my_unpack_one($this, 'l', fread($h, 4));
		if ($v >= 79){
			$this->extra = tohex(fread($h, 1));
		}
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'my'
			))){ continue; }
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function dumpJS(){
		return array(
			$this->uid,
			$this->b_sth,
			$this->path,
			$this->width,
			$this->height,
			$this->extra
		);
	}
	public function dumpFile(){
		$v = $this->my->version;
		$res = '';
		
		$res .= fromhex($this->uid);
		if ($v >= 126 && $v < 130){
			$res .= fromhex($this->b_sth);
		}
		$res .= write_string($this->path);
		$res .= pack('l2', $this->width, $this->height);
		if ($v >= 79){
			$res .= fromhex($this->extra);
		}
		
		return $res;
	}
}

class UIC__State {
	#region vars
	public $my;
	public $path;
	
	public $uid;
	public $b_sth = null;
	public $name;
	public $bounds;
	public $text;
	public $tooltip;
	public $textbounds = array('width' => null, 'height' => null);
	public $textalign = array('horizontal' => null, 'vertical' => null);
	public $b1;
	public $textlabel;
	public $b3;
	public $localized;
	public $b4;
	public $tooltip_id;
	public $b5;
	
	public $font_m_font_name;
	public $font_m_size;
	public $font_m_leading;
	public $font_m_tracking;
	public $font_m_colour;
	public $fontcat_name;
	public $textoffset = array();
	public $b7;
	public $shader_name;
	public $shadervars = array();
	public $text_shader_name;
	public $textshadervars = array();
	
	public $num_bgs;
	public $bgs = array();
	
	public $b_mouse;
	public $num_mouse;
	public $mouse = array();
	public $b8 = null;
	#endregion
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		foreach ($a as $k => $v){
			if (!property_exists('UIC__State', $k)){ continue; }
			if ($k === 'bgs'){
				foreach ($v as $bg){
					$this->bgs[] = $b = new UIC__State_Background($bg, $this);
				}
			} else if ($k === 'mouse'){
				foreach ($v as $mouse){
					$this->mouse[] = $b = new UIC__State_Mouse($mouse, $this);
				}
			} else{
				$this->$k = $v;
			}
		}
		if ($parent !== null){ $this->my = $parent; }
	}
	public function read($h, $my){
		global $path;
		
		$this->my = $my;
		$v = $my->version;
		$this->path = $my->getPath();
		
		$this->uid = tohex(fread($h, 4)); // uid
		if ($v >= 126 && $v < 130){
			$this->b_sth = tohex(fread($h, 16));
		}
		$this->name = read_string($h, 1, $my); // NewState
		
		$this->bounds = my_unpack_array($my, 'l2', fread($h, 8));
		
		$this->text = read_utf8_string($h, 1, $my);
		$this->tooltip = read_utf8_string($h, 1, $my);
		
		list($this->textbounds['width'],
			$this->textbounds['height'],
			// 0 - left, 1 - center, 2 - right
			$this->textalign['horizontal'],
			// 1 - top, 2 - middle, 3 - bottom
			$this->textalign['vertical']) = my_unpack_array($my, 'l4', fread($h, 4 * 4));
		// texthbehaviour
		$this->b1 = tohex(fread($h, 1));
		
		// textlabel
		$this->textlabel = read_utf8_string($h, 1, $my);
		
		// textlocalised
		if ($v <= 115){
			$this->b3 = tohex(fread($h, 2));
			$this->localized = read_utf8_string($h, 1, $my);
		} else{
			$this->localized = read_utf8_string($h, 1, $my);
			$this->b3 = tohex(fread($h, 2));
		}
		
		// imagedock9patch, blockedanims
		if ($v >= 70 && $v < 90){
			$this->tooltip_id = read_utf8_string($h, 1, $my); // ?
		} else if ($v >= 90 && $v < 110){
			// tooltiplabel
			$this->tooltip_id = read_utf8_string($h, 1, $my); // ?
			$this->b5 = read_string($h, 1, $my); // ?
		} else if ($v >= 110 && $v < 120){
			if ($v >= 110 && $v <= 115){
				$this->b4 = tohex(fread($h, 4));
			}
		}
		// else if ($v >= 120 && $v < 130){
		else if ($v === 129){
			$this->b5 = read_string($h, 1, $my);
		}
		
		$this->font_m_font_name = read_string($h, 1, $my); // georgia_italic, Norse-Bold
		
		list($this->font_m_size,
			$this->font_m_leading,
			$this->font_m_tracking) = my_unpack_array($my, 'l3', fread($h, 4 * 3));
		$this->font_m_colour = tohex(fread($h, 4));
		
		$this->fontcat_name = read_string($h, 1, $my); // Default Font category
		
		if ($v >= 70 && $v < 80){
			// left-right, top-bottom?
			$this->textoffset = my_unpack_array($my, 'l2', fread($h, 4 * 2));
		} else if ($v >= 80 && $v < 130){
			// left, right, top, bottom
			$this->textoffset = my_unpack_array($my, 'l4', fread($h, 4 * 4));
		}
		
		// focustype
		// interactive, disabled, pixelcollision
		if ($v >= 70 && $v < 80){
			$this->b7 = tohex(fread($h, 4 + 3));
		}
		else if ($v >= 90 && $v < 130){
			// (confirmed) 2nd byte doesn't allow you to trigger ComponentMouseOn event
			$this->b7 = tohex(fread($h, 4));
		}
		
		$this->shader_name = read_string($h, 1, $my); // normal_t0
		/*
		// normal_t0:
		// float, float, float, ? - color in [0; 1] range (i guess)
		
		// brighten_t0, set_greyscale_t0:
		// float, - how much to brighten or darken [-1; 1] grayscale [0; 1]
		// float - opacity [0; 1]
		// ?, ?
		
		// glow_pulse_t0:
		// float, float, float, ? - some sin arguments?
		
		// red_pulse_t0:
		// float, float, float, ? - (A + B) * sin(w * t); A, B, w
		
		// text_outline_t0:
		// ?, ?, ?, float - line_width?
		
		// border_alpha_blend:
		// float, float, float, float - some intensity? values - 10, 15, 20
		
		// smoke_overlay_t0:
		// float, float, ?, ? - amplifaction? smoke white color amplification?
		
		// pie_chart_t0:
		// float, float, float, float - ?
		
		// drop_shadow_t0:
		// ?, float, ?, ? - yoffset?
		
		// grey_over_time_t0:
		// float, ?, ?, ? - time?
		
		// distortion:
		// (this butch is scewing image from center to to corners.
		// and it moves in anti-clockwise direction)
		// float - how much to distort [0; 1] there is distortion even in "0" value
		// float - time, i guess negative value would make clockwise
		// ?, ?
		
		// winds_of_magic_t0:
		// float, float, ?, ? - ?
		*/
		$this->shadervars = my_unpack_array($my, 'f4', fread($h, 4 * 4));
		foreach ($this->shadervars as &$a){ $a = round($a * 10000000) / 10000000; }
		unset($a);
		
		$this->text_shader_name = read_string($h, 1, $my); // normal_t0
		$this->textshadervars = my_unpack_array($my, 'f4', fread($h, 4 * 4));
		foreach ($this->textshadervars as &$a){ $a = round($a * 10000000) / 10000000; }
		unset($a);
		
		// imagemetrics
		$this->num_bgs = my_unpack_one($my, 'l', fread($h, 4));
		my_assert($this->num_bgs < 40, $my);
		global $has;
		if ($this->num_bgs > 0){ $has['bgs'] = true; }
		for ($i = 0; $i < $this->num_bgs; ++$i){
			$this->bgs[] = $a = new UIC__State_Background();
			$a->read($h, $my, $this);
		}
		
		// stateeditordisplaypos (2 ints)
		$this->b_mouse = tohex(fread($h, 8)); // unknown
		
		// mouse states?
		$this->num_mouse = my_unpack_one($my, 'l', fread($h, 4));
		my_assert($this->num_mouse < 20, $my);
		for ($i = 0; $i < $this->num_mouse; ++$i){
			$this->mouse[] = $a = new UIC__State_Mouse();
			$a->read($h, $my, $this);
		}
		
		if ($v >= 120 && $v < 130){
			$a = read_string($h, 1, $my);
			if (empty($a)){
				$this->b8 = array($a);
			} else{
				$a = array($a);
				
				$num_sth = my_unpack_one($this, 'l', fread($h, 4));
				$sth = array();
				for ($i = 0; $i < $num_sth; ++$i){
					$b = array();
					$b[] = read_string($h, 1, $my);
					$b[] = tohex(fread($h, 16));
					$sth[] = $b;
				}
				$a[] = $sth;
				
				$num_sth = my_unpack_one($this, 'l', fread($h, 4));
				$sth = array();
				for ($i = 0; $i < $num_sth; ++$i){
					$b = array();
					$b[] = read_string($h, 1, $my);
					$b[] = read_string($h, 1, $my);
					$sth[] = $b;
				}
				$a[] = $sth;
				
				$this->b8 = $a;
			}
		}
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'my'
			))){ continue; }
			if ($k === 'bgs' && empty($v)){ continue; }
			if (in_array($k, array('bgs', 'mouse'))){
				$b = array();
				foreach ($v as $child){
					$b[] = $child->debug();
				}
				$a[ $k ] = $b;
				continue;
			}
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function dumpJS(){
		$a = array(
			$this->uid,
			$this->b_sth,
			$this->name,
			$this->bounds,
			$this->text,
			$this->tooltip,
			$this->textbounds,
			$this->textalign,
			$this->b1,
			$this->textlabel,
			$this->b3,
			$this->localized,
			$this->b4,
			$this->tooltip_id,
			$this->b5,
			$this->font_m_font_name,
			$this->font_m_size,
			$this->font_m_leading,
			$this->font_m_tracking,
			$this->font_m_colour,
			$this->fontcat_name,
			$this->textoffset,
			$this->b7,
			$this->shader_name,
			$this->shadervars,
			$this->text_shader_name,
			$this->textshadervars
		);
		
		$arr = array();
		foreach ($this->bgs as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$a[] = $this->b_mouse;
		
		$arr = array();
		foreach ($this->mouse as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$a[] = $this->b8;
		
		return $a;
	}
	public function dumpFile(){
		$v = $this->my->version;
		$res = '';
		
		$res .= fromhex($this->uid);
		if ($v >= 126 && $v < 130){
			$res .= fromhex($this->b_sth);
		}
		$res .= write_string($this->name);
		
		$res .= pack('l2', $this->bounds[0], $this->bounds[1]);
		
		$res .= write_utf8_string($this->text);
		$res .= write_utf8_string($this->tooltip);
		
		$res .= pack('l4',	$this->textbounds['width'],
							$this->textbounds['height'],
							$this->textalign['horizontal'],
							$this->textalign['vertical']);
		
		$res .= fromhex($this->b1);
		
		$res .= write_utf8_string($this->textlabel);
		
		if ($v <= 115){
			$res .= fromhex($this->b3);
			$res .= write_utf8_string($this->localized);
		}
		else{
			$res .= write_utf8_string($this->localized);
			$res .= fromhex($this->b3);
		}
		
		
		if ($v >= 70 && $v < 90){
			$res .= write_utf8_string($this->tooltip_id);
		}
		else if ($v >= 90 && $v < 110){
			$res .= write_utf8_string($this->tooltip_id);
			// $res .= fromhex($this->b5);
			$res .= write_string($this->b5);
		}
		else if ($v >= 110 && $v < 120){
			if ($v >= 110 && $v <= 115){
				$res .= fromhex($this->b4);
			}
		}
		else if ($v === 129){
			$res .= write_string($this->b5);
		}
		
		$res .= write_string($this->font_m_font_name);
		$res .= pack('l3',	$this->font_m_size,
							$this->font_m_leading,
							$this->font_m_tracking);
		$res .= fromhex($this->font_m_colour);
		
		$res .= write_string($this->fontcat_name);
		
		if ($v >= 70 && $v < 80){
			$res .= pack('l2',	$this->textoffset[0],
								$this->textoffset[1]);
		}
		else if ($v >= 80 && $v < 130){
			$res .= pack('l4',	$this->textoffset[0],
								$this->textoffset[1],
								$this->textoffset[2],
								$this->textoffset[3]);
		}
		
		if ($v >= 70 && $v < 80){
			$res .= fromhex($this->b7);
		}
		else if ($v >= 90 && $v < 130){
			$res .= fromhex($this->b7);
		}
		
		$res .= write_string($this->shader_name);
		$res .= pack('f4',	$this->shadervars[0],
							$this->shadervars[1],
							$this->shadervars[2],
							$this->shadervars[3]);
		
		$res .= write_string($this->text_shader_name);
		$res .= pack('f4',	$this->textshadervars[0],
							$this->textshadervars[1],
							$this->textshadervars[2],
							$this->textshadervars[3]);
		
		$res .= pack('l', sizeof($this->bgs));
		foreach ($this->bgs as $bg){
			$res .= $bg->dumpFile();
		}
		
		$res .= fromhex($this->b_mouse);
		
		$res .= pack('l', sizeof($this->mouse));
		foreach ($this->mouse as $mouse){
			$res .= $mouse->dumpFile();
		}
		
		if ($v >= 120 && $v < 130){
			$a = $this->b8;
			$res .= write_string($a[0]);
			
			if (isset($a[1])){
				$res .= pack('l', sizeof($a[1]));
				foreach ($a[1] as $b){
					$res .= write_string($b[0]);
					$res .= fromhex($b[1]);
				}
				
				$res .= pack('l', sizeof($a[2]));
				foreach ($a[2] as $b){
					$res .= write_string($b[0]);
					$res .= write_string($b[1]);
				}
			}
		}
		
		return $res;
	}
}

class UIC__State_Background {
	#region vars
	public $state;
	
	public $uid;
	public $b_sth = null;
	public $offset;
	public $bounds;
	public $colour;
	public $str_sth;
	public $tile;
	public $x_flipped;
	public $y_flipped;
	public $dockpoint;
	public $dock_offset = array( 'left' => null, 'top' => null );
	public $dock = array( 'right' => null, 'bottom' => null );
	public $rotation_angle;
	public $pivot_point = array( 'x' => null, 'y' => null );
	public $shader_name;
	public $rotation_axis;
	public $b4;
	public $shadertechnique_vars;
	public $margin;
	public $b5 = null;
	#endregion
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		if ($a instanceof UIC__State){
			$this->state = $a;
			return;
		}
		foreach ($a as $k => $v){
			if (!property_exists('UIC__State_Background', $k)){ continue; }
			$this->$k = $v;
		}
		if ($parent !== null){ $this->state = $parent; }
	}
	public function read($h, $my, $state){
		$this->state = $state;
		$v = $my->version;
		
		// componentimage
		$this->uid = tohex(fread($h, 4));
		if ($v >= 126 && $v < 130){
			$this->b_sth = tohex(fread($h, 16));
		}
		list($this->offset['left'],
			$this->offset['top']) = my_unpack_array($my, 'l2', fread($h, 4 * 2));
		$this->bounds = my_unpack_array($my, 'l2', fread($h, 8));
		
		$this->colour = tohex(fread($h, 4));
		
		if ($v >= 119 && $v < 130){
			// ui_colour_preset_type_key
			$this->str_sth = read_string($h, 1, $my); // alliance_player
		}
		
		// using margin property to split image and repeat centered part
		$this->tile = my_unpack_one($my, 'C1', fread($h, 1));
		// dockpoint
		// int, this seems to indicate position as
		// 0 = ? (span?), 1 = top left, 2 = top, 3 = top right,
		// 4 = center left, 5 = center, 6 = center right,
		// 7 = bottom left, 8 = bottom, 9 = bottom right
        // |1|2|3|
        // |4|5|6|
        // |7|8|9|
		list($this->x_flipped,
			$this->y_flipped,
			$this->dockpoint) = my_unpack_array($my, 'C2a/l1b', fread($h, 2 + 4));
		
		list($this->dock_offset['left'],
			$this->dock_offset['top']) = my_unpack_array($my, 'l2', fread($h, 4 * 2));
		
		// canresizeheight, canresizewidth
		list($this->dock['right'],
			$this->dock['bottom']) = my_unpack_array($my, 'C2', fread($h, 2));
		
		list($this->rotation_angle,
			$this->pivot_point['x'],
			$this->pivot_point['y']) = my_unpack_array($my, 'f3', fread($h, 4 * 3));
		
		if ($v >= 103){
			$this->rotation_axis = my_unpack_array($my, 'f3', fread($h, 4 * 3));
			$this->shader_name = read_string($h, 1, $my); // brighten_t0
		}
		else{
			$this->shader_name = read_string($h, 1, $my); // brighten_t0
			$this->rotation_axis = my_unpack_array($my, 'f3', fread($h, 4 * 3));
		}
		
		if ($v <= 102){
			// it seems always 00 00 00 00
			$this->b4 = tohex(fread($h, 4));
		}
		
		if ($v === 79){
			$this->b5 = tohex(fread($h, 8));
		} else if ($v >= 70 && $v < 80){
			$this->b5 = tohex(fread($h, 9));
		} else if ($v >= 80 && $v < 95){
			// top-bottom, left-right?
			if ($v === 92 || $v === 93){
				$this->margin = my_unpack_array($my, 'f4', fread($h, 4 * 4));
			} else{
				$this->margin = my_unpack_array($my, 'f2', fread($h, 4 * 2));
			}
		} else{
			if ($v >= 103){
				$this->shadertechnique_vars = my_unpack_array($my, 'f4', fread($h, 4 * 4));
				foreach ($this->shadertechnique_vars as &$a){ $a = round($a * 10000000) / 10000000; }
				unset($a);
			}
			// top, right, bottom, left
			$this->margin = my_unpack_array($my, 'f4', fread($h, 4 * 4));
			if ($v >= 125 && $v < 130){
				$this->b5 = tohex(fread($h, 1));
			}
		}
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'state'
			))){ continue; }
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function dumpJS(){
		return array(
			$this->uid,
			$this->b_sth,
			$this->offset,
			$this->bounds,
			$this->colour,
			$this->str_sth,
			$this->tile,
			$this->x_flipped,
			$this->y_flipped,
			$this->dockpoint,
			$this->dock_offset,
			$this->dock,
			$this->rotation_angle,
			$this->pivot_point,
			$this->shader_name,
			$this->rotation_axis,
			$this->b4,
			$this->shadertechnique_vars,
			$this->margin,
			$this->b5
		);
	}
	public function dumpFile(){
		$v = $this->state->my->version;
		$res = '';
		
		$res .= fromhex($this->uid);
		if ($v >= 126 && $v < 130){
			$res .= fromhex($this->b_sth);
		}
		$res .= pack('l2',	$this->offset['left'],
							$this->offset['top']);
		$res .= pack('l2',	$this->bounds[0],
							$this->bounds[1]);
		$res .= fromhex($this->colour);
		
		if ($v >= 119 && $v < 130){
			$res .= write_string($this->str_sth);
		}
		$res .= pack('C3',	$this->tile,
							$this->x_flipped,
							$this->y_flipped);
		
		$res .= pack('l',	$this->dockpoint);
		
		$res .= pack('l2',	$this->dock_offset['left'],
							$this->dock_offset['top']);
		
		$res .= pack('C2',	$this->dock['right'],
							$this->dock['bottom']);
		
		$res .= pack('f3',	$this->rotation_angle,
							$this->pivot_point['x'],
							$this->pivot_point['y']);
		
		if ($v >= 103){
			$res .= pack('f3',	$this->rotation_axis[0],
								$this->rotation_axis[1],
								$this->rotation_axis[2]);
			$res .= write_string($this->shader_name);
		}
		else{
			$res .= write_string($this->shader_name);
			$res .= pack('f3',	$this->rotation_axis[0],
								$this->rotation_axis[1],
								$this->rotation_axis[2]);
		}
		
		if ($v <= 102){
			$res .= fromhex($this->b4);
		}
		
		if ($v >= 70 && $v < 80){
			$res .= fromhex($this->b5);
		} else if ($v >= 80 && $v < 95){
			if ($v === 92 || $v === 93){
				$res .= pack('f4',	$this->margin[0],
									$this->margin[1],
									$this->margin[2],
									$this->margin[3]);
			} else{
				$res .= pack('f2',	$this->margin[0],
									$this->margin[1]);
			}
		} else{
			if ($v >= 103){
				$res .= pack('f4',	$this->shadertechnique_vars[0],
									$this->shadertechnique_vars[1],
									$this->shadertechnique_vars[2],
									$this->shadertechnique_vars[3]);
			}
			$res .= pack('f4',	$this->margin[0],
								$this->margin[1],
								$this->margin[2],
								$this->margin[3]);
			if ($v >= 125 && $v < 130){
				$res .= fromhex($this->b5);
			}
		}
		
		return $res;
	}
}

class UIC__State_Mouse {
	public $state;
	
	public $mouse_state;
	public $state_uid;
	public $b_sth = null;
	public $b0;
	public $num_sth;
	public $sth = array();
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		if ($a instanceof UIC__State){
			$this->state = $a;
			return;
		}
		foreach ($a as $k => $v){
			if (!property_exists('UIC__State_Mouse', $k)){ continue; }
			$this->$k = $v;
		}
		if ($parent !== null){ $this->state = $parent; }
	}
	public function read($h, $my, $state){
		$this->state = $state;
		$v = $my->version;
		
		// 0 - mouseenter
		// 1 - mouseout
		// 2 - mousedown
		// 3 - mouseup (click)
		// 8 - mouseup (blur?)
		$this->mouse_state = array_shift(unpack('l', fread($h, 4)));
		$this->state_uid = tohex(fread($h, 4));
		if ($v >= 120 && $v < 130){
			$this->b_sth = tohex(fread($h, 16));
		}
		// гипотеза: аудио?
		$this->b0 = tohex(fread($h, 8)); // ?, ?
		
		$this->num_sth = array_shift(unpack('l', fread($h, 4)));
		my_assert($this->num_sth < 20, $my);
		for ($i = 0; $i < $this->num_sth; ++$i){
			$a = array();
			$a[] = tohex(fread($h, 4));
			if ($v >= 120 && $v < 130){
				$a[] = tohex(fread($h, 16));
			}
			$a[] = read_string($h, 1, $my);
			$a[] = read_string($h, 1, $my);
			$a[] = read_string($h, 1, $my);
			$this->sth[] = $a;
		}
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'state'
			))){ continue; }
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function dumpJS(){
		return array(
			$this->mouse_state,
			$this->state_uid,
			$this->b_sth,
			$this->b0,
			$this->num_sth,
			$this->sth
		);
	}
	public function dumpFile(){
		$v = $this->state->my->version;
		$res = '';
		
		$res .= pack('l', $this->mouse_state);
		$res .= fromhex($this->state_uid);
		if ($v >= 120 && $v < 130){
			$res .= fromhex($this->b_sth);
		}
		$res .= fromhex($this->b0);
		
		$res .= pack('l', sizeof($this->sth));
		foreach ($this->sth as $a){
			$i = 0;
			$res .= fromhex($a[ $i++ ]);
			if ($v >= 120 && $v < 130){
				$res .= fromhex($a[ $i++ ]);
			}
			$res .= write_string($a[ $i++ ]);
			$res .= write_string($a[ $i++ ]);
			$res .= write_string($a[ $i++ ]);
		}
		
		return $res;
	}
}

class UIC__Dynamic {
	public $my;
	public $str1;
	public $str2;
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		foreach ($a as $k => $v){
			if (!property_exists('UIC__Dynamic', $k)){ continue; }
			$this->$k = $v;
		}
		if ($parent !== null){ $this->my = $parent; }
	}
	public function read($h, $my){
		$this->my = $my;
		$this->str1 = read_string($h, 1, $my);
		$this->str2 = read_string($h, 1, $my, false);
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'my'
			))){ continue; }
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function dumpJS(){
		return array(
			$this->str1,
			$this->str2
		);
	}
	public function dumpFile(){
		// $v = $this->my->version;
		$res = '';
		
		$res .= write_string($this->str1);
		$res .= write_string($this->str2);
		
		return $res;
	}
}

class UIC__Func {
	public $my;
	public $name;
	public $b0;
	public $num_anim;
	public $anim;
	public $str_sth;
	public $b1;
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		foreach ($a as $k => $v){
			if (!property_exists('UIC__Func', $k)){ continue; }
			if ($k === 'anim'){
				foreach ($v as $anim){
					$this->anim[] = $b = new UIC__Func_Anim($anim, $this);
				}
			} else{
				$this->$k = $v;
			}
		}
		if ($parent !== null){ $this->my = $parent; }
	}
	public function read($h, $my){
		$this->my = $my;
		$v = $my->version;
		
		$this->name = read_string($h, 1, $my);
		// propagate, makenoninteractive, totalloops
		$this->b0 = tohex(fread($h, 2)); // ?
		
		$this->num_anim = my_unpack_one($my, 'l', fread($h, 4));
		
		my_assert($this->num_anim < 40, $my);
		for ($j = 0; $j < $this->num_anim; ++$j){
			$this->anim[] = $a = new UIC__Func_Anim();
			$a->read($h, $my, $this);
		}
		
		if ($v >= 91 && $v <= 93){
			$this->b1 = tohex(fread($h, 2));
		}
		else if ($v >= 95 && $v < 97){
			$this->b1 = tohex(fread($h, 2));
		}
		else if ($v >= 97 && $v < 100){
			$this->str_sth = read_string($h, 1, $my);
		}
		else if ($v >= 110 && $v < 130){
			$this->str_sth = read_string($h, 1, $my);
			$this->b1 = read_string($h, 1, $my);
		}
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'my'
			))){ continue; }
			if (in_array($k, array('anim'))){
				$b = array();
				foreach ($v as $child){
					$b[] = $child->debug();
				}
				$a[ $k ] = $b;
				continue;
			}
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function dumpJS(){
		$a = array(
			$this->name,
			$this->b0
		);
		
		$arr = array();
		foreach ($this->anim as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$a[] = $this->str_sth;
		$a[] = $this->b1;
		
		return $a;
	}
	public function dumpFile(){
		$v = $this->my->version;
		$res = '';
		
		$res .= write_string($this->name);
		$res .= fromhex($this->b0);
		
		$res .= pack('l', sizeof($this->anim));
		foreach ($this->anim as $anim){
			$res .= $anim->dumpFile();
		}
		
		if ($v >= 91 && $v < 97){
			$res .= fromhex($this->b1);
		}
		else if ($v >= 97 && $v < 100){
			$res .= write_string($this->str_sth);
		}
		else if ($v >= 110 && $v < 130){
			$res .= write_string($this->str_sth);
			$res .= write_string($this->b1);
		}
		
		return $res;
	}
}

class UIC__Func_Anim {
	#region vars
	public $func;
	
	public $b_hex = array();
	public $b_str = array();
	
	public $offset = array( 'left' => null, 'top' => null );
	public $bounds;
	public $colour;
	
	public $m_shadervars;
	public $m_rotation_angle;
	public $m_imageindex1;
	public $m_imageindex2;
	public $m_font_scale;
	
	public $interpolationtime;
	public $interpolationpropertymask;
	public $easing_weight;
	
	public $linear;
	public $num_attr;
	public $attr = array();
	public $b2;
	public $str_sth;
	public $b3;
	#endregion
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		foreach ($a as $k => $v){
			if (!property_exists('UIC__Func_Anim', $k)){ continue; }
			if ($k === 'attr'){
				foreach ($v as $attr){
					$this->attr[] = $b = new UIC__Func_Anim_Attr($attr, $this);
				}
			} else{
				$this->$k = $v;
			}
		}
		if ($parent !== null){ $this->func = $parent; }
	}
	public function read($h, $my, $func){
		$this->func = $func;
		global $path;
		$v = $my->version;
		
		if ($v >= 110 && $v < 130){
			// soundcategory, soundcategoryend
			// not sure if i did it right.
			$len = my_unpack_one($my, 'S1', fread($h, 2));
			fseek($h, -2, SEEK_CUR);
			if ($len === 0xFFFF){
				$this->b_hex[0] = tohex(fread($h, 2));
				$this->b_hex[1] = tohex(fread($h, 2));
			}
			else if ($v >= 120 && $v < 130){
				$this->b_hex[0] = tohex(fread($h, 2));
				$this->b_str[1] = read_string($h, 1, $my);
			}
			else{
				if ($len === 0){
					$this->b_hex[0] = tohex(fread($h, 2));
					$this->b_hex[1] = tohex(fread($h, 2));
					// $this->b_str[1] = read_string($h, 1, $my);
				}
				else{
					$this->b_str[0] = read_string($h, 1, $my);
					
					$len = my_unpack_one($my, 'S1', fread($h, 2));
					fseek($h, -2, SEEK_CUR);
					if ($len === 0){
						$this->b_hex[1] = tohex(fread($h, 2));
					}
					else{
						$this->b_str[1] = read_string($h, 1, $my);
					}
				}
				
			}
		}
		
		// targetmetrics_m_offset
		list($this->offset['left'],
			$this->offset['top']) = my_unpack_array($my, 'f2', fread($h, 4 * 2));
		
		
		// targetmetrics_m_height, targetmetrics_m_width
		$this->bounds = my_unpack_array($my, 'l2', fread($h, 4 * 2));
		// targetmetrics_m_colour
		$this->colour = tohex(fread($h, 4));
		
		$this->m_shadervars = my_unpack_array($my, 'f4', fread($h, 4 * 4));
		$this->m_rotation_angle = my_unpack_one($my, 'f', fread($h, 4));
		list($this->m_imageindex1,
			$this->m_imageindex2) = my_unpack_array($my, 'l2', fread($h, 4 * 2));
		
		if ($v >= 110 && $v < 130){
			$this->m_font_scale = my_unpack_one($my, 'f', fread($h, 4));
		}
		
		list($this->interpolationtime,
			$this->interpolationpropertymask) = my_unpack_array($my, 'l2', fread($h, 4 * 2));
		
		$this->easing_weight = my_unpack_one($my, 'f', fread($h, 4));
		
		
		// easing_curve_type
		// Linear, Quadratic Out
		$this->linear = read_string($h, 1, $my);
		
		// triggers
		$this->num_attr = my_unpack_one($my, 'l', fread($h, 4));
		my_assert($this->num_attr < 20, $my);
		for ($i = 0; $i < $this->num_attr; ++$i){
			$this->attr[] = $a = new UIC__Func_Anim_Attr();
			$a->read($h, $my, $func, $this);
		}
		
		// (confirmed) is_movement_absolute (for b2)
		// is_resize_for_image
		if ($v >= 90 && $v < 100){
			$this->b2 = tohex(fread($h, 1));
		}
		else if ($v >= 100 && $v < 110){
			$this->b2 = tohex(fread($h, 2));
			// UI_CAM_ANI_Panel_Slide_Long_Open
			// UI_CAM_ANI_Panel_Slide_Long_Close
			if ($v >= 106){ $this->str_sth = read_string($h, 1, $my); }
			if ($v >= 104){
				// b3 might be string, as game crashes with value 01 00
				// $this->b3 = tohex(fread($h, 2));
				$this->b3 = read_string($h, 1, $my);
			}
			else{ $this->b3 = tohex(fread($h, 1)); }
		}
		else if ($v >= 110 && $v < 120){
			$this->b2 = tohex(fread($h, 2));
		}
		else if ($v >= 120 && $v < 130){
			$this->b2 = tohex(fread($h, 2));
			$this->b3 = read_string($h, 1, $my);
		}
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'func'
			))){ continue; }
			if (in_array($k, array('attr'))){
				$b = array();
				foreach ($v as $child){
					$b[] = $child->debug();
				}
				$a[ $k ] = $b;
				continue;
			}
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function dumpJS(){
		$a = array(
			$this->b_hex,
			$this->b_str,
			$this->offset,
			$this->bounds,
			$this->colour,
			
			$this->m_shadervars,
			$this->m_rotation_angle,
			$this->m_imageindex1,
			$this->m_imageindex2,
			$this->m_font_scale,
			
			$this->interpolationtime,
			$this->interpolationpropertymask,
			$this->easing_weight,
			$this->linear
		);
		
		$arr = array();
		foreach ($this->attr as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$a[] = $this->b2;
		$a[] = $this->str_sth;
		$a[] = $this->b3;
		
		return $a;
	}
	public function dumpFile(){
		$v = $this->func->my->version;
		$res = '';
		
		if ($v >= 110 && $v < 130){
			if (isset($this->b_str[0])){
				$res .= write_string($this->b_str[0]);
			} else{
				$res .= fromhex($this->b_hex[0]);
			}
			if (isset($this->b_str[1])){
				$res .= write_string($this->b_str[1]);
			} else{
				$res .= fromhex($this->b_hex[1]);
			}
		}
		
		$res .= pack('f2', $this->offset['left'], $this->offset['top']);
		$res .= pack('l2', $this->bounds[0], $this->bounds[1]);
		$res .= fromhex($this->colour);
		
		$res .= pack('f4',	$this->m_shadervars[0],
							$this->m_shadervars[1],
							$this->m_shadervars[2],
							$this->m_shadervars[3]);
		
		$res .= pack('f', $this->m_rotation_angle);
		$res .= pack('l2', $this->m_imageindex1, $this->m_imageindex2);
		if ($v >= 110 && $v < 130){
			$res .= pack('f', $this->m_font_scale);
		}
		
		$res .= pack('l', $this->interpolationtime);
		$res .= pack('l', $this->interpolationpropertymask);
		$res .= pack('f', $this->easing_weight);
		
		$res .= write_string($this->linear);
		
		$res .= pack('l', sizeof($this->attr));
		foreach ($this->attr as $attr){
			$res .= $attr->dumpFile();
		}
		
		if ($v >= 90 && $v < 100){
			$res .= fromhex($this->b2);
		}
		else if ($v >= 100 && $v < 110){
			$res .= fromhex($this->b2);
			
			if ($v >= 106){ $res .= write_string($this->str_sth); }
			if ($v >= 104){ $res .= write_string($this->b3); }
			else{ $res .= fromhex($this->b3); }
		}
		else if ($v >= 110 && $v < 120){
			$res .= fromhex($this->b2);
		}
		else if ($v >= 120 && $v < 130){
			$res .= fromhex($this->b2);
			$res .= write_string($this->b3);
		}
		
		return $res;
	}
}

class UIC__Func_Anim_Attr {
	public $anim;
	public $uid;
	public $b_sth = null;
	public $animation;
	public $state;
	public $property;
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		foreach ($a as $k => $v){
			if (!property_exists('UIC__Func_Anim_Attr', $k)){ continue; }
			$this->$k = $v;
		}
		if ($parent !== null){ $this->anim = $parent; }
	}
	public function read($h, $my, $func, $anim){
		$this->anim = $anim;
		$v = $my->version;
		
		// trigger_component
		$this->uid = tohex(fread($h, 4));
		if ($v >= 125 && $v < 130){
			$this->b_sth = tohex(fread($h, 16));
		}
		
		$this->animation = read_string($h, 1, $my);
		$this->state = read_string($h, 1, $my);
		
		$this->property = read_string($h, 1, $my);
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'anim'
			))){ continue; }
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function dumpJS(){
		return array(
			$this->uid,
			$this->b_sth,
			$this->animation,
			$this->state,
			$this->property
		);
	}
	public function dumpFile(){
		$v = $this->anim->func->my->version;
		$res = '';
		
		$res .= fromhex($this->uid);
		if ($v >= 125 && $v < 130){
			$res .= fromhex($this->b_sth);
		}
		
		$res .= write_string($this->animation);
		$res .= write_string($this->state);
		
		$res .= write_string($this->property);
		
		return $res;
	}
}

class UIC_Template {
	public $version;
	public $parent;
	
	public $name;
	public $uid = null;
	public $b_sth = null;
	
	public $num_template;
	public $template = array();
	
	public $num_child;
	public $child = array();
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		foreach ($a as $k => $v){
			if (!property_exists('UIC_Template', $k)){ continue; }
			if ($k === 'template'){
				foreach ($v as $child){
					$this->template[] = new UI_Template_Child($child, $this);
				}
			} else if ($k === 'child'){
				foreach ($v as $child){
					if ($child instanceof UIC){
						$this->child[] = new UIC($child, $this);
					} else{
						$this->child[] = new UIC_Template($child, $this);
					}
				}
			} else{
				$this->$k = $v;
			}
		}
		if ($parent !== null){ $this->parent = $parent; }
	}
	public function read($h, $parent){
		global $DIR_DATA;
		
		$this->version = $parent->version;
		$this->parent = $parent;
		
		$v = $this->version;
		
		$this->name = read_string($h, 1, $this);
		if ($v >= 110 && $v < 130){
			$this->uid = tohex(fread($h, 4));
		}
		if ($v >= 120 && $v < 130){
			$this->b_sth = tohex(fread($h, 16));
		}
		
		$h_t = fopen($DIR_DATA['templates']['DIR'] . $this->name, 'r');
		my_assert($h_t, $this);
		$uic_t = new UIC();
		$uic_t->read($h_t);
		$uic_t = $uic_t->child[0];
		
		$this->num_template = array_shift(unpack('l', fread($h, 4)));
		my_assert($this->num_template < 64, $this);
		for ($i = 0; $i < $this->num_template; ++$i){
			$this->template[] = $a = new UI_Template_Child();
			$a->read($h, $this, $uic_t);
		}
		
		$this->num_child = array_shift(unpack('l', fread($h, 4)));
		my_assert($this->num_child < 20, $this);
		for ($i = 0; $i < $this->num_child; ++$i){
			$this->child[] = $a = new UIC();
			$a->read($h, $this);
		}
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'version', 'parent',
				// 'num_child', 'child'
			))){ continue; }
			if (in_array($k, array('template', 'child'))){
				$b = array();
				foreach ($v as $child){
					$b[] = $child->debug();
				}
				$a[ $k ] = $b;
				continue;
			}
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function root(){
		$p = $this;
		while ($p->parent !== null){
			$p = $p->parent;
		}
		return $p;
	}
	public function dumpJS(){
		$a = array(
			null,
			$this->name,
			$this->uid,
			$this->b_sth,
		);
		
		$arr = array();
		foreach ($this->template as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		$arr = array();
		foreach ($this->child as $b){
			$arr[] = $b->dumpJS();
		}
		$a[] = $arr;
		
		return $a;
	}
	public function dumpFile(){
		global $DIR_DATA;
		
		$v = $this->version;
		$res = '';
		
		$res .= write_string($this->name);
		if ($v >= 110 && $v < 130){
			$res .= fromhex($this->uid);
		}
		if ($v >= 120 && $v < 130){
			$res .= fromhex($this->b_sth);
		}
		
		$res .= pack('l', sizeof($this->template));
		foreach ($this->template as $template){
			$res .= $template->dumpFile();
		}
		
		$res .= pack('l', sizeof($this->child));
		foreach ($this->child as $child){
			$res .= $child->dumpFile();
		}
		
		return $res;
	}
	public function setVersion($v){
		$this->version = $v;
		foreach ($this->child as $child){
			$child->setVersion($v);
		}
	}
	public function eachChild($func){
		$func($this);
		foreach ($this->child as $child){
			$child->eachChild($func);
		}
	}
}

class UI_Template_Child {
	#region vars
	public $my;
	public $name_src;
	public $name_dst;
	
	public $b_sth = null;
	public $states_sth = null;
	
	public $b0 = null;
	public $type = null;
	
	public $num_events;
	public $events = array();
	
	public $func_name;
	
	public $b_floats;
	public $b_ints;
	public $b1;
	public $docking;
	public $b2;
	
	public $tooltip_id;
	public $tooltip_text;
	
	public $b3;
	public $states = array();
	
	public $num_dynamic;
	public $dynamic = array();
	
	public $num_images;
	public $images = array();
	
	public $b4;
	public $arr_sth = null;
	public $images_sth = null;
	#endregion
	
	public function __construct($a = null, $parent = null){
		if ($a === null){ return; }
		foreach ($a as $k => $v){
			if (!property_exists('UI_Template_Child', $k)){ continue; }
			$this->$k = $v;
		}
		if ($parent !== null){ $this->my = $parent; }
	}
	public function read($h, $my, $uic_t){
		$this->my = $my;
		$v = $my->version;
		
		// uic source name. empty for the first child of root
		$this->name_src = read_string($h, 1, $my); 
		$this->name_dst = read_string($h, 1, $my, false); // uic dest name
		
		if ($v >= 120 && $v < 130){
			$this->b_sth = tohex(fread($h, 16));
			$num_states = my_unpack_one($my, 'l', fread($h, 4));
			$this->states_sth = array();
			for ($i = 0; $i < $num_states; ++$i){
				$a = array();
				$a[] = read_string($h, 1, $my);
				$a[] = tohex(fread($h, 16));
				$this->states_sth[] = $a;
			}
		}
		
		$this->b0 = read_string($h, 1, $my);
		
		if ($v >= 100 && $v < 110){
			$this->type = read_string($h, 1, $my);
		} else if ($v >= 110 && $v < 130){
			$this->num_events = my_unpack_one($my, 'l', fread($h, 4));
			my_assert($this->num_events < 20, $my);
			for ($i = 0; $i < $this->num_events; ++$i){
				$a = read_string($h, 3, $this);
				if ($v >= 125 && $v < 130){
					$num_sth = my_unpack_one($this, 'l', fread($h, 4));
					$b = array();
					for ($j = 0; $j < $num_sth; ++$j){
						$b[] = read_string($h, 2, $this);
					}
					$a[] = $b;
				}
				$this->events[] = $a;
			}
		}
		
		$this->func_name = read_string($h, 1, $my); // название функции в коде?
		
		// offset.left?, offset.top?
		// width, height
		$this->b_floats = my_unpack_array($my, 'f4', fread($h, 4 * 4));
		$this->b_ints = my_unpack_array($my, 'l2', fread($h, 4 * 2));
		 // unknown
		if ($v >= 120 && $v < 130){
			$this->b1 = tohex(fread($h, 2));
		} else{
			$this->b1 = tohex(fread($h, 1));
		}
		// not sure if my guess is even remotely correct
		$this->docking = my_unpack_one($my, 'l', fread($h, 4));
		$this->b2 = tohex(fread($h, 6)); // unknown
		
		$this->tooltip_id = read_utf8_string($h, 1, $my); // tooltip uid?
		$this->tooltip_text = read_utf8_string($h, 1, $my); // tooltip text?
		
		
		if ($this->name_src){
			$ch = $uic_t->find($this->name_src);
		} else{ $ch = $uic_t; }
		
		if ($v >= 120 && $v < 130){
			$this->b3 = tohex(fread($h, 1));
		}
		$i = 0;
		// тут нужно делать не while, в брать инфу с template?
		// main problem with this part, is the guy who rote this section of ui templates
		// is fucking retard. there is no number of states, in some files u get
		// NewState, which doesn't exists in template, or not all of states
		// that are present in template.
		while (true){
			$a = array();
			$num = unpack('S2', fread($h, 4));
			fseek($h, -4, SEEK_CUR);
			// so we can make sure that we are not overflowing with states,
			// is to check strlen and next 2 bytes. we know that all strings are not empty and
			// should be printable (but there is one exception somewhere, btn{1F}_sth)
			// and practically num_dynamic shouldn't be that big, so last 2 bytes should be zero
			if ($num[1] === 0 || $num[2] === 0){
				break;
			}
			$a[] = $state = read_string($h, 1, $my);
			
			// should we bother correlating with an actual fields in template?
			$a[] = read_utf8_string($h, 1, $my);
			$a[] = read_utf8_string($h, 1, $my);
			$a[] = read_utf8_string($h, 1, $my);
			$a[] = read_utf8_string($h, 1, $my);
			if ($v >= 120 && $v < 130){
				$a[] = tohex(fread($h, 1));
			}
			++$i;
			$this->states[] = $a;
			// NewState check
			if ($ch){
				if ($i >= $ch->num_states || $i === 1 && !$ch->hasState($state)){
					break;
				}
			}
		}
		
		$this->num_dynamic = my_unpack_one($my, 'l', fread($h, 4));
		my_assert($this->num_dynamic < 20, $my);
		for ($i = 0; $i < $this->num_dynamic; ++$i){
			$this->dynamic[] = read_string($h, 2, $my);
		}
		
		$this->num_images = my_unpack_one($my, 'l', fread($h, 4));
		my_assert($this->num_images < 20, $my);
		for ($i = 0; $i < $this->num_images; ++$i){
			$this->images[] = read_string($h, 1, $my);
		}
		
		if ($v >= 120 && $v < 130){
			$this->b4 = tohex(fread($h, 4));
			
			$num_sth = my_unpack_one($my, 'l', fread($h, 4));
			my_assert($num_sth < 20, $my);
			$this->arr_sth = array();
			for ($i = 0; $i < $num_sth; ++$i){
				$a = array();
				$a[] = read_string($h, 1, $my);
				$a[] = read_string($h, 1, $my);
				$a[] = tohex(fread($h, 4));
				$a[] = tohex(fread($h, 12));
				$this->arr_sth[] = $a;
			}
			
			$num_images = my_unpack_one($my, 'l', fread($h, 4));
			my_assert($num_images < 20, $my);
			$this->images_sth = array();
			for ($i = 0; $i < $num_images; ++$i){
				$a = array();
				$a[] = read_string($h, 1, $my);
				$a[] = tohex(fread($h, 16));
				$this->images_sth[] = $a;
			}
		}
	}
	public function debug(){
		$a = array();
		foreach ($this as $k => $v){
			if (in_array($k, array(
				'my',
				// 'num_events', 'events',
				// 'func_name', 'b1', 'tooltip_id', 'tooltip_text',
				// 'states',
				// 'num_dynamic', 'dynamic', 'num_images', 'images'
			))){ continue; }
			$a[ $k ] = $v;
		}
		return $a;
	}
	public function dumpJS(){
		return array(
			$this->name_src,
			$this->name_dst,
			
			$this->b_sth,
			$this->states_sth,
			
			$this->b0,
			$this->type,
			$this->events,
			$this->func_name,
			
			$this->b_floats,
			$this->b_ints,
			$this->b1,
			$this->docking,
			$this->b2,
			
			$this->tooltip_id,
			$this->tooltip_text,
			
			$this->b3,
			$this->states,
			$this->dynamic,
			$this->images,
			
			$this->b4,
			$this->arr_sth,
			$this->images_sth
		);
	}
	public function dumpFile(){
		$v = $this->my->version;
		$res = '';
		
		$res .= write_string($this->name_src);
		$res .= write_string($this->name_dst);
		
		if ($v >= 120 && $v < 130){
			$res .= fromhex($this->b_sth);
			$res .= pack('l', sizeof($this->states_sth));
			foreach ($this->states_sth as $a){
				$res .= write_string($a[0]);
				$res .= fromhex($a[1]);
			}
		}
		
		$res .= write_string($this->b0);
		
		if ($v >= 100 && $v < 110){
			$res .= write_string($this->type);
		}
		else if ($v >= 110 && $v < 130){
			$res .= pack('l', sizeof($this->events));
			foreach ($this->events as $a){
				$res .= write_string(array_slice($a, 0, 3));
				if ($v >= 125 && $v < 130){
					$res .= pack('l', sizeof($a[3]));
					foreach ($a[3] as $b){
						$res .= write_string(array_slice($b, 0, 2));
					}
				}
			}
		}
		
		$res .= write_string($this->func_name);
		
		$res .= pack('f4',	$this->b_floats[0],
							$this->b_floats[1],
							$this->b_floats[2],
							$this->b_floats[3]);
		
		$res .= pack('l2',	$this->b_ints[0],
							$this->b_ints[1]);
		
		$res .= fromhex($this->b1);
		$res .= pack('l', $this->docking);
		$res .= fromhex($this->b2);
		
		$res .= write_utf8_string($this->tooltip_id);
		$res .= write_utf8_string($this->tooltip_text);
		
		if ($v >= 120 && $v < 130){
			$res .= fromhex($this->b3);
		}
		
		foreach ($this->states as $a){
			$res .= write_string($a[0]);
			$res .= write_utf8_string($a[1]);
			$res .= write_utf8_string($a[2]);
			$res .= write_utf8_string($a[3]);
			$res .= write_utf8_string($a[4]);
			if ($v >= 120 && $v < 130){
				$res .= fromhex($a[5]);
			}
		}
		
		$res .= pack('l', sizeof($this->dynamic));
		foreach ($this->dynamic as $dynamic){
			$res .= write_string($dynamic);
		}
		
		$res .= pack('l', sizeof($this->images));
		foreach ($this->images as $image){
			$res .= write_string($image);
		}
		
		if ($v >= 120 && $v < 130){
			$res .= fromhex($this->b4);
			
			$res .= pack('l', sizeof($this->arr_sth));
			foreach ($this->arr_sth as $a){
				$res .= write_string($a[0]);
				$res .= write_string($a[1]);
				$res .= fromhex($a[2]);
			}
			
			$res .= pack('l', sizeof($this->images_sth));
			foreach ($this->images_sth as $a){
				$res .= write_string($a[0]);
				$res .= fromhex($a[1]);
			}
		}
		
		return $res;
	}
}





































