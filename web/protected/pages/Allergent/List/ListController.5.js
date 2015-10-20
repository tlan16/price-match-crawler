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
		
		tmp.selectBox = jQuery('[search_field="ingr.allergents"]').select2({
			minimumInputLength: 1,
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
		return tmp.me;
	}
});