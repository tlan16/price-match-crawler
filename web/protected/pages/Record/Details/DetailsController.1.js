/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		console.log(tmp.me._item);
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me
			._getInputDiv('sku', (tmp.me._item.product.sku || ''), $(tmp.me._containerIds.sku), null ,true)
			._getInputDiv('description', (tmp.me._item.product.description || ''), $(tmp.me._containerIds.description))
			._getInputDiv('vendor', (tmp.me._item.vendor.name || ''), $(tmp.me._containerIds.vendor))
			//._getSaveBtn()
		;
		tmp.me.disableInput(tmp.me._containerIds.sku)
			.disableInput(tmp.me._containerIds.description)
			.disableInput(tmp.me._containerIds.vendor)
		;
		return tmp.me;
	}
	,disableInput: function(elId)
	{
		jQuery('#' + elId + ' input').prop('disabled', true);
		return this;
	}
});