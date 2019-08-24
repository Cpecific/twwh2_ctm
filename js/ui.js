(function(){

UI = {}
UI.Manager = function(q){
	var self = this
	this.el = E('div', { id: 'ui_manager' })
	.A(
		this.elLeft = E('table', { id: 'ui_left_outer', class: 'pa' })
		.A(E('thead')
			.A(E('tr').A(this.elTabs = E('td', { class: 'tab-cont' })
				.A(E('div', { class: 'fl tab active' }, 'Files').on('click', UI.Manager.Tab_onClick))
				.A(E('div', { class: 'fl tab' }, 'Info').on('click', UI.Manager.Tab_onClick))
				.A(E('div', { class: 'cl' }))
			))
		)
		.A(E('tbody').A(E('tr').A(E('td')
			.A(E('div')
				.A(E('table', { id: 'ui_left' }).A(E('tbody')
					.A(E('tr')
						.A(this.elDir = E('td', { id: 'ui_directories' }))
						.A(this.elInfo = E('td', { id: 'ui_info', style: 'display: none;' }))
					)
				))
			)
		)))
	)
	q.dir_data.each(function(a, key){
		self.elDir.A(
			E('div', { class: 'dir-folder active' }, a.name)
			.on('click', UI.Manager.DirFolder_onClick)
		)
		a.files.each(function(file){
			var elFile = E('div', { class: 'dir-file', style: 'display: none;' }, file)
			elFile._data = {
				key: key,
				file: file
			}
			self.elDir.A(
				elFile
				.on('click', self.SelectFile.bind(self, elFile))
			)
		})
	})
	q.el.A(this.el)
}

UI.Manager.Tab_onClick = function(){
	if (this.CC('active')){ return }
	var p = this.p()
	var active_idx = p.index(F('.active', p)[0].CR('active'))
	var idx = p.index(this.CA('active'))
	var ch = p.p(2).next().firstChild.firstChild.firstChild
	.firstChild.firstChild.firstChild.children
	ch[ active_idx ].H()
	ch[ idx ].S()
}
UI.Manager.DirFolder_onClick = function(){
	this.CT('active')
	var a = this
	while (a = a.next()){
		if (a.CC('dir-folder')){ return }
		a.TG()
	}
}

function UIC_PrintObject(object){
	switch (typeof object){
	case 'number':
		return [E('span', { class: 'print-number' }, object.toString())]
		break
	case 'boolean':
		return [E('span', { class: 'print-object' }, object.toString())]
		break
	case 'string':
		return [E('span', { class: 'print-string' }, '\''+ object +'\'')]
		break
	case 'object':
		if (object === null){
			return [E('span', { class: 'print-object' }, 'null')]
		}
	}
	
	if (object instanceof Array){
		var str = [ET('[')]
		for (var i = 0; i < object.length; ++i){
			var v = object[ i ]
			str.push(ET(', '))
			str = str.concat(UIC_PrintObject(v))
		}
		if (str.length > 1){ str.splice(1, 1) }
		str.push(ET(']'))
	}
	else{
		var str = [ET('{')]
		for (var i in object){
			var v = object[ i ]
			str.push(ET(', '))
			str.push(E('span', { class: 'print-string' }, i))
			str.push(ET(': '))
			str = str.concat(UIC_PrintObject(v))
		}
		if (str.length > 1){ str.splice(1, 1) }
		str.push(ET('}'))
	}
	return str
}
function UIC_Print(object, schema, schema_template, stack, path, level){
	level = level || 0
	stack = stack || []
	path = path || []
	stack.push(object)
	var children, block, top, bottom, value, array_children
	var title
	
	var el = E('div', { class: 'print-block' })
	.A(cont = E('div', {
		class: 'print-block-top',
		style: 'padding-left: '+ (level * 20) +'px;'
	}))
	
	var path_title = path.join(' > ')
	
	switch (typeof object){
	case 'number':
		cont.A(E('span', { class: 'print-number', title: path_title }, object.toString()))
		return el
		break
	case 'boolean':
	case 'null':
		cont.A(E('span', { class: 'print-object', title: path_title }, object.toString()))
		return el
		break
	case 'string':
		cont.A(E('span', { class: 'print-string', title: path_title }, '\''+ object +'\''))
		return el
		break
	}
	
	var is_array = false,
		data = object
	if (object instanceof UIC){
		cont.A(E('span', { class: 'print-object', title: path_title }, title = 'UIC'))
		data = data.data
	}
	else if (object instanceof UIC_Template){
		cont.A(E('span', { class: 'print-object', title: path_title }, title = 'UIC_Template'))
		data = data.data
	}
	else{
		is_array = (data instanceof Array)
		cont
		.A(E('span', { class: 'print-object', title: path_title }, title = (is_array ? 'Array('+ data.length +')' : 'object')))
		.A(ET(' '+ (is_array ? '[' : '{')))
	}
	
	el.A(children = E('div'))
	
	for (var i in data){
		var v = data[i]
		
		var sch
		if (title === 'UIC_Template'){ sch = schema_template[ i ] }
		else{ sch = schema[ i ] }
		sch = sch || {}
		var sch_type = (sch.type ? sch.type.split('.') : [])
		var sch_child = sch.child
		
		block = E('div', { class: 'print-block' })
		.A(cont = E('div', { class: 'print-block-top', style: 'padding-left: '+ ((level + 1) * 20) +'px;' }))
		if (!is_array){
			cont.A(E('span', { class: 'print-key' }, i))
			.A(ET(': '))
		}
		
		switch (typeof v){
		case 'number':
			cont.A(value = E('span', { class: 'print-number' }, v.toString()))
			if (sch_type[0] === 'dock'){
				var dock = ''
				if (v >= 0 && v <= 9){
					dock = [
					'span',
					'top-left', 'top', 'top-right',
					'center-left', 'center', 'center-right',
					'bottom-left', 'bottom', 'bottom-right'
					][v]
				}
				cont
				.A(ET(' ('))
				.A(E('span', { class: 'print-string' }, '\''+ dock +'\''))
				.A(ET(')'))
			}
			else if (sch_type[0] === 'angle'){
				var v2 = Math.round((Math.round(v * 1000) / 1000) / 3.1415 * 360)
				value.h(v2 +'&deg;')
			}
			break
		case 'boolean':
			cont.A(value = E('span', { class: 'print-object' }, v.toString()))
			break
		case 'string':
			cont.A(value = E('span', { class: 'print-string' }, '\''+ v +'\''))
			break
		case 'object':
			if (v === null){
				cont.A(value = E('span', { class: 'print-object' }, 'null'))
			}
			else{
				if (sch_type[0] === 'array'){
					if (v.length){ block.CA('print-collapsable') }
					cont.A(value = E('span', { class: 'print-object' }, 'Array('+ v.length +')'))
					block.A(array_children = E('div'))
					if (sch_child){
						for (var j = 0; j < v.length; ++j){
							var a = v[ j ],
								b = UIC_Print(
									a, sch_child, null, stack.slice(),
									path.concat([data.name, i, j]), level + 1)
							array_children.A(b)
						}
					}
					else{
						for (var j = 0; j < v.length; ++j){
							var a = v[ j ],
								b = UIC_Print(
									a, schema, schema_template, stack.slice(),
									path.concat([data.name, i, j]), level + 1)
							array_children.A(b)
						}
					}
				}
				else{
					cont.AA(UIC_PrintObject(v))
				}
			}
			break
		}
		
		if (sch.click){
			value.on('click', sch.click.bind(value, stack, path))
		}
		if (sch.class){
			value.CA(sch.class)
		}
		
		children.A(block)
	}
	
	if (title === 'object' || is_array){
		el.A(E('div', {
			class: 'print-block-footer',
			style: 'padding-left: '+ (level * 20) +'px;' }, is_array ? ']' : '}'
		))
	}
	
	return el
}

function UIC_images_path_click(stack){
	var value = stack[ stack.length - 1 ].path
	var content = E('img', { src: 'game/'+ value })
	Tooltip(this, content)
}
function UIC_State_bgs_click(stack, path){
	// ищем image
	var value = stack[ stack.length - 1 ].uid
	var uic = stack[ stack.length - 3 ] // bgs > state > uic
	var a = uic.data.images
	for (var i = 0; i < a.length; ++i){
		if (a[i].uid === value){
			a = a[i]
			break
		}
	}
	var content = UIC_Print(
		a, UIC.SCHEMA.images.child, null,
		stack.slice(0, stack.length - 2),
		// idx > bgs > state > idx > states
		path.slice(0, path.length - 5).concat(['images', i])
	)
	Tooltip(this, content)
}
function UIC_State_mouse_state_click(stack, path){
	// ищем state
	var value = stack[ stack.length - 1 ].state_uid
	var uic = stack[ stack.length - 3 ] // mouse > state > uic
	var a = uic.data.states
	for (var i = 0; i < a.length; ++i){
		if (a[i].uid === value){
			a = a[i]
			break
		}
	}
	var content = UIC_Print(
		a, UIC.SCHEMA.states.child, null,
		stack.slice(0, stack.length - 2),
		// idx > mouse > state > idx > states
		path.slice(0, path.length - 5).concat(['states', i])
	)
	Tooltip(this, content)
}

// UIC
var UIC = function(){
	
}
UIC.SCHEMA = {
	'uid': {},
	'name': {},
	'b0': {},
	'events': {},
	// не совсем тот offset, которые можно было бы предположить,
	// это смещение всех bgs относительно parent?
	'offset': {},
	'b1': {},
	'b_01': {},
	'tooltip_text': {},
	'tooltip_id': {},
	'docking': { type: 'dock' },
	'dock_offset': {},
	'b3': {},
	'default_state': {},
	'images': {
		type: 'array',
		child: {
			'path': {
				click: UIC_images_path_click,
				class: 'has-tooltip'
			}
		}
	},
	'maskimage': {},
	'b5': {},
	'states': {
		type: 'array',
		child: {
			'uid': {},
			'name': {},
			'bounds': {},
			'text': {},
			'tooltip': {},
			'textbounds': {},
			'textalign': {},
			'b1': {},
			'textlabel': {},
			'b3': {},
			'localized': {},
			'b4': {},
			'tooltip_id': {},
			'b5': {},
			'font_m_font_name': {},
			'font_m_size': {},
			'font_m_leading': {},
			'font_m_tracking': {},
			'font_m_colour': {},
			'fontcat_name': {},
			'textoffset': {},
			'b7': {},
			'shader_name': {},
			'shadervars': {},
			'text_shader_name': {},
			'textshadervars': {},
			'bgs': {
				type: 'array',
				child: {
					'uid': {
						click: UIC_State_bgs_click,
						class: 'has-tooltip'
					},
					'offset': {},
					'bounds': {},
					'colour': {},
					'str_sth': {},
					'tile': {},
					'x_flipped': {},
					'y_flipped': {},
					'dockpoint': { type: 'dock' },
					'dock_offset': {},
					'dock': {},
					'rotation_angle': { type: 'angle' },
					'pivot_point': {},
					'shader_name': {},
					'rotation_axis': {},
					'b4': {},
					'shadertechnique_vars': {},
					'margin': {}
				}
			},
			'b_mouse': {},
			'mouse': {
				type: 'array',
				child: {
					'mouse_state': {},
					'state_uid': {
						click: UIC_State_mouse_state_click,
						class: 'has-tooltip'
					},
					'b0': {},
					'num_sth': {},
					'sth': {
						type: 'array',
						child: {}
					}
				}
			}
		}
	},
	'dynamic': {
		type: 'array',
		child: {}
	},
	'b6': {},
	'funcs': {
		type: 'array',
		child: {
			'name': {},
			'b0': {},
			'anim': {
				type: 'array',
				child: {
					'b_sth': {},
					'b_str1': {},
					'b_str1': {},
					'offset': {},
					'bounds': {},
					'colour': {},
					
					'm_shadervars': {},
					'm_rotation_angle': { type: 'angle' },
					'm_imageindex1': {},
					'm_imageindex2': {},
					'm_font_scale': {},
					
					'interpolationtime': {},
					'interpolationpropertymask': {},
					'easing_weight': {},
					'linear': {},
					'attr': {
						type: 'array',
						child: {
							'uid': {},
							'animation': {},
							'state': {},
							'property': {}
						}
					},
					'b2': {},
					'str_sth': {},
					'b3': {}
				}
			},
			'str_sth': {},
			'b1': {}
		}
	},
	'child': {
		type: 'array'
	},
	'after': {}
}
UIC.SCHEMA_TEMPLATE = {
	'uid': {},
	'name': {},
	'template': {
		type: 'array',
		child: {
			'name_src': {},
			'name_dst': {},
			'b0': {},
			'type': {},
			'events': {},
			'func_name': {},
			'b_floats': {},
			'b_ints': {},
			'b1': {},
			'docking': { type: 'dock' },
			'b2': {},
			'tooltip_id': {},
			'tooltip_text': {},
			'states': {
				type: 'array'
			},
			'str_sth_1': {},
			'str_sth_2': {},
			'dynamic': {},
			'images': {
				type: 'array'
			}
		}
	},
	'child': {
		type: 'array'
	}
}
extProt(
	UIC.prototype,
	'Init', function(){
		var a = this.data.states
		for (var i = 0; i < a.length; ++i){
			if (a[i].uid === this.data.default_state){
				this.state = i
				return
			}
		}
	},
	'Print', function(container){
		return UIC_Print(this, UIC.SCHEMA, UIC.SCHEMA_TEMPLATE)
	},
	'GenerateDiv', function(){
		if (!this.data.parent){
			var el = E('div', { class: 'pr' })
			for (var i = 0; i < this.data.child.length; ++i){
				el.A(this.data.child[i].GenerateDiv())
			}
			return el
		}
		var data = this.data
		var el = E('div', {
			class: 'pa',
			title: data.name,
			style: 
			'left: '+ (data.offset.left) +'px; top: '+ (data.offset.top) +'px;'
		})
		var state = data.states[ this.state ]
		el.style.width = state.bounds.width +'px'
		el.style.height = state.bounds.height +'px'
		el.t(state.text)
	}
)

// UIC_Template
var UIC_Template = function(){
	
}
extProt(
	UIC_Template.prototype,
	'Init', function(){
		this.state = 0
	}
)

extProt(
	UI.Manager.prototype,
	'SelectTab', function(name){
		this.elTabs.children[ ['files', 'info'].index(name) ].click()
	},
	'LoadFile', function(data, success){
		function convert(r){
			var uic = new UIC()
			r = r.toKeys(
				'uid', 'b_sth', 'name', 'b0', 'events',
				'offset', 'b1', 'b_01', 'tooltip_text', 'tooltip_id',
				'docking', 'dock_offset',
				'b3', 'default_state', 'images',
				'maskimage', 'b5', 'b_sth2',
				'states', 'b_sth3', 'dynamic', 'b6',
				'funcs', 'child', 'after'
			)
			r.images.each(function(a, i){
				r.images[i] = a.toKeys('uid', 'b_sth', 'path', 'width', 'height', 'extra')
			})
			r.states.each(function(state, i){
				var state = state.toKeys(
					'uid', 'b_sth', 'name', 'bounds', 'text',
					'tooltip',
					'textbounds', 'textalign',
					'b1',
					'textlabel', 'b3',
					'localized', 'b4', 'tooltip_id', 'b5',
					'font_m_font_name', 'font_m_size', 'font_m_leading', 'font_m_tracking', 'font_m_colour',
					'fontcat_name',
					'textoffset', 'b7',
					'shader_name', 'shadervars',
					'text_shader_name', 'textshadervars',
					'bgs', 'b_mouse', 'mouse',
					'b8'
				)
				state.bounds = state.bounds.toKeys(
					'width', 'height'
				)
				state.bgs.each(function(bg, j){
					// порядок картинок снизу вверх (сначала нижние слои)
					state.bgs[j] = bg.toKeys(
						'uid', 'b_sth',
						'offset', 'bounds',
						'colour',
						'str_sth',
						'tile',
						'x_flipped', 'y_flipped', 'dockpoint',
						'dock_offset',
						'dock',
						'rotation_angle', 'pivot_point',
						'shader_name', 'rotation_axis',
						'b4',
						'shadertechnique_vars',
						'margin', 'b5'
					)
				})
				state.mouse.each(function(mouse, j){
					state.mouse[j] = mouse.toKeys(
						'mouse_state',
						'state_uid', 'b_sth',
						'b0', 'num_sth', 'sth', 'b1'
					)
				})
				r.states[i] = state
			})
			r.dynamic.each(function(dynamic, i){
				// dynamic_image, idx - SetStateImage только для images[ idx ]?
				r.dynamic[i] = dynamic.toKeys(
					'str1', 'str2'
				)
			})
			r.funcs.each(function(func, i){
				func = func.toKeys(
					'name', 'b0', 'anim',
					'str_sth', 'b1'
				)
				func.anim.each(function(anim, j){
					anim = anim.toKeys(
						'b_hex', 'b_str',
						'offset', 'bounds',
						'colour',
						
						'm_shadervars',
						'm_rotation_angle',
						'm_imageindex1',
						'm_imageindex2',
						'm_font_scale',
						
						'interpolationtime',
						'interpolationpropertymask',
						'easing_weight',
						'linear', 'attr',
						'b2', 'str_sth', 'b3'
					)
					anim.attr.each(function(attr, k){
						anim.attr[k] = attr.toKeys(
							'uid', 'b_sth',
							'animation',
							'state',
							// ((Visibility true, destroy, hide))
							'property'
						)
					})
					func.anim[j] = anim
				})
				r.funcs[i] = func
			})
			r.child.each(function(child, i){
				if (child[0] === null){ child = convertTemplate(child) }
				else{ child = convert(child)  }
				child.parent = uic
				r.child[i] = child
			})
			uic.data = r
			uic.parent = null
			uic.Init()
			return uic
		}
		function convertTemplate(r){
			var uic = new UIC_Template()
			r = r.toKeys(
				'uid', 'b_sth',
				'name', 'uid',
				'template',
				'child'
			)
			r.template.each(function(temp, i){
				r.template[i] = temp.toKeys(
					'name_src', 'name_dst',
					'b_sth', 'states_sth',
					'b0', 'type', 'events', 'func_name',
					'b_floats', 'b_ints',
					'b1', 'docking', 'b2',
					'tooltip_id', 'tooltip_text',
					'b3', 'states', 'dynamic', 'images',
					'b4', 'arr_sth', 'images_sth'
				)
			})
			r.child.each(function(child, i){
				child = convert(child)
				child.parent = uic
				r.child[i] = child
			})
			uic.data = r
			uic.parent = null
			uic.Init()
			return uic
		}
		ajax({
			url: 'get_uic.php',
			data: {
				KEY: data.key,
				FILE: data.file
			},
			success: function(r){
				success(convert(r))
			}
		})
	},
	'SelectFile', function(el){
		var self = this
		F('.dir-file', this.elDir).CR('active')
		el.CA('active')
		this.LoadFile(el._data, function(r){
			console.log(r)
			self.SelectTab('info')
			self.elInfo.h('').A(r.Print())
		})
	},
	'SetUICData', function(a){
	
	}
)

})()