@extends('layouts.app')

@section('title', __('essentials::lang.knowledge_base'))

@section('content')
@include('essentials::layouts.nav_essentials')
	<section class="content">
		<div class="row">
			<div class="col-md-3">
				@include('essentials::knowledge_base.sidebar', ['knowledge_base' => $knowledge_base, 'current_id' => $kb_object->id, 'article_id' => $article_id, 'section_id' => $section_id])
			</div>
			<div class="col-md-9">
				<div class="box box-solid">
					<div class="box-header">
						<h4 class="box-title">{{$kb_object->title}}</h4>
						@if(!empty($kb_object->share_with))
						<br>
							<small><b>@lang('essentials::lang.share_with'):</b> @lang('essentials::lang.' . $kb_object->share_with) @if($kb_object->share_with == 'only_with') ({{implode(', ', $users)}}) @endif</small>
						@endif
					</div>
					<div class="box-body">
						{!! $kb_object->content !!}
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('javascript')
<script type="text/javascript">
	$(document).ready( function(){

	});
</script>
@endsection