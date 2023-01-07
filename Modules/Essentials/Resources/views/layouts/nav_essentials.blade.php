<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action('\Modules\Essentials\Http\Controllers\ToDoController@index')}}"><i class="fas fa-check-circle"></i> {{__('essentials::lang.essentials')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li @if(request()->segment(2) == 'todo') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\ToDoController@index')}}">@lang('essentials::lang.todo')</a></li>

                    <li @if(request()->segment(2) == 'document' && request()->get('type') != 'memos') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\DocumentController@index')}}">@lang('essentials::lang.document')</a></li>

                    <li @if(request()->segment(2) == 'document' && request()->get('type') == 'memos') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\DocumentController@index') .'?type=memos'}}">@lang('essentials::lang.memos')</a></li>

                    <li @if(request()->segment(2) == 'reminder') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\ReminderController@index')}}">@lang('essentials::lang.reminders')</a></li>
                    @if (auth()->user()->can('essentials.view_message') || auth()->user()->can('essentials.create_message'))
                        <li @if(request()->segment(2) == 'messages') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsMessageController@index')}}">@lang('essentials::lang.messages')</a></li>
                    @endif
                    <li @if(request()->segment(2) == 'knowledge-base') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@index')}}">@lang('essentials::lang.knowledge_base')</a></li>
                    @if (auth()->user()->can('edit_essentials_settings'))
                        <li @if(request()->segment(2) == 'hrm' && request()->segment(2) == 'settings') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsSettingsController@edit')}}">@lang('business.settings')</a></li>
                    @endif
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>