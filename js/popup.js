(function(){
var $stack = []
var $cur
var $clickCanProcess = !false
var $ok = false
setTimeout(function(){
	$ok = true
}, 100)
$.subscribe('bodyShow', function(){
	$(document.body).mousedown(function(e){
		var i
		while ($cur && $cur.event === 'mousedown' && i !== $stack.length){
			i = $stack.length
			if ($cur.canProcess){
				if (!e.target.HC($cur.allowed)){ $cur.Pop(true) }
			} else{
				$cur.canProcess = true
				break
			}
		}
	})
	.click(function(e){
		if (e.which !== 1 || !$clickCanProcess){ return }
		var i
		while ($cur && $cur.event === 'click' && i !== $stack.length){
			i = $stack.length
			if ($cur.canProcess){
				if (!e.target.HC($cur.allowed)){
					$cur.Pop(true)
					if (!e.target.HC(document.body)){
						if ($cur){ $cur.canProcess = true }
						break
					}
				}
			} else{
				$cur.canProcess = true
				break
			}
		}
	})
})
var $pop = function(obj, from_watcher){
	for (var i = $stack.length - 1; i >= 0; --i){
		if ($stack[i] !== obj){ continue }
		$stack.splice(i, 1)
		if (i === $stack.length){
			$cur = null
			if (i > 0){
				$cur = $stack[i - 1]
				if (!from_watcher && (1 || window.event && window.event instanceof MouseEvent)){ $cur.canProcess = false }
			}
		}
		break
	}
}

/*
Полный контроль над API.
Создаётся объект типа 'если кликнешь не на allowed и созданные элементы в create, то вызовется onHide'
Никаких event сам не прописывает
{
	[allowed] - разрешённые элементы
	[event] = 'mousedown' || 'click'
	create - function()
	returns созданные элементы для popup.show/hide
	[push] - function()
	[pop] - function()
	Вы делаете выбор 'должен быть вызван .hide или .remove' (или ничего)
	[show] - function()
	[hide] - function()
	[remove] - function()
}
*/
Popup = function(e){
	this.e = e
	this.event = e.event || 'mousedown'
	this.visible = false
	this.el = false
	this.allowed = e.allowed || []
	this.canProcess = !true
	this.hide_stack = null
	//if ($ok){ console.trace(this) }
}
extProt(
	Popup.prototype,
	'Show', function(prevent){
		//console.trace('Show')
		if (!this.visible){
			if (prevent || !this.e.show || this.e.show() !== false){
				this.visible = true
				if (this.hide_stack){
					var hide_stack = this.hide_stack.slice(0)
					for (var i = 0; i < hide_stack.length; ++i){
						var a = hide_stack[i]
						a.canProcess = true
						a.hide_stack = null
						$stack.push(a)
					}
					$cur = null
					$cur = $stack[ $stack.length - 1 ]
				}
			}
		}
		return this
	},
	'Hide', function(prevent){
		//console.trace('Hide')
		if (this.visible){
			if (prevent || !this.e.hide || this.e.hide() !== false){
				this.visible = false
				this.hide_stack = $stack.splice($stack.index(this))
				$cur = null
				if ($stack.length){
					$cur = $stack[ $stack.length - 1 ]
				}
				this.canProcess = false
				if (window.event && $cur){ $cur.canProcess = false }
			}
		}
		return this
	},
	'Push', function(prevent){
		//console.trace('Push')
		if ($stack.has(this)){ return this }
		var event = window.event
		
		// Create
		if (this.el === false){
			this.el = this.e.create()
			if (this.el){ this.allowed.add(this.el) }
			this.canProcess = false
			if (
			!event ||
			this.event === 'mousedown' && ['click', 'contextmenu'].has(event.type) ||
			this.event === 'click' && ['mousedown', 'focus'].has(event.type)
			){
				this.canProcess = true
			}
		}
		if (!prevent && this.e.push){ this.e.push() }
		
		//if (event){ $clickCanProcess = false }
		if (event && $cur){
			if (['mousedown', 'focus'].has(event.type)){
				var i
				while ($cur && $cur.event === 'mousedown' && i !== $stack.length){
					i = $stack.length
					if (!event.target.HC($cur.allowed)){
						$cur.Pop(true)
						if (!event.target.HC(document.body)){
							if ($cur){ $cur.canProcess = true }
							break
						}
					}
					else{ break }
				}
			}
			else if (event.type === 'click'){
				var i
				while ($cur && $cur.event === 'click' && i !== $stack.length){
					i = $stack.length
					if (!event.target.HC($cur.allowed)){ $cur.Pop(true) }
					else{ break }
				}
			}
		}
		$cur = null
		$stack.push($cur = this)
		return this
	},
	'Pop', function(from_watcher){
		if (this.el !== false){
			var event = window.event
			if (this.e.pop){
				if (this.e.pop() !== false){ $pop(this, from_watcher) }
			}
			else{ this.Remove(null, null, from_watcher) }
		}
		return this
	},
	'Remove', function(prevent, from_delete, from_watcher){
		if (this.el !== false){
			if (prevent || !this.e.remove || this.e.remove() !== false){
				if (this.hide_stack){
					for (var i = 0; i < this.hide_stack.length; ++i){
						this.hide_stack[ i ][from_delete ? 'Delete' : 'Remove']()
					}
				}
				$pop(this, !from_delete && from_watcher)
				if (this.el){ this.allowed.not(this.el) }
				this.el = false
			}
		}
		this.hide_stack = null
		return this
	},
	'IsTop', function(){
		return ($cur === this)
	},
	'Delete', function(prevent, from_watcher){
		this.Remove(prevent, true, from_watcher)
		FREE(this)
		return this
	}
	/*,'Kill', function(prevent){
		// Костылище
		this.Remove(prevent, false, window.event && window.event instanceof MouseEvent ? false : true)
		FREE(this)
		return this
	}*/
)


Tooltip = function(el, content){
	// console.log('Tooltip', arguments)
	var cont
	function create(){
		var offset = $(el).offset(),
			width = $(el).width(),
			height = $(el).height()
		el.B(cont = E('span', { class: 'pr' })
		.A(
			E('div', {
				class: 'pa bra tooltip-cont',
				style: 'top: '+ (height) +'px; left: 0; min-width: 400px;'
				// style: 'top: '+ (offset.top - 12) +'px; left: '+ (offset.left + width) +'px;'
			})
			.A(
				E('div', { class: 'tooltip-body' })
				.AA(content)
			)
		))
		// cont.firstChild.h('').AA(content)
		return cont
	}
	function remove(){
		el.CR('tooltip-active')
		el.__tooltip.popup.Delete(true)
		el.__tooltip = null
		cont.R()
	}
	el.CA('tooltip-active')
	
	var tdata = el.__tooltip
	if (!tdata){
		el.__tooltip = tdata = {
			popup: new Popup({
				event: 'click',
				allowed: [],
				create: create,
				remove: remove
			})
		}
		tdata.popup.Push()
	}
	else{
		// return;
		// tdata.popup.Remove()
	}
	return tdata
}

})()