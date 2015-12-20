/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
    _getTitleRowData: function() {
        this._titleRowData.vendor = {'name': 'Vendor'};
        return this._titleRowData;
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
            .insert({'bottom': new Element(tmp.tag, {'class': 'sku col-sm-4 col-xs-12'}).update(row.product.sku) })
            .insert({'bottom': new Element(tmp.tag, {'class': 'vendor col-sm-3 col-xs-12'}).update(row.vendor.name) })
            .insert({'bottom': new Element(tmp.tag, {'class': 'price col-sm-3 col-xs-12'}).update(tmp.isTitle ? row.price :
                new Element('a', {'href': row.url, 'target': '_blank'}).update(accounting.formatMoney(row.price))
            ) })
            .insert({'bottom': new Element(tmp.tag, {'class': 'text-right btns col-sm-2 col-xs-12'}).update(
                tmp.isTitle === true ?
                    (new Element('span', {'class': 'btn btn-primary btn-xs', 'title': 'New'})
                            .insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-plus'}) })
                            .insert({'bottom': ' NEW' })
                            .observe('click', function(){
                                tmp.me._openDetailsPage();
                            })
                            .hide()
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
            'modal'			: false,
            'autoScale'     : false,
            'autoDimensions': false,
            'fitToView'     : false,
            'autoSize'      : false,
            'type'			: 'iframe',
            'href'			: '/' + tmp.me._focusEntity.toLowerCase() + '/' + (row ? row.id : 'new') + '.html',
            'helpers'		: {
                'overlay': {
                    'locked': false
                }
            }
        });
        return tmp.me;
    }
});