@extends('layouts.app')

@php
    $page_title = __( 'essentials::lang.edit_knowledge_base' );
    $kb_type = '';
    if($kb->kb_type == 'section') {
        $page_title = __( 'essentials::lang.edit_section' );
    } else if($kb->kb_type == 'article') {
        $page_title = __( 'essentials::lang.edit_article' );
    }
@endphp
@section('title', $page_title)

@section('content')
@include('essentials::layouts.nav_essentials')

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@update', [$kb->id]), 'method' => 'put' ]) !!}
    @component('components.widget', ['title' => $page_title])
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('title', __( 'essentials::lang.title' ) . ':*') !!}
                    {!! Form::text('title', $kb->title, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.title' ), 'required' ]); !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('content', __( 'essentials::lang.content' ) . ':') !!}
                    {!! Form::textarea('content', $kb->content, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.content' ) ]); !!}
                </div>
            </div>
            @if($kb->kb_type == 'knowledge_base')
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('share_with', __( 'essentials::lang.share_with' ) . ':') !!}
                        {!! Form::select('share_with', ['public' => __('essentials::lang.public'), 'private' => __( 'essentials::lang.private' ), 'only_with' => __( 'essentials::lang.only_with' )], $kb->share_with, ['class' => 'form-control select2' ]); !!}
                    </div>
                </div>
                <div class="col-md-6" id="user_ids_div" @if($kb->share_with != 'only_with')style="display: none;" @endif>
                    <div class="form-group">
                        {!! Form::label('user_ids', __( 'essentials::lang.share_only_with' ) . ':') !!}
                        {!! Form::select('user_ids[]', $users, $kb->users->pluck('id')->toArray(), ['class' => 'form-control select2', 'multiple', 'id' => 'user_ids', 'style' => 'width: 100%;' ]); !!}
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
