@extends('layouts.app')

@section('title', __('essentials::lang.knowledge_base'))

@section('content')
@include('essentials::layouts.nav_essentials')
	<section class="content">
		<div class="box box-solid">
			<div class="box-header">
				<h4 class="box-title">@lang('essentials::lang.knowledge_base')</h4>
				<div class="box-tools pull-right">
					<a href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@create')}}" class="btn btn-sm btn-primary">
						<i class="fa fa-plus"></i> 
						@lang( 'messages.add' )
					</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
				@foreach($knowledge_bases as $kb)
					<div class="col-md-4">
						<div class="box box-solid" style="max-height: 500px; overflow-y: auto;">
							<div class="box-header">
								<h4 class="box-title">{{$kb->title}}</h4>
								<div class="box-tools pull-right">
									<a class="text-info p-5-5" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@show', [$kb->id])}}" title="@lang('messages.view')" data-toggle="tooltip"><i class="fas fa-eye"></i></a>

									<a class="text-primary p-5-5" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@edit', [$kb->id])}}" title="@lang('messages.edit')" data-toggle="tooltip"><i class="fas fa-edit"></i></a>

									<a class="text-danger p-5-5 delete-kb" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@destroy', [$kb->id])}}" title="@lang('messages.delete')" data-toggle="tooltip"><i class="fas fa-trash"></i></a>

									<a class="text-primary p-5-5" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@create')}}?parent={{$kb->id}}" title="@lang('essentials::lang.add_section')" data-toggle="tooltip"><i class="fas fa-plus"></i></a>
								</div>
							</div>
							<div class="box-body">
								{!! $kb->content !!}
								@if(count($kb->children) > 0)
									<div class="box-group" 
										id="accordian_{{$kb->id}}">
										@foreach($kb->children as $section)
											<div class="panel box box-solid">
												<div class="box-header with-border" style="padding: 10px 12px;">
													<h4 class="box-title">
														<a data-toggle="collapse" data-parent="#accordian_{{$kb->id}}" href="#collapse_{{$section->id}}" @if($loop->index == 0 )aria-expanded="true" @endif>{{$section->title}}
														</a>
													</h4>
													<div class="box-tools pull-right">
														<a class="text-info p-5-5" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@show', [$section->id])}}" title="@lang('messages.view')" data-toggle="tooltip"><i class="fas fa-eye"></i></a>

														<a class="text-primary p-5-5" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@edit', [$section->id])}}" title="@lang('messages.edit')" data-toggle="tooltip"><i class="fas fa-edit"></i></a>

														<a class="text-danger p-5-5 delete-kb" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@destroy', [$section->id])}}" title="@lang('messages.delete')" data-toggle="tooltip"><i class="fas fa-trash"></i></a>

														<a class="text-success p-5-5" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@create')}}?parent={{$section->id}}" title="@lang('essentials::lang.add_article')" data-toggle="tooltip"><i class="fas fa-plus"></i></a>
													</div>
												</div>
												<div id="collapse_{{$section->id}}" class="panel-collapse collapse @if($loop->index == 0 )in @endif" @if($loop->index == 0 )aria-expanded="true" @endif >
								                    <div class="box-body" style="padding: 10px 12px;">
								                		{!!$section->content!!}
								                		@if(count($section->children) > 0)
								                			<ul class="todo-list">
								                			@foreach($section->children as $article)
								                				<li><a class="text-primary" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@show', [$article->id])}}">{{$article->title}}
								                				</a>
								                				<div class="tools">
								                				<a class="text-primary p-5-5" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@edit', [$article->id])}}" title="@lang('messages.edit')" data-toggle="tooltip"><i class="fas fa-edit"></i></a>

																<a class="text-danger p-5-5 delete-kb" href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@destroy', [$article->id])}}" title="@lang('messages.delete')" data-toggle="tooltip"><i class="fas fa-trash"></i></a>
																</div>
								                				</li>
								                			@endforeach
								                			</ul>
								                		@endif
								                    </div>
								                </div>
											</div>
										@endforeach
									</div>
								@endif
							</div>
						</div>
					</div>
					@if($loop->iteration%3 == 0)
						<div class="clearfix"></div>
					@endif
				@endforeach
				</div>
			</div>
		</div>
	</section>
@endsection

@section('javascript')
<script type="text/javascript">
	$(document).ready( function(){
		$('.delete-kb').click(function(e){
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
	                            toastr.success(result.msg);
	                        } else {
	                            toastr.error(result.msg);
	                        }

	                        location.reload();
	                    },
	                });
	            }
	        });
		})
	});
</script>
@endsection