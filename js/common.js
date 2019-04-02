
function emptyFunc(){}

/*
{
	url
	data
	[type] = POST
	[dataType] = json
	success
	error
	complete
}
*/
;(function(){
var elErr = E('div', { class: 'pf nowr pre js-error' })
var timeout = null
$.subscribe('bodyShow', function(){
	document.body.A(elErr)
})
ajax = function(q){
	if (q.el){
		// form
		q.url = q.url || q.el.ga('action') || $AJAX +'/'+ q.action
		q.method = q.el.ga('method')
		if (!q.data){ q.data = {} }
		q.el.on('submit', function(e){
			e.preventDefault()
			q.submit()
			var t = extend({}, q)
			t.data = $(this).serialize()
			for (var i in q.data){
				t.data += '&'+ i +'='+ q.data[i]
			}
			ajax(t)
			return false
		})
		delete q.el
		return
	}
	q.url = q.url || $AJAX +'/'+ q.action
	var success = q.success || emptyFunc
	var error = q.error || emptyFunc
	var complete = q.complete || emptyFunc
	delete q.success
	delete q.error
	delete q.complete
	q.type = q.type || 'POST'
	q.dataType = q.dataType || 'json'
	if (q.dataType === 'json'){
		q.success = success
		q.complete = complete
	}
	return $.ajax(q)
}
})();