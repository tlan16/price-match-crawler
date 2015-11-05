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
		tmp.sellinAllStore = (tmp.me._item.sellInAllStores === true);
		tmp.storeTitle = new Element('div', {'class': 'row'})
			.insert({'bottom': new Element('div', {'class': 'col-xs-6'}).update('Selling in Stores:') })
			.insert({'bottom': new Element('div', {'class': 'col-xs-6 text-right'})
				.insert({'bottom': new Element('label')
					.update(' selling in all stores')
					.insert({'top': new Element('input', {'type': 'checkbox', 'save-item': 'allStores', 'dirty': false, 'checked': tmp.sellinAllStore})
						.observe('change', function(){
							this.writeAttribute('dirty', true );
							tmp.me._refreshDirty()._getSaveBtn();
							if(this.checked === true) {
								$(this).up('.form-group').down('.form-control').hide();
							} else {
								$(this).up('.form-group').down('.form-control').show();
							}
						})
					})
				})
			})
			;
		tmp.me
			._getInputDiv('name', (tmp.me._item.name || ''), $(tmp.me._containerIds.name), null ,true)
			._getInputDiv('description', (tmp.me._item.description || ''), $(tmp.me._containerIds.description))
			._getInputDiv('unitPrice', tmp.me.getCurrency((tmp.me._item.unitPrice || 0), ''), $(tmp.me._containerIds.unitPrice), 'Unit Price ($)',true, undefined, true)
			._getInputDiv('barcode',(tmp.me._item.barcode || ''), $(tmp.me._containerIds.barcode), 'Barcode', true)
			._getInputDiv('size',(tmp.me._item.size || ''), $(tmp.me._containerIds.size))
			._getInputDiv('labelVersionNo',(tmp.me._item.labelVersionNo || ''), $(tmp.me._containerIds.labelVersion), 'Label Version',true)
			._getInputDiv('useByVariance',(tmp.me._item.usedByVariance || ''), $(tmp.me._containerIds.useByVariance), 'Days By From Printed',true)
			._getSelect2Div('Material', 'materials', tmp.me._item.id ? tmp.me._item.materials : [], $(tmp.me._containerIds.materials), null)
			._getSelect2Div('Category', 'categories', tmp.me._item.id ? tmp.me._item.categories : [], $(tmp.me._containerIds.categories), null)
			._getSelect2Div('Store', 'stores', tmp.me._item.id ? tmp.me._item.stores : [], $(tmp.me._containerIds.stores), tmp.storeTitle )
			._getSaveBtn()
		;
		if(tmp.sellinAllStore === true) {
			$(tmp.me._containerIds.stores).down('.form-control').hide();
		}
		
		tmp.me._addNewComboBtn($(tmp.me._containerIds.materials), 'material');

		if(tmp.me._item.materials && Array.isArray(tmp.me._item.materials) && tmp.me._item.materials.length > 0) {
			tmp.me._item.materials.each(function(item){
				tmp.me._addComboRow(item, $(tmp.me._containerIds.materials), 'material');
			});
			if($$('.newStoreBtn') && $$('.newStoreBtn').length > 0)
				$$('.newStoreBtn').first().replace(new Element('label').update('Material'));
		}
		
		return tmp.me;
	}
	,_addNewComboBtn: function(container, entityName) {
		var tmp = {};
		tmp.me = this;
		tmp.container = (container || null);
		tmp.entityName = 'New ' + (entityName ? tmp.me.ucfirst(entityName) : 'Info');
		if(!tmp.container || !tmp.container.id)
			return tmp.me;
		tmp.newBtn = new Element('button', {'class': 'newStoreBtn btn btn-primary btn-sm'})
			.update(tmp.entityName)
			.observe('click', function(e){
				tmp.newBtn.writeAttribute('disabled', true);
				tmp.container.insert({'bottom': tmp.newDiv = new Element('div')});
				tmp.me._signRandID(tmp.newDiv);
				tmp.me._addComboRow(null, tmp.newDiv, null, (!tmp.me._item || !tmp.me._item.id));
				tmp.newBtn.writeAttribute('disabled', false);
				if(!tmp.me._item || !tmp.me._item.id)
					tmp.newBtn.replace(new Element('label').update('Material'));
			})
			;
	
		tmp.container.update(tmp.me._getFormGroup('', tmp.newBtn).addClassName('col-xs-12'));
		if(!tmp.me._item || !tmp.me._item.id)
			tmp.newBtn.click();
		return tmp.me;
	}
	,_getComboRowDeleteBtn: function(combo, className, entityName) {
		var tmp = {};
		tmp.me = this;
		tmp.combo = (combo || null);
		tmp.active = (combo ? combo.active === true : true);
		tmp.className = (className || '');
		tmp.entityName = (entityName || 'Info');
	
		tmp.deleteBtn = new Element('button', {'class': (tmp.active === true ? 'btn btn-sm btn-danger' : 'btn btn-sm btn-success') })
			.addClassName(tmp.className)
			.setStyle('margin-bottom: 15px;')
			.insert({'bottom': new Element('i', {'class': (tmp.active === true ? 'glyphicon glyphicon-trash' : 'glyphicon glyphicon-repeat')}) })
			.observe('click', function(e){
				if(confirm('This ' + (tmp.combo ? '' : 'newly added ') + tmp.entityName + ' will be REMOVED, continue?')) {
					tmp.panel = tmp.deleteBtn.up('.combo');
					if(tmp.combo && tmp.combo.id) {
						tmp.panel.up().insert({'bottom': new Element('input', {'type': 'hidden', 'save-item': 'ignore_' + tmp.combo.id, 'dirty': true}) });
					}
					tmp.panel.remove();
					tmp.me._refreshDirty()._getSaveBtn();
				}
			})
			;
		return tmp.deleteBtn;
	}
	,_addComboRow: function(combo, container, entityName, noDeleteBtn) {
		var tmp = {};
		tmp.me = this;
		tmp.combo = (combo || null);
		tmp.container = (container || null);
		tmp.noDeleteBtn = (noDeleteBtn || false);
		if(!tmp.container || !tmp.container.id)
			return tmp.me;
		tmp.container
			.insert({'bottom': new Element('div', {'class': 'combo col-xs-12', 'combo_id': (tmp.combo ? tmp.combo.id : 'new'), 'active': (tmp.combo ? tmp.combo.active : true) })
				.insert({'bottom': new Element('div', {'class': 'row '}).addClassName((tmp.combo && (!tmp.combo.active || !tmp.combo.material.active)) ? 'row-danger' : '')
					.insert({'bottom': tmp.material = new Element('div', {'class': 'store col-md-7 col-sm-6 col-xs-12'}) })
					.insert({'bottom': tmp.qty = new Element('div', {'class': 'role col-md-4 col-sm-5  col-xs-12'}) })
					.insert({'bottom': new Element('div', {'class': 'pull-right text-right col-md-1 col-sm-1 col-xs-12'}).update(tmp.noDeleteBtn === true ? '' : tmp.me._getComboRowDeleteBtn(tmp.combo, 'col-xs-12', entityName)) })
				})
			});
	
		tmp.materialSelect2Options = {
			multiple: false,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'Material', 'pageNo': 1};
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
			._getSelect2Div('Material', 'material', ((tmp.combo && tmp.combo.material) ? {'id': tmp.combo.material.id, 'text': tmp.combo.material.name, 'data': tmp.combo.material} : null), tmp.material, ' ', true, tmp.materialSelect2Options)
			._getInputDiv('qty', ((tmp.combo && tmp.combo.material) ? tmp.combo.qty : 1), tmp.qty, ' ' , false, '', false, 'Qty')
		;
		return tmp.me;
	}
	,collectData: function() {
		var tmp = {};
		tmp.me = this;
		tmp.data = tmp.me._collectFormData($(tmp.me.getHTMLID('itemDiv')), 'save-item');
		if(!tmp.data)
			return null;
		if(!tmp.data.barcode || typeof(tmp.data.barcode) !== 'string' || (parseInt(tmp.data.barcode).toString().length !== 12 && parseInt(tmp.data.barcode).toString().length !== 13)) {
			tmp.me.showModalBox('ERROR:', 'Error: The barcode needs to be 12 or 13 <b>digits</b> long.');
			return null;
		}
		if(tmp.data.allStores === false && (!tmp.data.stores || tmp.data.stores.split(',').length === 0) ) {
			tmp.me.showModalBox('ERROR:', 'Error: You need at least one store for this product!.');
			return null;
		}
		
		tmp.data['materials'] = [];
		$(tmp.me.getHTMLID('itemDiv')).getElementsBySelector('.combo[combo_id]').each(function(item){
			tmp.combo = tmp.me._collectFormData($(item), 'save-item');
			if(tmp.combo)
				tmp.data['materials'].push(tmp.combo);
		});
		
		if(tmp.data.materials.length == 0) {
			tmp.me.showModalBox('ERROR:', 'Error: At lease one valid material is required for a product.');
			return null;
		}
	
		return tmp.data;
	}
});