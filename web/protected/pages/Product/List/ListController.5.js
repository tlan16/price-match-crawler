/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
	_getTitleRowData: function() {
		return {'id': "ID", 'active': 'Active', 'name': 'Name', 'description': 'Description', 'barcode': 'Barcode', 'unitPrice': 'Unit Price', 'size': 'Size', 'categories': 'Categories'};
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

		jQuery('select.select2').each(function(){
			tmp.options = {};
			if($(this).readAttribute('data-minimum-results-for-search') === 'Infinity' || $(this).readAttribute('data-minimum-results-for-search') === 'infinity' || $(this).readAttribute('data-minimum-results-for-search') == -1)
				tmp.options['minimumResultsForSearch'] = 'Infinity';
			jQuery(this).select2(tmp.options);
		});

		tmp.selectBox = jQuery('[search_field="pro.categories"]').select2({
			allowClear: true,
			multiple: true,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'Category', 'pageNo': 1};
				}
				,results: function(data, page, query) {
					tmp.result = [];
					if(data.resultData && data.resultData.items) {
						data.resultData.items.each(function(item){
							tmp.result.push({'id': item.id, 'text': item.name, 'data': item});
						});
					}
					return { 'results' : tmp.result };
				}
			}
			,cache: true
			,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
		});
	}
	,_getOjbNames: function (objects) {
		var tmp = {};
		tmp.names = [];
		objects.each(function(obj){
			tmp.names.push(obj.name);
		});
		return jQuery.unique(tmp.names);
	}
	,_getResultRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'strong' : 'span');
		tmp.row = new Element('span', {'class': 'row'})
			.store('data', row)
			.addClassName( (row.active === false && tmp.isTitle === false ) ? 'warning' : '')
			.addClassName('list-group-item')
			.addClassName('item_row')
			.writeAttribute('item_id', row.id)
			.insert({'bottom': new Element(tmp.tag, {'class': 'name col-sm-3 col-xs-12' + ( tmp.isTitle === true ? 'hidden-xs' :'')}).update(tmp.isTitle === true ? row.name : new Element('div')
				.insert({'bottom': new Element('div').update(row.name) })
				.insert({'bottom': new Element('div').insert({'bottom': new Element('i').update(new Element('small').update(row.description))}) })
			) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'barcode col-sm-2 col-xs-12' + ( tmp.isTitle === true ? 'hidden-xs' :'')}).update(row.barcode) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'unitPrice col-sm-2 col-xs-12' + ( tmp.isTitle === true ? 'hidden-xs' :'')}).update(tmp.isTitle === true ? row.unitPrice : tmp.me.getCurrency(row.unitPrice)) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'size col-md-1 hidden-sm col-xs-12' + ( tmp.isTitle === true ? 'hidden-xs' :'')}).update(row.size) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'size col-sm-2 col-xs-12' + ( tmp.isTitle === true ? 'hidden-xs' :'')}).update(tmp.isTitle === true ? row.categories : tmp.me._getOjbNames(row.categories).join(', ')) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'text-right btns col-sm-2 col-xs-12'}).update(
				tmp.isTitle === true ? '' :
					(new Element('span', {'class': 'btn-group'})
						.insert({'bottom': new Element('span')
							.addClassName( (row.active === false && tmp.isTitle === false ) ? 'btn btn-success' : 'btn btn-info')
							.writeAttribute('data-loading-text', '<i class="fa fa-refresh fa-spin"></i>')
							.insert({'bottom': new Element('span')
								.addClassName( (row.active === false && tmp.isTitle === false ) ? 'glyphicon glyphicon-repeat' : 'glyphicon glyphicon-print')
							})
							.observe('click', function(){
								tmp.me._printItem(this, row);
							})
						})
						.insert({'bottom': tmp.editBtn = new Element('span', {'class': 'btn btn-primary', 'title': 'Edit'})
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
								if(!confirm('Are you sure you want to ' + (row.active === true ? 'DE-ACTIVATE' : 'RE-ACTIVATE') +' this item?'))
									return false;
								tmp.me._deleteItem(row, row.active);
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
			'modal'			: true,
			'autoScale'     : false,
			'autoDimensions': false,
			'fitToView'     : false,
			'autoSize'      : false,
			'type'			: 'iframe',
			'href'			: '/product/' + (row ? row.id : 'new') + '.html',
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
	,_openLabel: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.data = $(btn).retrieve('labelData');
		tmp.newWindow = window.open('', '', 'width=300,height=800,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no');
		if(!tmp.newWindow) {
			tmp.me.showModalBox('<b>Window Popup Blocked</b>', 'Your browser has blocked the popup from this site, Please change your browser settings to allow popup from this site and try again <span class="btn btn-xs btn-info" onclick="$(' + "'" + btn.id + "'" + ').click();"> here </span>. <div></b>');
			return tmp.me;
		}
		tmp.newWindow.document.write(tmp.data);
		tmp.newWindow.document.close();
		tmp.newWindow.focus();
		tmp.newWindow.print();
		tmp.newWindow.close();
		return tmp.me;
	}
	,_printItem: function(btn, item) {
		var tmp = {};
		tmp.me = this;
		tmp.me._signRandID(btn);
		tmp.me.postAjax(tmp.me.getCallbackId('printLabel'), {'id': item.id}, {
			'onLoading': function () {
				jQuery(btn).button('loading');
			}
			,'onSuccess': function(sender, param) {
				try{
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item)
						return;
					tmp.imgData = tmp.result.item;
					btn.store('labelData', tmp.imgData);
					tmp.me._openLabel(btn);
				} catch (e) {
					tmp.me.showModalBox('<span class="text-danger">ERROR:</span>', e, true);
					$(btn).show();
				}
			}
			,'onComplete': function() {
				jQuery(btn).button('reset');
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