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
		tmp.newBtn = new Element('button', {'class': 'newNutritionBtn btn btn-success btn-sm'})
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
					tmp.panel = tmp.deleteBtn.up('.panel.material_nutrition');
					if(!tmp.material_nutrition)
						tmp.panel.remove();
					else {
						tmp.active = !tmp.active;
						tmp.material_nutrition.active = tmp.active;
						tmp.panel.writeAttribute('active', tmp.active);
						tmp.deleteBtn.replace(tmp.me._getNutritionRowDeleteBtn(tmp.material_nutrition, tmp.className));
					}
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
		tmp.container.addClassName('col-xs-12')
			.insert({'bottom': new Element('div', {'class': 'material_nutrition panel panel-default', 'material_nutrition_id': (tmp.material_nutrition ? tmp.material_nutrition.id : 'new'), 'active': (tmp.material_nutrition ? tmp.material_nutrition.active : true) })
				.insert({'bottom': new Element('div', {'class': 'panel-heading'})
					.update(tmp.material_nutrition ? 'Editing Nutrition' : 'New Nutrition')
				})
				.insert({'bottom': new Element('div', {'class': 'panel-body'})
					.insert({'bottom': tmp.nutrition = new Element('div', {'class': 'nutrition'}) })
					.insert({'bottom': tmp.qty = new Element('div', {'class': 'qty'}) })
					.insert({'bottom': tmp.servemeasurement = new Element('div', {'class': 'servemeasurement'}) })
					.insert({'bottom': tmp.me._getNutritionRowDeleteBtn(tmp.material_nutrition, 'col-xs-12 pull-right text-right') })
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
			._getSelect2Div('Nutrition', 'nutrition', (tmp.material_nutrition ? {'id': tmp.material_nutrition.nutrition.id, 'text': tmp.material_nutrition.nutrition.name, 'data': tmp.material_nutrition.nutrition} : null), tmp.nutrition, null, true, tmp.nutritionSelect2Options)
			._getInputDiv('qty', (tmp.material_nutrition ? tmp.material_nutrition.qty : ''), tmp.qty, null ,true)
			._getSelect2Div('ServeMeasurement', 'serveMeasurement', (tmp.material_nutrition ? {'id': tmp.material_nutrition.serveMeasurement.id, 'text': tmp.material_nutrition.serveMeasurement.name, 'data': tmp.material_nutrition.serveMeasurement} : null), tmp.servemeasurement, null, true, tmp.serveMeasurementSelect2Options)
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