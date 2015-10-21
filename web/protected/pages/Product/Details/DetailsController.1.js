/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_readOnlyMode: false
	,_selectTypeTxt: 'Select One...'
	/**
	 * Set some pre defined data before javascript start
	 */
	,setPreData: function() {
		return this;
	}
	,load: function () {
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
		return tmp.me;
	}
	/**
	 * Public: binding all the js events
	 */
	,bindAllEventNObjects: function() {
		var tmp = {};
		tmp.me = this;
		return tmp.me;
	}
});