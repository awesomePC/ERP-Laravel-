<div class="row">
	<div class="col-md-8">
		@include('contact.partials.contact_info_tab', ['reward_enabled' => 0])
	</div>
	<div class="col-md-4 mt-56">
        @if(!empty($contact->Source))
    		<strong><i class="fas fa fa-search"></i>
                @lang('crm::lang.source')
            </strong>
            <p>{{$contact->Source->name}}</p>
        @endif
        @if(!empty($contact->lifeStage))
            <strong><i class="fas fa fa-life-ring"></i>
                @lang('crm::lang.life_stage')
            </strong> 
            <p>{{$contact->lifeStage->name}}</p>
        @endif

        <strong><i class="fas fa-users"></i>
            @lang('crm::lang.assgined')
        </strong> <br>
        <p>
            @includeIf('components.avatar', ['max_count' => '10', 'members' => $contact->leadUsers])
        </p>
	</div>
</div>