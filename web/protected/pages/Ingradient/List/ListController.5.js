/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
	_getTitleRowData: function() {
		return {'id': "ID", 'active': 'Active', 'name': 'Name', 'description': 'Description'};
	}
	,_bindSearchKey: function() {
		var tmp = {}
		tmp.me = this;
		$('searchPanel').getElementsBySelector('[search_field]').each(function(item) {
			item.observe('keydown', function(event) {
				tmp.me.keydown(event, function() {
					$('searchBtn').click();
				});
			})
		});
		return this;
	}
	,loadSelect2: function() {
		var tmp = {};
		tmp.me = this;
		
		jQuery('.select2').select2();
	}
	,_getResultRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		console.debug(row);
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'strong' : 'span');
		tmp.row = new Element('span', {'class': 'row'})
			.store('data', row)
			.addClassName( (row.active === false && tmp.isTitle === false ) ? 'warning' : '')
			.addClassName('list-group-item')
			.addClassName('item_row')
			.writeAttribute('item_id', row.id)
			.insert({'bottom': new Element(tmp.tag, {'class': 'name col-md-6'}).update(row.name) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'description col-md-2'}).update(row.description) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'allergents col-md-2'}).update(tmp.isTitle === true ? 'Allgergents' : tmp.me._getNamesString(row.infos.allergents)) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'text-right btns col-md-2'}).update(
				tmp.isTitle === true ?  
					(new Element('span', {'class': 'btn btn-primary btn-xs', 'title': 'New'})
						.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-plus'}) })
						.insert({'bottom': ' NEW' })
						.observe('click', function(){
							tmp.me._openDetailsPage();
						})
					)
				: 
					(new Element('span', {'class': 'btn-group btn-group-xs'})
						.insert({'bottom': tmp.editBtn = new Element('span', {'class': 'btn btn-primary', 'title': 'Delete'})
							.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-pencil'}) })
							.observe('click', function(){
								tmp.me._openDetailsPage(row);
							})
						})
						.insert({'bottom': new Element('span')
							.addClassName( (row.active === false && tmp.isTitle === false ) ? 'btn btn-success' : 'btn btn-danger')
							.writeAttribute('title', ((row.active === false && tmp.isTitle === false ) ? 'Re-activate' : 'De-activate') )
							.insert({'bottom': new Element('span') 
								.addClassName( (row.active === false && tmp.isTitle === false ) ? 'glyphicon glyphicon-repeat' : 'glyphicon glyphicon-trash')
							})
							.observe('click', function(){
								if(!confirm('Are you sure you want to delete this item?'))
									return false;
								tmp.me._deleteItem(row, true);
							})
						}) 
					)
			) })
		;
		return tmp.row;
	}
	,_openDetailsPage: function(row) {
		var tmp = {};
		tmp.me = this;
		jQuery.fancybox({
			'width'			: '95%',
			'height'		: '95%',
			'autoScale'     : false,
			'autoDimensions': false,
			'fitToView'     : false,
			'autoSize'      : false,
			'type'			: 'iframe',
			'href'			: '/ingradient/' + (row ? row.id : 'new') + '.html',
			'helpers'		: {
				'overlay': {
			    	'locked': false
				}
			},
			'beforeClose'	    : function() {
			}
 		});
		return tmp.me;
	}
	,_updateItem: function(btn, entityId, newValue, method) {
		var tmp = {};
		tmp.me = this;
		tmp.itemId = $(btn).up('.item_row[item_id]').readAttribute('item_id');
		tmp.data = {'itemId': tmp.itemId, 'entityId': entityId, 'newValue': newValue, 'method': method};
		if(tmp.data === null)
			return;

		tmp.me.postAjax(tmp.me.getCallbackId('updateItem'), tmp.data, {
			'onLoading': function () {
				$(btn).hide();
			}
			,'onSuccess': function(sender, param) {
				try{
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item)
						return;
					tmp.row = $(tmp.me.resultDivId).down('#'+tmp.me.resultDivId+'-body').down('.item_row[item_id=' + tmp.result.item.id + ']');
					tmp.newRow = tmp.me._getResultRow(tmp.result.item).addClassName('list-group-item').addClassName('item_row').writeAttribute('item_id', tmp.result.item.id);
					tmp.row.replace(tmp.newRow);
				} catch (e) {
					tmp.me.showModalBox('<span class="text-danger">ERROR:</span>', e, true);
					$(btn).show();
				}
			}
		});
		return tmp.me;
	}
});