/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
	loadSelect2: function() {
		var tmp = {};
		tmp.me = this;
		
		jQuery('select.select2').each(function(){
			tmp.options = {};
			if($(this).readAttribute('data-minimum-results-for-search') === 'Infinity' || $(this).readAttribute('data-minimum-results-for-search') === 'infinity' || $(this).readAttribute('data-minimum-results-for-search') == -1)
				tmp.options['minimumResultsForSearch'] = 'Infinity';
			jQuery(this).select2(tmp.options);
		});
		
		tmp.selectBox = jQuery('[search_field="mat.ingredients"]').select2({
			allowClear: true,
			multiple: true,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'Ingredient', 'pageNo': 1};
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
		
		tmp.selectBox = jQuery('[search_field="mat.nutritions"]').select2({
			allowClear: true,
			multiple: true,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'Nutrition', 'pageNo': 1};
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
	,_getNutritionNameString: function(row) {
		var tmp = {};
		tmp.me = this;
		tmp.result = '';
		tmp.names = [];
		tmp.glue = ', ';
		
		if(row.infos.material_nutrition && Array.isArray(row.infos.material_nutrition) && row.infos.material_nutrition.length > 0) {
			row.infos.material_nutrition.each(function(item){
				if(item.nutrition && item.nutrition.name) {
					tmp.name = item.nutrition.name;
					if(item.qty && item.serveMeasurement && item.serveMeasurement.name)
						tmp.name += '(' + item.qty + ' ' + item.serveMeasurement.name + ')';
					tmp.names.push(tmp.name);
				}
			});
		}
		
		if(tmp.names.length > 0)
			tmp.result = tmp.names.join(tmp.glue);
		return tmp.result;
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
			.insert({'bottom': new Element(tmp.tag, {'class': 'name col-sm-4 col-xs-12'}).update(row.name) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'description col-sm-2 col-xs-12'}).update(row.description) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'ingredients col-sm-2 col-xs-12'}).update(tmp.isTitle === true ? 'Ingredients' : tmp.me._getNamesString(row.infos.ingredients)) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'nutritions col-sm-2 col-xs-12'}).update(tmp.isTitle === true ? 'Nutritions' : tmp.me._getNutritionNameString(row) ) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'text-right btns col-sm-2 col-xs-12'}).update(
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
});