<script type="text/javascript">
	$(document).ready( function () {

		$('#production_list_filter_date_range').daterangepicker(
        dateRangeSettings,
	        function (start, end) {
	            $('#production_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
	            productions_table.ajax.reload();
	        }
	    );
	    $('#production_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
	        $('#production_list_filter_date_range').val('');
	        productions_table.ajax.reload();
	    });
		//Purchase table
	    productions_table = $('#productions_table').DataTable({
	        processing: true,
	        serverSide: true,
	        aaSorting: [[0, 'desc']],
	        ajax: {
	            url: '{{action("\Modules\Manufacturing\Http\Controllers\ProductionController@index")}}',
	            "data": function ( d ) {
	                if($('#production_list_filter_date_range').val()) {
	                    var start = $('#production_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
	                    var end = $('#production_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
	                    d.start_date = start;
	                    d.end_date = end;
	                }
	                d.location_id = $('#productstion_list_filter_location_id').val();
	                if($('#production_list_is_final').is(':checked')) {
                        d.is_final = 1;
                    }
	                d = __datatable_ajax_callback(d);
	            },
	        },
	        columnDefs: [
	            {
	                targets: [6],
	                orderable: false,
	                searchable: false,
	            },
	        ],
	        columns: [
	            { data: 'transaction_date', name: 'transaction_date' },
	            { data: 'ref_no', name: 'ref_no' },
	            { data: 'location_name', name: 'bl.name' },
	            { data: 'product_name', name: 'product_name' },
	            { data: 'quantity', searchable: false },
	            { data: 'final_total', name: 'final_total' },
	            { data: 'action', name: 'action' },
	        ],
	        fnDrawCallback: function(oSettings) {
	            __currency_convert_recursively($('#productions_table'));
	        }
	    });

	    $(document).on('change', '#production_list_filter_date_range, #productstion_list_filter_location_id',  function() {
        	productions_table.ajax.reload();
    	});
    	$('#production_list_is_final').on('ifChanged', function(event){
            productions_table.ajax.reload();
        });

	    if ($('textarea#instructions').length > 0) {
            tinymce.init({
                selector: 'textarea#instructions',
            });
	    }

		if ($('#search_product').length) {
			initialize_search($('#search_product'));
	    }
	    if ($('.search_product').length) {
	    	$('.search_product').each( function(){
	    		initialize_search($(this));
	    	});
	    }

    	recipe_table = $('#recipe_table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: '{{action("\Modules\Manufacturing\Http\Controllers\RecipeController@index")}}',
	        columnDefs: [
	            {
	                targets: [0, 5, 6, 7],
	                orderable: false,
	                searchable: false,
	            },
	        ],
	        "order": [[ 1, "desc" ]],
	        columns: [
	        	{ data: 'row_select' },
	            { data: 'recipe_name', name: 'recipe_name' },
	            { data: 'category', name: 'c.name' },
	            { data: 'sub_category', name: 'sc.name' },
	            { data: 'total_quantity', name: 'total_quantity' },
	            { data: 'recipe_total' },
	            { data: 'unit_cost' },
	            { data: 'action', name: 'action' },
	        ],
	        fnDrawCallback: function(oSettings) {
	            __currency_convert_recursively($('#recipe_table'));
	        },
	    });
	});

	$(document).on('shown.bs.modal', '#recipe_modal', function(){
		initSelect2($(this).find('#variation_id'), $('#recipe_modal'));
        $(this).find('#copy_recipe_id').select2();
	});

	$(document).on('shown.bs.modal', '.view_modal', function(){
		__currency_convert_recursively($('.view_modal'));
	});

	$(document).on('change', '.quantity, .row_sub_unit_id, #total_quantity, #extra_cost, #sub_unit_id, #production_cost_type', function(){
		calculateRecipeTotal();
	});

    function addIngredientRow(variation_id, search_element) {
    	var row_index = parseInt($('#row_index').val());
    	var ingredient_group = search_element.closest('.box').find('.ingredient_group');
        var row_ig_index = ingredient_group.length ? ingredient_group.data('ig_index') : '';
        var sort_order = ++(search_element.closest('.box').find('.ingredient-row-sortable').children().length);

    	$.ajax({
            url: "/manufacturing/get-ingredient-row/" + variation_id + '?row_index=' + row_index + '&row_ig_index=' + row_ig_index + '&sort_order=' + sort_order,
            dataType: 'html',
            success: function(result) {
                search_element.closest('.box').find('table.ingredients_table tbody').append(result);
                calculateRecipeTotal();
                row_index++;
                $('#row_index').val(row_index);
            },
        });
    }

    function calculateRecipeTotal() {
    	var total = 0;
    	$('.ingredients_table tbody tr').each( function() {
    		var line_unit_price = $(this).find('.ingredient_price').val();
    		var quantity = __read_number($(this).find('.quantity'));
    		var multiplier = 1;
    		if ($(this).find('.row_sub_unit_id').length) {
    			multiplier = parseFloat(
		            $(this).find('.row_sub_unit_id')
		                .find(':selected')
		                .data('multiplier')
		        	);
    		}

    		var line_total = line_unit_price * quantity * multiplier;
    		$(this).find('span.ingredient_price').text(__currency_trans_from_en(line_total, true));
    		total += line_total;
    	});
    	$('span#ingredients_cost_text').text(__currency_trans_from_en(total, true));
    	$('#ingredients_cost').val(total);
    	var production_cost = __read_number($('#extra_cost'));
        var production_cost_type = $('#production_cost_type').val();
        if (production_cost_type == 'percentage') {
    	   production_cost = __calculate_amount('percentage', production_cost, total);
        } else if(production_cost_type == 'per_unit') {
            var total_quantity = __read_number($('#total_quantity'));
            production_cost = total_quantity * production_cost;
        }

        $('span#total_production_cost').text(__currency_trans_from_en(production_cost, true));
        
		total += production_cost;
    	__write_number($('#total'), total);
    }

	function initSelect2(element, dropdownParent = $('body')) {
		element.select2({
	        ajax: {
	            url: '/products/list',
	            dataType: 'json',
	            delay: 250,
	            data: function(params) {
	                return {
	                    term: params.term, // search term
	                };
	            },
	            processResults: function(data) {
	            	return {
			            results: $.map(data, function (value, key) {
			            	var name = value.type == 'variable' ? value.name + ' - ' + value.variation : value.name;
			            	name += ' (' + value.sub_sku + ')';
			                return {
			                    id: value.variation_id,
			                    text: name
			                }
			            })
			        };
	            },
	        },
	        minimumInputLength: 1,
	        escapeMarkup: function(markup) {
	            return markup;
	        },
	        dropdownParent: dropdownParent
	    });
	}

	$(document).on('click', 'button.remove_ingredient', function() {
        
        element = $(this).closest('tbody.ingredient-row-sortable');

		$(this).closest('tr').remove();

        //set the order of ingredient
        $(element).children().each(function(index) {
            $(this).find('input.sort_order').val(++index)
        });

		calculateRecipeTotal();
	});
    
	$(document).on('submit', '#recipe_form', function (e) {
		var ingredients_length = $('.ingredients_table tbody .quantity').length;
		if (ingredients_length < 1) {
			toastr.error('@lang("manufacturing::lang.please_add_ingredients")');
			e.preventDefault();
			return false;
		}
	});

	$(document).on('click', 'button#add_ingredient_group', function() {
		var ig_index = parseInt($('#ig_index').val());
    	$.ajax({
            url: "/manufacturing/ingredient-group-form" + '?ig_index=' + ig_index,
            dataType: 'html',
            success: function(result) {
            	var el = $(result);
                $('#box_group').append(el);
                initialize_search(el.find('.search_product'));
                el.find('.ingredient_group').focus();
                ig_index++;
                $('#ig_index').val(ig_index);
            },
        });
	});

	function initialize_search(element) {
		element.autocomplete({
            source: function(request, response) {
                $.getJSON(
                    '/products/list',
                    {
                        term: request.term,
                        product_types: ['single', 'variable']
                    },
                    response
                );
            },
            minLength: 2,
            response: function(event, ui) {
                if (ui.content.length == 0) {
                    toastr.error(LANG.no_products_found);
                    $('input#search_product').select();
                }
            },
            select: function(event, ui) {
                addIngredientRow(ui.item.variation_id, $(this));
            },
        }).autocomplete('instance')._renderItem = function(ul, item) {
	        var string = '<li>' + item.name;
            if (item.type == 'variable') {
                string += '-' + item.variation;
            }
            string +=
                ' (' +
                item.sub_sku +
                ')' +
                '</li>';
            return $(string).appendTo(ul);
        }
	}
	$(document).on('click', 'button.remove_ingredient_group', function() {
	$(this).closest('.box').remove();
	calculateRecipeTotal();
});

$(document).on('click', '#mass_update_product_price', function(e){
    e.preventDefault();
    var selected_rows = [];
    var unit_prices = [];
    var i = 0;
    $('.row-select:checked').each(function () {
    	var recipe_id = $(this).val();
        selected_rows[i++] = recipe_id;
        unit_prices[recipe_id] = $(this).closest('tr').find('span.unit_cost').data('unit_cost');
    });
    
    if(selected_rows.length > 0){
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var data = {
                	recipe_ids: selected_rows,
                	unit_prices: unit_prices
                }
                $.ajax({
                    method: "post",
                    url: "/manufacturing/update-product-prices",
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            recipe_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    } else{
        swal('@lang("lang_v1.no_row_selected")');
    }    
});
$(document).on('click', 'button.delete_recipe', function() {
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            var href = $(this).data('href');
            var data = $(this).serialize();
            $.ajax({
                method: 'DELETE',
                url: href,
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        recipe_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        }
    });
});

$(document).on('click', '.delete-production', function(e) {
	e.preventDefault();
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            var href = $(this).data('href');
            var data = $(this).serialize();
            $.ajax({
                method: 'DELETE',
                url: href,
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        productions_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        }
    });
});

$(document).on('change', '#choose_product_form #variation_id', function() {
    var variation_id = $(this).val();
    if (variation_id) {
        $.ajax({
            method: 'get',
            url: "/manufacturing/is-recipe-exist/" + variation_id,
            dataType: 'json',
            success: function(result) {
                if (result == 1) {
                    $('#choose_product_form #recipe_selection').addClass('hide');
                } else {
                    $('#choose_product_form #recipe_selection').removeClass('hide');
                }
            },
        });
    } else {
        $('#choose_product_form #recipe_selection').removeClass('hide');
    }
})
</script>