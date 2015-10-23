/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();

		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me
			._getInputDiv('name', (tmp.me._item.name || ''), $(tmp.me._containerIds.name), null ,true)
			._getInputDiv('description', (tmp.me._item.description || ''), $(tmp.me._containerIds.description))
			._getSelect2Div('Ingredient', 'ingredients', tmp.me._item.id ? tmp.me._item.infos.ingredients : [], $(tmp.me._containerIds.ingredients), null)
			._getSaveBtn()
		;

		tmp.me._addNewNutritionBtn($(tmp.me._containerIds.new_material_nutrition));

		tmp.me._item.infos.material_nutrition.each(function(item){
			tmp.me._addNutritionRow(item, $(tmp.me._containerIds.material_nutrition));
		});
		return tmp.me;
	}
	,_addNewNutritionBtn: function(container) {
		var tmp = {};
		tmp.me = this;
		tmp.container = (container || null);
		if(!tmp.container || !tmp.container.id)
			return tmp.me;
		tmp.newBtn = new Element('button', {'class': 'newNutritionBtn btn btn-primary btn-sm'})
			.update('New Nutrition')
			.observe('click', function(e){
				tmp.newBtn.writeAttribute('disabled', true);
				tmp.container.insert({'bottom': tmp.newDiv = new Element('div')});
				tmp.me._signRandID(tmp.newDiv);
				tmp.me._addNutritionRow(null, tmp.newDiv);
				tmp.newBtn.writeAttribute('disabled', false);
			})
			;

		tmp.container.update(tmp.me._getFormGroup('', tmp.newBtn).addClassName('col-xs-12'));
		return tmp.me;
	}
	,_getNutritionRowDeleteBtn: function(material_nutrition, className) {
		var tmp = {};
		tmp.me = this;
		tmp.material_nutrition = (material_nutrition || null);
		tmp.active = (material_nutrition ? material_nutrition.active === true : true);
		tmp.className = (className || '');

		tmp.deleteBtn = new Element('button', {'class': (tmp.active === true ? 'btn btn-sm btn-danger' : 'btn btn-sm btn-success') })
			.addClassName(tmp.className)
			.insert({'bottom': new Element('i', {'class': (tmp.active === true ? 'glyphicon glyphicon-trash' : 'glyphicon glyphicon-repeat')}) })
			.observe('click', function(e){
				if(confirm('This nutrition will be ' + (tmp.active === true ? (tmp.material_nutrition ? 'DE-ACTIVATED' : 'REMOVED') : 'RE_ACTIVATED') + ', continue?')) {
					tmp.panel = tmp.deleteBtn.up('.material_nutrition');
					if(tmp.material_nutrition && tmp.material_nutrition.id) {
						tmp.panel.up().insert({'bottom': new Element('input', {'type': 'hidden', 'save-item': 'ignore_' + tmp.material_nutrition.id, 'dirty': true}) });
					}
					tmp.panel.remove();
					tmp.me._refreshDirty()._getSaveBtn();
				}
			})
			;
		return tmp.deleteBtn;
	}
	,_addNutritionRow: function(material_nutrition, container) {
		var tmp = {};
		tmp.me = this;
		tmp.material_nutrition = (material_nutrition || null);
		tmp.container = (container || null);
		if(!tmp.container || !tmp.container.id)
			return tmp.me;
		tmp.container
			.insert({'bottom': new Element('div', {'class': 'material_nutrition col-xs-12', 'material_nutrition_id': (tmp.material_nutrition ? tmp.material_nutrition.id : 'new'), 'active': (tmp.material_nutrition ? tmp.material_nutrition.active : true) })
				.insert({'bottom': new Element('div', {'class': 'row '})
					.insert({'bottom': tmp.nutrition = new Element('div', {'class': 'nutrition col-md-7 col-sm-4 col-xs-12'}) })
					.insert({'bottom': tmp.qty = new Element('div', {'class': 'qty col-md-2 col-sm-3 col-xs-12'}) })
					.insert({'bottom': tmp.servemeasurement = new Element('div', {'class': 'servemeasurement col-md-2 col-sm-3  col-xs-12'}) })
					.insert({'bottom': new Element('div', {'class': 'pull-right text-right col-md-1 col-sm-1 col-xs-12'}).update(tmp.me._getNutritionRowDeleteBtn(tmp.material_nutrition, 'col-xs-12')) })
				})
			});

		tmp.nutritionSelect2Options = {
			minimumInputLength: 1,
			multiple: false,
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
			};

		tmp.serveMeasurementSelect2Options = {
			minimumInputLength: 1,
			multiple: false,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'ServeMeasurement', 'pageNo': 1};
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
			};

		tmp.me
			._getSelect2Div('Nutrition', 'nutrition', (tmp.material_nutrition ? {'id': tmp.material_nutrition.nutrition.id, 'text': tmp.material_nutrition.nutrition.name, 'data': tmp.material_nutrition.nutrition} : null), tmp.nutrition, ' ', true, tmp.nutritionSelect2Options)
			._getInputDiv('qty', (tmp.material_nutrition ? tmp.material_nutrition.qty : ''), tmp.qty, ' ' , true, '', false, 'Qty')
			._getSelect2Div('ServeMeasurement', 'serveMeasurement', (tmp.material_nutrition ? {'id': tmp.material_nutrition.serveMeasurement.id, 'text': tmp.material_nutrition.serveMeasurement.name, 'data': tmp.material_nutrition.serveMeasurement} : null), tmp.servemeasurement, ' ', true, tmp.serveMeasurementSelect2Options)
		;
		return tmp.me;
	}
	,collectData: function() {
		var tmp = {};
		tmp.me = this;
		tmp.data = tmp.me._collectFormData($(tmp.me.getHTMLID('itemDiv')), 'save-item');
		if(!tmp.data)
			return null;
		tmp.data['material_nutrition'] = [];
		$(tmp.me.getHTMLID('itemDiv')).getElementsBySelector('.material_nutrition[material_nutrition_id]').each(function(item){
			tmp.material_nutrition = tmp.me._collectFormData($(item), 'save-item');
			if(tmp.material_nutrition)
				tmp.data['material_nutrition'].push(tmp.material_nutrition);
		});

		return tmp.data;
	}
});