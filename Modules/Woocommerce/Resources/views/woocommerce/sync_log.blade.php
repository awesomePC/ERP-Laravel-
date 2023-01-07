@extends('layouts.app')
@section('title', __('woocommerce::lang.sync_log'))

@section('content')
@include('woocommerce::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'woocommerce::lang.sync_log' )
    </h1>
</section>

<!-- Main content -->
<section class="content">

	<div class="box box-solid">
        <div class="box-body">
        	<table class="table table-bordered table-striped" id="sync_log_table">
        		<thead>
        			<tr>
                        <th>&nbsp;</th>
        				<th>@lang( 'messages.date' )</th>
        				<th>@lang( 'woocommerce::lang.sync_type' )</th>
        				<th>@lang( 'woocommerce::lang.operation' )</th>
                        <th>@lang( 'woocommerce::lang.synced_by' )</th>
                        <th class="col-sm-5">@lang( 'woocommerce::lang.records' )</th>
        			</tr>
        		</thead>
        	</table>
        </div>
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function () {
        var sync_log_table =  $('#sync_log_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@viewSyncLog')}}",
            "order": [[ 1, "desc" ]],
            columnDefs: [ {
                "targets": 5,
                "orderable": false
            } ],
            columns: [
                {
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "defaultContent": ""
                },
                {data: 'created_at', name: 'woocommerce_sync_logs.created_at'},
                {data: 'sync_type', name: 'sync_type'},
                {data: 'operation_type', name: 'operation_type'},
                {data: 'full_name', name: 'full_name'},
                {data: 'data', name: 'woocommerce_sync_logs.data'},
            ],
            createdRow: function( row, data, dataIndex ) {
                if( data.log_details != ''){
                    $( row ).find('td:eq(0)').addClass('details-control');
                }
            },
        });

        // Array to track the ids of the details displayed rows
        var detailRows = [];
     
        $('#sync_log_table tbody').on( 'click', 'tr td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = sync_log_table.row( tr );
            var idx = $.inArray( tr.attr('id'), detailRows );
     
            if ( row.child.isShown() ) {
                tr.removeClass( 'details' );
                row.child.hide();
     
                // Remove from the 'open' array
                detailRows.splice( idx, 1 );
            }
            else {
                tr.addClass( 'details' );

                row.child( get_log_details( row.data() ) ).show();
     
                // Add to the 'open' array
                if ( idx === -1 ) {
                    detailRows.push( tr.attr('id') );
                }
            }
        } );
     
        // On each draw, loop over the `detailRows` array and show any child rows
        sync_log_table.on( 'draw', function () {
            $.each( detailRows, function ( i, id ) {
                $('#'+id+' td.details-control').trigger( 'click' );
            } );
        });
    });

    function get_log_details ( rowData ) {
        var div = $('<div/>')
            .addClass( 'loading' )
            .text( 'Loading...' );
        $.ajax( {
            url: '/woocommerce/get-log-details/' + rowData.DT_RowId,
            dataType: 'html',
            success: function ( data ) {
                div
                    .html( data )
                    .removeClass( 'loading' );
            }
        } );
     
        return div;
    }
</script>

@endsection
