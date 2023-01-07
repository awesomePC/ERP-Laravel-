@forelse($checklists as $key => $list)
    @php
        $is_yes_no = false;
    @endphp
    <div class="col-xs-4">
        <fieldset>
            <label style="color: #525f7f;">{{$list}}</label>
            <div class="switch-toggle switch-candy">

                <input id="{{$key}}_yes" name="repair_checklist[{{$list}}]" type="radio" value="yes"
                    @if(!empty($selected_checklist[$list]) && $selected_checklist[$list] == 'yes')
                        @php
                            $is_yes_no = true;
                        @endphp
                        checked
                    @endif
                >
                <label for="{{$key}}_yes" onclick="" style="color: #228B22;">
                   <span class="font-23"> &#10004;</span>
                </label>
                
                <input id="{{$key}}_no" name="repair_checklist[{{$list}}]" type="radio" value="no"
                    @if((!empty($selected_checklist[$list]) && $selected_checklist[$list] == 'no'))
                        @php
                            $is_yes_no = true;
                        @endphp
                        checked
                    @endif
                >
                <label for="{{$key}}_no" onclick="" style="color: #DC143C;">
                    <span class="font-23">&#10007;</span>
                </label>

                <input id="{{$key}}_not_applicable" name="repair_checklist[{{$list}}]" type="radio" value="not_applicable"
                    @if((!empty($selected_checklist[$list]) && $selected_checklist[$list] == 'not_applicable') || empty($selected_checklist) || !$is_yes_no)
                        checked
                    @endif
                >
                <label for="{{$key}}_not_applicable" onclick="" style="color: #efe3e6;">
                    <span class="font-17">@lang('repair::lang.not_applicable_key')</span>
                </label>

                <a class="btn btn-flat btn-success"></a>
            </div>
        </fieldset>
    </div>
@empty
    <div class="col-xs-4">
        @lang('repair::lang.no_repair_check_list')
    </div>
@endforelse