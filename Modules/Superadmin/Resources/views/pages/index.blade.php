@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.frontend_pages'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.frontend_pages')</h1>
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-header">
            <h3 class="box-title">&nbsp;</h3>
        	<div class="box-tools">
                <a href="{{action('\Modules\Superadmin\Http\Controllers\PageController@create')}}" 
                    class="btn btn-block btn-primary">
                	<i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
            </div>
        </div>

        <div class="box-body">
        	<div class="row">
        		@foreach ($pages as $page)
	                <div class="col-md-4 page_details">
						<div class="box box-success hvr-grow-shadow">
							<div class="box-header with-border text-center">
								<h2 class="box-title">{{$page->title}}</h2>

								<div class="row">
									@if($page->is_shown == 1)
										<span class="badge bg-green">
											@lang('superadmin::lang.visible')
										</span>
									@else
										<span class="badge bg-red">
										@lang('superadmin::lang.hidden')
										</span>
									@endif

                                    <span class="badge bg-info" title="@lang('superadmin::lang.menu_order')">
                                        {{$page->menu_order}}
                                    </span>
									
									<a href="{{action('\Modules\Superadmin\Http\Controllers\PageController@edit', [$page->id])}}" class="btn btn-box-tool" title="edit"><i class="fa fa-edit"></i></a>
									<a href="{{action('\Modules\Superadmin\Http\Controllers\PageController@destroy', [$page->id])}}" class="btn btn-box-tool delete_page" title="delete"><i class="fa fa-trash"></i></a>
	              					
								</div>
							</div>
							<!-- /.box-header -->
							<div class="box-body" style="max-height: 300px; overflow: hidden;">
								<strong>@lang('superadmin::lang.page_content'):</strong>
								{!! $page->content !!}
							</div>
						</div>
						<!-- /.box -->
	                </div>
	                @if($loop->iteration%3 == 0)
	    				<div class="clearfix"></div>
	    			@endif
	            @endforeach
        	</div>
        </div>

    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
	$(document).on('click', 'a.delete_page', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            page_details.remove();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
</script>
@endsection