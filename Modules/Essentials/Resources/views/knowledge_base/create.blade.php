@extends('layouts.app')

@php
    $page_title = __( 'essentials::lang.add_knowledge_base' );
    $kb_type = '';
    if(!empty($parent)) {
        $kb_type = $parent->kb_type == 'knowledge_base' ? 'section' : 'article';
    }
    if($kb_type == 'section') {
        $page_title = __( 'essentials::lang.add_section' );
    } else if($kb_type == 'article') {
        $page_title = __( 'essentials::lang.add_article' );
    }
@endphp
@section('title', $page_title)

@section('content')
@include('essentials::layouts.nav_essentials')

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@store'), 'method' => 'post' ]) !!}
    @if(!empty($parent))
        {!! Form::hidden('kb_type', $kb_type); !!}
        {!! Form::hidden('parent_id', $parent->id); !!}
    @endif
    @component('components.widget', ['title' => $page_title])
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('title', __( 'essentials::lang.title' ) . ':*') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.title' ), 'required' ]); !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('content', __( 'essentials::lang.content' ) . ':') !!}
                    {!! Form::textarea('content', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.content' ) ]); !!}
                </div>
            </div>
            @if(empty($parent))
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('share_with', __( 'essentials::lang.share_with' ) . ':') !!}
                        {!! Form::select('share_with', ['public' => __('essentials::lang.public'), 'private' => __( 'essentials::lang.private' ), 'only_with' => __( 'essentials::lang.only_with' )], 'public', ['class' => 'form-control select2' ]); !!}
                    </div>
                </div>
                <div class="col-md-6" id="user_ids_div" style="display: none;">
                    <div class="form-group">
                        {!! Form::label('user_ids', __( 'essentials::lang.share_only_with' ) . ':') !!}
                        {!! Form::select('user_ids[]', $users, null, ['class' => 'form-control select2', 'multiple', 'id' => 'user_ids', 'style' => 'width: 100%;' ]); !!}
                    </div>
                </div>
            @endif
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.save' )</button>
            </div>
        </div>
    @endcomponent
{!! Form::close() !!}
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        init_tinymce('content');

        $('#share_with').change( function() {
            if ($(this).val() == 'only_with') {
                $('#user_ids_div').fadeIn();
            } else {
                $('#user_ids_div').fadeOut();
            }
        });
    });
</script>
@endsection
