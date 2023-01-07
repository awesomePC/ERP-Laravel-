<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\DocumentShareController@update', [$id]), 'id' => 'share_document_form', 'method' => 'put']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title text-center" id="exampleModalLabel">
        @if(!empty($type))
          @lang('essentials::lang.share_memos')
        @else
          @lang('essentials::lang.share_document')
        @endif
      </h4>
    </div>
    <div class="modal-body">
      
        <input type="hidden" name="document_id" id="document_id" value="{{$id}}">
        <div class="form-group">
            {!! Form::label('user', __('essentials::lang.user').':') !!} <br>
            {!! Form::select('user[]', $users, $shared_user, ['class' => 'form-control select2', 'multiple' => 'multiple', 'style'=>"width: 50%; height:50%"]); !!}
        </div>
        <div class="form-group">
            {!! Form::label('role', __('essentials::lang.role').':') !!} <br>
            {!! Form::select('role[]', $roles, $shared_role, ['class' => 'form-control select2', 'multiple' => 'multiple', 'style'=>"width: 50%; height:50%"]); !!}
        </div>

    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary pull-right ladda-button doc-share-btn" data-style="expand-right">
          <span class="ladda-label">@lang('messages.update')</span>
      </button>
    </div>
  </div>
  {!! Form::close() !!}
</div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    __select2($('.select2'));
  })
</script>