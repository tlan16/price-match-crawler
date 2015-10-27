/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
	_getTitleRowData: function() {
		return {
			'id' : "ID"
			,'active' : 'Active'
			,'city' : 'City'
			,'contactName' : 'Contact Name'
			,'contactNo' : 'ContactNo'
			,'country' : 'Country'
			,'postCode' : 'Post Code'
			,'region' : 'Region'
			,'street' : 'Street'
			,'contactNo' : 'Contact Number'
			,'contactName' : 'Contact Name'
		};
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
		
		tmp.selectBox = jQuery('[search_field="ingr.allergents"]').select2({
			allowClear: true,
			multiple: true,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'Allergent', 'pageNo': 1};
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
			.insert({'bottom': new Element(tmp.tag, {'class': 'contactName col-md-2'}).update(row.contactName) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'contactNo col-md-2'}).update(row.contactNo) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'street col-md-2'}).update(row.street) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'city col-md-1'}).update(row.city) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'region col-md-1'}).update(row.region) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'country col-md-1'}).update(row.country) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'postCode col-md-1'}).update(row.postCode) })
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
			'href'			: '/address/' + (row ? row.id : 'new') + '.html',
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