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
	,_getCommentsDiv() {
		var tmp = {};
		tmp.me = this;

		tmp.container = $(tmp.me._containerIds.comments);
		
		tmp.comments = new Element('div');
		
		tmp.container.insert({'bottom': tmp.me._getFormGroup('Comments', tmp.comments, true).addClassName('col-md-12') });
		
		tmp.me._signRandID(tmp.comments);
		
		new CommentsDivJs(tmp.me, 'Question', tmp.me._item.id)._setDisplayDivId(tmp.comments.id).render();
		
		return tmp.me;
	}
	,load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.printedByOptions = {
				minimumInputLength: 1,
				allowClear: true,
				width: "100%",
				ajax: {
					delay: 250
					,url: '/ajax/getAll'
					,type: 'GET'
					,data: function (params) {
						return {"searchTxt": 'username like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'UserAccount', 'pageNo': 1};
					}
					,results: function(data, page, query) {
						tmp.result = [];
						if(data.resultData && data.resultData.items) {
							data.resultData.items.each(function(item){
								tmp.result.push({'id': item.id, 'text': item.username, 'data': item});
							});
						}
						return { 'results' : tmp.result };
					}
				}
				,cache: true
				,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
			};
		tmp.productOptions = {
				minimumInputLength: 1,
				allowClear: true,
				width: "100%",
				ajax: {
					delay: 250
					,url: '/ajax/getAll'
					,type: 'GET'
					,data: function (params) {
						return {"searchTxt": 'barcode like ? or name like ?', 'searchParams': ['%' + params + '%', '%' + params + '%'], 'entityName': 'Product', 'pageNo': 1};
					}
					,results: function(data, page, query) {
						tmp.result = [];
						if(data.resultData && data.resultData.items) {
							data.resultData.items.each(function(item){
								tmp.result.push({'id': item.id, 'text': item.username, 'data': item});
							});
						}
						return { 'results' : tmp.result };
					}
				}
				,cache: true
				,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
			};
		tmp.me
			._getInputDiv('name', (tmp.me._item.name || ''), $(tmp.me._containerIds.name), null ,true)
			._getDatePickerDiv('printed', (tmp.me._item.printedDate || ''), $(tmp.me._containerIds.printed), null, false, 'DD/MM/YYYY hh:mm:ss')
			._getSelect2Div('UserAccount', 'printedById', (tmp.me._item.printedBy || 0), $(tmp.me._containerIds.printedBy), 'Printed By', false, tmp.printedByOptions)
			._getDatePickerDiv('useByDate', (tmp.me._item.useByDate || ''), $(tmp.me._containerIds.expiry), 'Expiry')
			._getInputDiv('versionNo', (tmp.me._item.versionNo || ''), $(tmp.me._containerIds.version), 'Version')
			._getInputDiv('printedPrice', (tmp.me._item.printedPrice || ''), $(tmp.me._containerIds.price), 'Price', false, null, true)
			._getSelect2Div('Product', 'productId', (tmp.me._item.product ? tmp.me._item.product : null), $(tmp.me._containerIds.product), 'Product', false, tmp.productOptions)
			._getSaveBtn()
		;
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