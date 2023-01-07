@extends('layouts.app')

@section('title', __('crm::lang.view_lead'))

@section('content')
@include('crm::layouts.nav')
<section class="content no-print">
    <div class="row no-print">
        <div class="col-md-4">
            <h3>@lang('crm::lang.view_lead')</h3>
        </div>
        <div class="col-md-4 col-xs-12 mt-15 pull-right">
            {!! Form::select('lead_id', $leads, $contact->id , ['class' => 'form-control select2', 'id' => 'lead_id']); !!}
        </div>
    </div><br>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body">
                    @include('crm::lead.partial.lead_info')
                </div>
            </div>
        </div>
    </div>
	<div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <li class="active">
                        <a href="#lead_schedule" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa fa-calendar-check"></i>
                            @lang('crm::lang.schedule')
                        </a>
                    </li>
                    <li>
                        <a href="#documents_and_notes" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa-file-image"></i>
                            @lang('crm::lang.documents_and_notes')
                        </a>
                    </li>

                    @if(!empty($contact_view_tabs))
                        @foreach($contact_view_tabs as $key => $tabs)
                            @foreach ($tabs as $index => $value)
                                @if(!empty($value['tab_menu_path']))
                                    @php
                                        $tab_data = !empty($value['tab_data']) ? $value['tab_data'] : [];
                                    @endphp
                                    @include($value['tab_menu_path'], $tab_data)
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="lead_schedule">
                        @include('crm::lead.partial.lead_schedule')
                    </div>
                    <!-- model id like project_id, user_id -->
                    <input type="hidden" name="notable_id" id="notable_id" value="{{$contact->id}}">
                    <!-- model name like App\User -->
                    <input type="hidden" name="notable_type" id="notable_type" value="App\Contact">
                    <div class="tab-pane document_note_body" id="documents_and_notes">
                    </div>

                    @if(!empty($contact_view_tabs))
                        @foreach($contact_view_tabs as $key => $tabs)
                            @foreach ($tabs as $index => $value)
                                @if(!empty($value['tab_content_path']))
                                    @php
                                        $tab_data = !empty($value['tab_data']) ? $value['tab_data'] : [];
                                    @endphp
                                    @include($value['tab_content_path'], $tab_data)
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade schedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_schedule" tabindex="-1" role="dialog"></div>
@endsection
@section('javascript')
	<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
    @includeIf('documents_and_notes.document_and_note_js')
    <script type="text/javascript">
        $(document).ready(function() {
            initializeLeadScheduleDatatable();
        });

        $('#lead_id').change( function() {
            if ($(this).val()) {
                window.location = "{{url('/crm/leads')}}/" + $(this).val();
            }
        });
    </script>
@endsection